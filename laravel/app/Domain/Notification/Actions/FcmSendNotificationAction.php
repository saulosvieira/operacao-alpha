<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\DTOs\NotificationData;
use App\Domain\Notification\Models\FcmToken;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class FcmSendNotificationAction
{
    private Client $httpClient;

    public function __construct(?Client $httpClient = null)
    {
        $this->httpClient = $httpClient ?? new Client();
    }

    /**
     * Send a notification to all FCM tokens for a specific user.
     */
    public function execute(string $userId, NotificationData $notification): array
    {
        $tokens = FcmToken::where('user_id', $userId)->get();

        if ($tokens->isEmpty()) {
            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'total' => 0,
            ];
        }

        return $this->sendToTokens($tokens, $notification);
    }

    /**
     * Send a notification to all registered FCM tokens.
     */
    public function sendToAll(NotificationData $notification): array
    {
        $tokens = FcmToken::all();

        if ($tokens->isEmpty()) {
            return [
                'success' => false,
                'sent' => 0,
                'failed' => 0,
                'total' => 0,
            ];
        }

        return $this->sendToTokens($tokens, $notification);
    }

    /**
     * Send a notification to a collection of FCM tokens.
     */
    private function sendToTokens($tokens, NotificationData $notification): array
    {
        $accessToken = $this->getAccessToken();

        if ($accessToken === null) {
            Log::error('FCM: Failed to obtain access token from service account');

            return [
                'success' => false,
                'sent' => 0,
                'failed' => $tokens->count(),
                'total' => $tokens->count(),
            ];
        }

        $projectId = config('services.firebase.project_id');
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $sent = 0;
        $failed = 0;

        foreach ($tokens as $fcmToken) {
            try {
                $payload = $this->buildPayload($fcmToken->token, $notification);

                $this->httpClient->post($url, [
                    'headers' => [
                        'Authorization' => "Bearer {$accessToken}",
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $payload,
                ]);

                $sent++;
            } catch (RequestException $e) {
                $failed++;
                $this->handleSendError($e, $fcmToken);
            } catch (\Exception $e) {
                $failed++;
                Log::error('FCM: Unexpected error sending notification', [
                    'error' => $e->getMessage(),
                    'token_id' => $fcmToken->id,
                ]);
            }
        }

        return [
            'success' => $sent > 0,
            'sent' => $sent,
            'failed' => $failed,
            'total' => $tokens->count(),
        ];
    }

    /**
     * Build the FCM v1 API data message payload.
     *
     * Uses data messages (not notification messages) for full control
     * over display in the Android app.
     */
    private function buildPayload(string $token, NotificationData $notification): array
    {
        $data = [
            'title' => $notification->title,
            'body' => $notification->body,
        ];

        if ($notification->url !== null) {
            $data['url'] = $notification->url;
        }

        return [
            'message' => [
                'token' => $token,
                'data' => $data,
            ],
        ];
    }

    /**
     * Handle errors from the FCM API.
     *
     * Automatically removes tokens that are no longer valid (UNREGISTERED).
     */
    private function handleSendError(RequestException $e, FcmToken $fcmToken): void
    {
        $response = $e->getResponse();

        if ($response === null) {
            Log::error('FCM: Network error sending notification', [
                'error' => $e->getMessage(),
                'token_id' => $fcmToken->id,
            ]);

            return;
        }

        $statusCode = $response->getStatusCode();
        $body = json_decode((string) $response->getBody(), true);

        $errorCode = $body['error']['details'][0]['errorCode'] ?? ($body['error']['status'] ?? null);

        // Check for UNREGISTERED error — token is invalid or expired
        if ($errorCode === 'UNREGISTERED' || $this->isUnregisteredError($body)) {
            Log::info('FCM: Removing unregistered token', [
                'token_id' => $fcmToken->id,
                'device_id' => $fcmToken->device_id,
            ]);

            $fcmToken->delete();

            return;
        }

        Log::error('FCM: API error sending notification', [
            'status_code' => $statusCode,
            'error' => $body['error'] ?? 'Unknown error',
            'token_id' => $fcmToken->id,
        ]);
    }

    /**
     * Check if the FCM error response indicates an unregistered token.
     */
    private function isUnregisteredError(array $body): bool
    {
        $details = $body['error']['details'] ?? [];

        foreach ($details as $detail) {
            if (isset($detail['errorCode']) && $detail['errorCode'] === 'UNREGISTERED') {
                return true;
            }
        }

        // Also check the error message for UNREGISTERED
        $message = $body['error']['message'] ?? '';

        return str_contains($message, 'UNREGISTERED');
    }

    /**
     * Get an OAuth2 access token from the Firebase service account credentials.
     */
    private function getAccessToken(): ?string
    {
        $credentialsPath = config('services.firebase.credentials_path');

        if (! $credentialsPath || ! file_exists($credentialsPath)) {
            Log::error('FCM: Service account credentials file not found', [
                'path' => $credentialsPath,
            ]);

            return null;
        }

        try {
            $credentials = json_decode(file_get_contents($credentialsPath), true);

            if (! $credentials || ! isset($credentials['client_email'], $credentials['private_key'], $credentials['token_uri'])) {
                Log::error('FCM: Invalid service account credentials file');

                return null;
            }

            $jwt = $this->createJwt($credentials);

            $response = $this->httpClient->post($credentials['token_uri'], [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ],
            ]);

            $tokenData = json_decode((string) $response->getBody(), true);

            return $tokenData['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('FCM: Failed to get access token', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Create a signed JWT for Google OAuth2 service account authentication.
     */
    private function createJwt(array $credentials): string
    {
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];

        $now = time();
        $claims = [
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $credentials['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $claimsEncoded = $this->base64UrlEncode(json_encode($claims));

        $signingInput = "{$headerEncoded}.{$claimsEncoded}";

        $privateKey = openssl_pkey_get_private($credentials['private_key']);

        if ($privateKey === false) {
            throw new \RuntimeException('FCM: Failed to parse service account private key');
        }

        openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        $signatureEncoded = $this->base64UrlEncode($signature);

        return "{$signingInput}.{$signatureEncoded}";
    }

    /**
     * Base64 URL-safe encoding (no padding).
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
