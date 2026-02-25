<?php

use App\Domain\Auth\Models\User;
use App\Domain\Edduz\Actions\ProcessWebhookAction;
use App\Domain\Edduz\Enums\WebhookEventType;
use App\Domain\Edduz\Enums\WebhookProcessingStatus;
use App\Domain\Edduz\Models\EdduzWebhookLog;
use App\Domain\Edduz\Repositories\EdduzWebhookLogRepository;
use App\Domain\Subscription\Repositories\SubscriptionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Build a minimal valid payload for the given event and user.
 */
function makePayload(string $event, int|string|null $userId, ?string $transactionId = null): array
{
    return [
        'event'          => $event,
        'user_id'        => $userId,
        'transaction_id' => $transactionId ?? fake()->uuid(),
        'plan_id'        => fake()->randomElement(['monthly', 'yearly']),
    ];
}

// ─────────────────────────────────────────────────────────────────────────────
// Property 2: Token inválido retorna 401 e é registrado
// Feature: edduz-subscription-integration, Property 2: Token inválido retorna 401 e é registrado
// Validates: Requirement 2.3
// ─────────────────────────────────────────────────────────────────────────────

describe('Property 2: Token inválido retorna 401 e é registrado', function () {

    /**
     * **Validates: Requirements 2.3**
     *
     * Para qualquer token que não corresponda ao token configurado, o processamento
     * deve retornar HTTP 401 e persistir um log com status invalid_token.
     */
    test('property: token inválido sempre retorna 401 e registra invalid_token (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 2: Token inválido retorna 401 e é registrado

        config(['services.edduz.webhook_token' => 'correct-secret-token']);

        $action = new ProcessWebhookAction(
            new EdduzWebhookLogRepository(),
            new SubscriptionRepository(),
        );

        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            // Generate a random token that is guaranteed to differ from the configured one
            $invalidToken = 'invalid-' . $faker->uuid() . '-' . $faker->word();

            $payload = makePayload(
                WebhookEventType::SUBSCRIPTION_CONFIRMED->value,
                null, // user_id null — FK-safe, token is rejected before user lookup
                $faker->uuid(),
            );

            $result = $action->execute(
                payload: $payload,
                ipAddress: $faker->ipv4(),
                headers: ['X-Edduz-Token' => $invalidToken],
                receivedToken: $invalidToken,
            );

            // Assert HTTP 401
            expect($result->httpStatus)
                ->toBe(401, "Iteration $i: expected 401 for invalid token '$invalidToken'");

            // Assert processing status
            expect($result->processingStatus)
                ->toBe(WebhookProcessingStatus::INVALID_TOKEN, "Iteration $i: expected INVALID_TOKEN status");

            // Assert a log was persisted with invalid_token status
            $log = EdduzWebhookLog::where('transaction_id', $payload['transaction_id'])->first();
            expect($log)->not->toBeNull("Iteration $i: webhook log must be created");
            expect($log->processing_status)
                ->toBe(WebhookProcessingStatus::INVALID_TOKEN->value, "Iteration $i: log status must be invalid_token");
        }
    });

    test('token correto não é rejeitado', function () {
        // Feature: edduz-subscription-integration, Property 2: Token inválido retorna 401 e é registrado

        config(['services.edduz.webhook_token' => 'my-secret']);

        $user = User::factory()->create();

        $subscriptionRepo = Mockery::mock(SubscriptionRepository::class);
        $subscriptionRepo->shouldReceive('createSubscription')->once()->andReturn(
            new \App\Domain\Subscription\DTOs\SubscriptionData(
                userId: (string) $user->id,
                status: \App\Domain\Auth\Enums\SubscriptionStatus::ACTIVE,
                planType: \App\Domain\Subscription\Enums\PlanType::MONTHLY,
                expiresAt: null,
                platformId: 'txn-123',
            )
        );

        $action = new ProcessWebhookAction(
            new EdduzWebhookLogRepository(),
            $subscriptionRepo,
        );

        $result = $action->execute(
            payload: makePayload(WebhookEventType::SUBSCRIPTION_CONFIRMED->value, $user->id),
            ipAddress: '127.0.0.1',
            headers: [],
            receivedToken: 'my-secret',
        );

        expect($result->httpStatus)->toBe(200);
        expect($result->processingStatus)->not->toBe(WebhookProcessingStatus::INVALID_TOKEN);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Property 3: Mapeamento evento → status de assinatura
// Feature: edduz-subscription-integration, Property 3: Mapeamento evento → status de assinatura
// Validates: Requirements 2.4, 2.5, 2.6, 2.8
// ─────────────────────────────────────────────────────────────────────────────

describe('Property 3: Mapeamento evento → status de assinatura', function () {

    /**
     * **Validates: Requirements 2.4, 2.5, 2.6, 2.8**
     *
     * Para cada tipo de evento válido com usuário existente, o método correto do
     * SubscriptionRepository deve ser chamado e o resultado deve ser HTTP 200.
     */
    test('property: subscription_confirmed chama createSubscription e retorna 200 (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 3: Mapeamento evento → status de assinatura

        config(['services.edduz.webhook_token' => 'valid-token']);

        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            $user = User::factory()->create();
            $transactionId = $faker->uuid();
            $planId = $faker->randomElement(['monthly', 'yearly']);

            $subscriptionRepo = Mockery::mock(SubscriptionRepository::class);
            $subscriptionRepo->shouldReceive('createSubscription')
                ->once()
                ->withArgs(function ($uid, $planType, $txnId) use ($user, $transactionId) {
                    return (string) $uid === (string) $user->id && $txnId === $transactionId;
                })
                ->andReturn(
                    new \App\Domain\Subscription\DTOs\SubscriptionData(
                        userId: (string) $user->id,
                        status: \App\Domain\Auth\Enums\SubscriptionStatus::ACTIVE,
                        planType: \App\Domain\Subscription\Enums\PlanType::MONTHLY,
                        expiresAt: null,
                        platformId: $transactionId,
                    )
                );

            $action = new ProcessWebhookAction(
                new EdduzWebhookLogRepository(),
                $subscriptionRepo,
            );

            $payload = [
                'event'          => WebhookEventType::SUBSCRIPTION_CONFIRMED->value,
                'user_id'        => $user->id,
                'transaction_id' => $transactionId,
                'plan_id'        => $planId,
            ];

            $result = $action->execute(
                payload: $payload,
                ipAddress: $faker->ipv4(),
                headers: [],
                receivedToken: 'valid-token',
            );

            expect($result->httpStatus)->toBe(200, "Iteration $i: expected 200 for subscription_confirmed");
            expect($result->processingStatus)->toBe(WebhookProcessingStatus::SUCCESS, "Iteration $i: expected SUCCESS");

            Mockery::close();
        }
    });

    test('property: subscription_cancelled chama cancelSubscription e retorna 200 (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 3: Mapeamento evento → status de assinatura

        config(['services.edduz.webhook_token' => 'valid-token']);

        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            $user = User::factory()->create();

            $subscriptionRepo = Mockery::mock(SubscriptionRepository::class);
            $subscriptionRepo->shouldReceive('cancelSubscription')
                ->once()
                ->with((string) $user->id)
                ->andReturn(
                    new \App\Domain\Subscription\DTOs\SubscriptionData(
                        userId: (string) $user->id,
                        status: \App\Domain\Auth\Enums\SubscriptionStatus::INACTIVE,
                        planType: null,
                        expiresAt: null,
                        platformId: null,
                    )
                );

            $action = new ProcessWebhookAction(
                new EdduzWebhookLogRepository(),
                $subscriptionRepo,
            );

            $result = $action->execute(
                payload: makePayload(WebhookEventType::SUBSCRIPTION_CANCELLED->value, $user->id, $faker->uuid()),
                ipAddress: $faker->ipv4(),
                headers: [],
                receivedToken: 'valid-token',
            );

            expect($result->httpStatus)->toBe(200, "Iteration $i: expected 200 for subscription_cancelled");
            expect($result->processingStatus)->toBe(WebhookProcessingStatus::SUCCESS, "Iteration $i: expected SUCCESS");

            Mockery::close();
        }
    });

    test('property: subscription_expired atualiza status para expired e retorna 200 (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 3: Mapeamento evento → status de assinatura

        config(['services.edduz.webhook_token' => 'valid-token']);

        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            $user = User::factory()->create();

            $action = new ProcessWebhookAction(
                new EdduzWebhookLogRepository(),
                new SubscriptionRepository(),
            );

            $result = $action->execute(
                payload: makePayload(WebhookEventType::SUBSCRIPTION_EXPIRED->value, $user->id, $faker->uuid()),
                ipAddress: $faker->ipv4(),
                headers: [],
                receivedToken: 'valid-token',
            );

            expect($result->httpStatus)->toBe(200, "Iteration $i: expected 200 for subscription_expired");
            expect($result->processingStatus)->toBe(WebhookProcessingStatus::SUCCESS, "Iteration $i: expected SUCCESS");

            // Verify the user's subscription_status was updated to expired
            $user->refresh();
            expect($user->subscription_status->value ?? $user->subscription_status)
                ->toBe(\App\Domain\Auth\Enums\SubscriptionStatus::EXPIRED->value, "Iteration $i: user status must be expired");
        }
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Property 4: Webhook é sempre registrado independentemente do resultado
// Feature: edduz-subscription-integration, Property 4: Webhook é sempre registrado independentemente do resultado
// Validates: Requirements 3.1, 3.2, 3.3
// ─────────────────────────────────────────────────────────────────────────────

describe('Property 4: Webhook é sempre registrado independentemente do resultado', function () {

    /**
     * **Validates: Requirements 3.1, 3.2, 3.3**
     *
     * Para qualquer requisição (token válido ou inválido, usuário existente ou não),
     * um registro de webhook deve ser persistido no banco de dados.
     */
    test('property: todo webhook é persistido independentemente do token (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 4: Webhook é sempre registrado independentemente do resultado

        config(['services.edduz.webhook_token' => 'configured-token']);

        $action = new ProcessWebhookAction(
            new EdduzWebhookLogRepository(),
            new SubscriptionRepository(),
        );

        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            $useValidToken = $faker->boolean();
            $token = $useValidToken ? 'configured-token' : 'wrong-token-' . $faker->uuid();
            $transactionId = $faker->uuid();

            $payload = makePayload(
                WebhookEventType::SUBSCRIPTION_CONFIRMED->value,
                null, // null user_id — FK-safe; user lookup happens after token validation
                $transactionId,
            );

            $action->execute(
                payload: $payload,
                ipAddress: $faker->ipv4(),
                headers: ['User-Agent' => $faker->userAgent()],
                receivedToken: $token,
            );

            // A log record must always exist regardless of token validity
            $log = EdduzWebhookLog::where('transaction_id', $transactionId)->first();
            expect($log)->not->toBeNull(
                "Iteration $i: log must be created for every webhook (valid_token=$useValidToken)"
            );

            // Log must contain the full payload
            expect($log->payload)->toMatchArray($payload, "Iteration $i: log must contain full payload");

            // Log must contain the IP address
            expect($log->ip_address)->not->toBeNull("Iteration $i: log must contain ip_address");

            // Log must contain received_at
            expect($log->received_at)->not->toBeNull("Iteration $i: log must contain received_at");
        }
    });

    test('property: log é criado antes do processamento (token inválido ainda gera log)', function () {
        // Feature: edduz-subscription-integration, Property 4: Webhook é sempre registrado independentemente do resultado

        config(['services.edduz.webhook_token' => 'real-token']);

        $action = new ProcessWebhookAction(
            new EdduzWebhookLogRepository(),
            new SubscriptionRepository(),
        );

        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            $transactionId = $faker->uuid();
            $ip = $faker->ipv4();

            $payload = makePayload(
                $faker->randomElement(array_column(WebhookEventType::cases(), 'value')),
                null, // null user_id — FK-safe
                $transactionId,
            );

            // Use invalid token to trigger early return
            $action->execute(
                payload: $payload,
                ipAddress: $ip,
                headers: [],
                receivedToken: 'wrong-token',
            );

            $log = EdduzWebhookLog::where('transaction_id', $transactionId)->first();
            expect($log)->not->toBeNull("Iteration $i: log must exist even when token is invalid");
            expect($log->ip_address)->toBe($ip, "Iteration $i: log must store the correct IP");
        }
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Property 5: Falha no processamento atualiza registro com erro
// Feature: edduz-subscription-integration, Property 5: Falha no processamento atualiza registro com erro
// Validates: Requirement 3.4
// ─────────────────────────────────────────────────────────────────────────────

describe('Property 5: Falha no processamento atualiza registro com erro', function () {

    /**
     * **Validates: Requirement 3.4**
     *
     * Quando o processamento falha (ex: SubscriptionRepository lança exceção),
     * o registro de webhook deve ser atualizado com status error e a mensagem de erro.
     */
    test('property: exceção no SubscriptionRepository atualiza log com ERROR (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 5: Falha no processamento atualiza registro com erro

        config(['services.edduz.webhook_token' => 'valid-token']);

        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            $user = User::factory()->create();
            $transactionId = $faker->uuid();
            $errorMessage = 'Simulated failure: ' . $faker->sentence();

            $subscriptionRepo = Mockery::mock(SubscriptionRepository::class);
            $subscriptionRepo->shouldReceive('createSubscription')
                ->once()
                ->andThrow(new \RuntimeException($errorMessage));

            $action = new ProcessWebhookAction(
                new EdduzWebhookLogRepository(),
                $subscriptionRepo,
            );

            $result = $action->execute(
                payload: makePayload(WebhookEventType::SUBSCRIPTION_CONFIRMED->value, $user->id, $transactionId),
                ipAddress: $faker->ipv4(),
                headers: [],
                receivedToken: 'valid-token',
            );

            // Action should still return 200 (business error, not infra error)
            expect($result->httpStatus)->toBe(200, "Iteration $i: expected 200 even on failure");
            expect($result->processingStatus)->toBe(WebhookProcessingStatus::ERROR, "Iteration $i: expected ERROR status");

            // The log must be updated with error status and message
            $log = EdduzWebhookLog::where('transaction_id', $transactionId)->first();
            expect($log)->not->toBeNull("Iteration $i: log must exist");
            expect($log->processing_status)
                ->toBe(WebhookProcessingStatus::ERROR->value, "Iteration $i: log status must be error");
            expect($log->error_message)
                ->toBe($errorMessage, "Iteration $i: log must contain the error message");

            Mockery::close();
        }
    });

    test('property: cancelSubscription lançando exceção atualiza log com ERROR (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 5: Falha no processamento atualiza registro com erro

        config(['services.edduz.webhook_token' => 'valid-token']);

        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            $user = User::factory()->create();
            $transactionId = $faker->uuid();
            $errorMessage = 'Cancel failed: ' . $faker->word();

            $subscriptionRepo = Mockery::mock(SubscriptionRepository::class);
            $subscriptionRepo->shouldReceive('cancelSubscription')
                ->once()
                ->andThrow(new \RuntimeException($errorMessage));

            $action = new ProcessWebhookAction(
                new EdduzWebhookLogRepository(),
                $subscriptionRepo,
            );

            $result = $action->execute(
                payload: makePayload(WebhookEventType::SUBSCRIPTION_CANCELLED->value, $user->id, $transactionId),
                ipAddress: $faker->ipv4(),
                headers: [],
                receivedToken: 'valid-token',
            );

            expect($result->processingStatus)->toBe(WebhookProcessingStatus::ERROR, "Iteration $i: expected ERROR");

            $log = EdduzWebhookLog::where('transaction_id', $transactionId)->first();
            expect($log->processing_status)->toBe(WebhookProcessingStatus::ERROR->value, "Iteration $i: log must be error");
            expect($log->error_message)->toBe($errorMessage, "Iteration $i: error message must match");

            Mockery::close();
        }
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Property 11: Idempotência por transaction_id
// Feature: edduz-subscription-integration, Property 11: Idempotência por transaction_id
// Validates: Requirements 6.1, 6.2
// ─────────────────────────────────────────────────────────────────────────────

describe('Property 11: Idempotência por transaction_id', function () {

    /**
     * **Validates: Requirements 6.1, 6.2**
     *
     * Para qualquer webhook com transaction_id já processado com sucesso,
     * o reenvio deve retornar HTTP 200 com status DUPLICATE e não chamar
     * o SubscriptionRepository novamente.
     */
    test('property: segundo envio com mesmo transaction_id retorna DUPLICATE sem reprocessar (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 11: Idempotência por transaction_id

        config(['services.edduz.webhook_token' => 'valid-token']);

        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            $user = User::factory()->create();
            $transactionId = $faker->uuid();

            // SubscriptionRepository should be called EXACTLY ONCE (first request only)
            $subscriptionRepo = Mockery::mock(SubscriptionRepository::class);
            $subscriptionRepo->shouldReceive('createSubscription')
                ->once()
                ->andReturn(
                    new \App\Domain\Subscription\DTOs\SubscriptionData(
                        userId: (string) $user->id,
                        status: \App\Domain\Auth\Enums\SubscriptionStatus::ACTIVE,
                        planType: \App\Domain\Subscription\Enums\PlanType::MONTHLY,
                        expiresAt: null,
                        platformId: $transactionId,
                    )
                );

            $action = new ProcessWebhookAction(
                new EdduzWebhookLogRepository(),
                $subscriptionRepo,
            );

            $payload = makePayload(WebhookEventType::SUBSCRIPTION_CONFIRMED->value, $user->id, $transactionId);

            // First call — should succeed
            $firstResult = $action->execute(
                payload: $payload,
                ipAddress: $faker->ipv4(),
                headers: [],
                receivedToken: 'valid-token',
            );

            expect($firstResult->httpStatus)->toBe(200, "Iteration $i: first call must return 200");
            expect($firstResult->processingStatus)->toBe(WebhookProcessingStatus::SUCCESS, "Iteration $i: first call must be SUCCESS");

            // Second call with same transaction_id — should be DUPLICATE
            $secondResult = $action->execute(
                payload: $payload,
                ipAddress: $faker->ipv4(),
                headers: [],
                receivedToken: 'valid-token',
            );

            expect($secondResult->httpStatus)->toBe(200, "Iteration $i: second call must return 200");
            expect($secondResult->processingStatus)->toBe(WebhookProcessingStatus::DUPLICATE, "Iteration $i: second call must be DUPLICATE");

            // Mockery verifies that createSubscription was called only once
            Mockery::close();
        }
    });

    test('property: múltiplos reenvios do mesmo webhook são todos marcados como DUPLICATE (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 11: Idempotência por transaction_id

        config(['services.edduz.webhook_token' => 'valid-token']);

        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            $user = User::factory()->create();
            $transactionId = $faker->uuid();
            $extraSends = $faker->numberBetween(1, 5);

            $subscriptionRepo = Mockery::mock(SubscriptionRepository::class);
            $subscriptionRepo->shouldReceive('createSubscription')
                ->once() // Only the first call should trigger this
                ->andReturn(
                    new \App\Domain\Subscription\DTOs\SubscriptionData(
                        userId: (string) $user->id,
                        status: \App\Domain\Auth\Enums\SubscriptionStatus::ACTIVE,
                        planType: \App\Domain\Subscription\Enums\PlanType::MONTHLY,
                        expiresAt: null,
                        platformId: $transactionId,
                    )
                );

            $action = new ProcessWebhookAction(
                new EdduzWebhookLogRepository(),
                $subscriptionRepo,
            );

            $payload = makePayload(WebhookEventType::SUBSCRIPTION_CONFIRMED->value, $user->id, $transactionId);

            // First call
            $action->execute(
                payload: $payload,
                ipAddress: $faker->ipv4(),
                headers: [],
                receivedToken: 'valid-token',
            );

            // Subsequent calls (1 to 5 extra)
            for ($j = 0; $j < $extraSends; $j++) {
                $result = $action->execute(
                    payload: $payload,
                    ipAddress: $faker->ipv4(),
                    headers: [],
                    receivedToken: 'valid-token',
                );

                expect($result->processingStatus)
                    ->toBe(WebhookProcessingStatus::DUPLICATE, "Iteration $i, resend $j: must be DUPLICATE");
                expect($result->httpStatus)
                    ->toBe(200, "Iteration $i, resend $j: must return 200");
            }

            // Mockery verifies createSubscription was called only once
            Mockery::close();
        }
    });
});
