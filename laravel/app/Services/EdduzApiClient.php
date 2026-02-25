<?php

namespace App\Services;

use App\Exceptions\EdduzApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EdduzApiClient
{
    public function __construct(
        private string $baseUrl,
        private string $apiKey,
        private string $webhookToken,
    ) {}

    /**
     * Gera URL de checkout na Edduz.
     *
     * @throws EdduzApiException
     */
    public function createCheckoutSession(string $userId, string $planId): string
    {
        try {
            $response = Http::post("{$this->baseUrl}/checkout", [
                'api_key' => $this->apiKey,
                'user_id' => $userId,
                'plan_id' => $planId,
            ]);

            if ($response->failed()) {
                throw new EdduzApiException(
                    "Edduz API retornou status {$response->status()} ao criar sessão de checkout."
                );
            }

            $checkoutUrl = $response->json('checkout_url');

            if (empty($checkoutUrl)) {
                throw new EdduzApiException(
                    'Edduz API não retornou uma URL de checkout válida.'
                );
            }

            return $checkoutUrl;
        } catch (EdduzApiException $e) {
            Log::error('Falha ao criar sessão de checkout na Edduz.', [
                'user_id' => $userId,
                'plan_id' => $planId,
                'error'   => $e->getMessage(),
            ]);

            throw $e;
        } catch (\Throwable $e) {
            Log::error('Erro inesperado ao comunicar com a Edduz.', [
                'user_id' => $userId,
                'plan_id' => $planId,
                'error'   => $e->getMessage(),
            ]);

            throw new EdduzApiException(
                "Erro inesperado ao comunicar com a Edduz: {$e->getMessage()}",
                previous: $e
            );
        }
    }

    /**
     * Verifica se a configuração está completa.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->baseUrl)
            && ! empty($this->apiKey)
            && ! empty($this->webhookToken);
    }
}
