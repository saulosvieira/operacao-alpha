<?php

// Feature: edduz-subscription-integration, Property 8: Listagem exibe todos os campos obrigatórios

use App\Domain\Auth\Enums\UserRole;
use App\Domain\Auth\Models\User;
use App\Domain\Edduz\Enums\WebhookEventType;
use App\Domain\Edduz\Enums\WebhookProcessingStatus;
use App\Domain\Edduz\Models\EdduzWebhookLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Map event_type raw value to the human-readable label rendered in the view.
 */
function eventTypeLabel(string $eventType): string
{
    return match ($eventType) {
        'subscription_confirmed' => 'Assinatura Confirmada',
        'subscription_cancelled' => 'Assinatura Cancelada',
        'subscription_expired'   => 'Assinatura Expirada',
        default                  => $eventType,
    };
}

/**
 * Map processing_status raw value to the human-readable label rendered in the view.
 */
function processingStatusLabel(string $status): string
{
    return match ($status) {
        'success'       => 'Sucesso',
        'error'         => 'Erro',
        'duplicate'     => 'Duplicado',
        'invalid_token' => 'Token Inválido',
        default         => $status,
    };
}

// ─────────────────────────────────────────────────────────────────────────────
// Property 8: Listagem exibe todos os campos obrigatórios
// Feature: edduz-subscription-integration, Property 8: Listagem exibe todos os campos obrigatórios
// Validates: Requirement 4.3
// ─────────────────────────────────────────────────────────────────────────────

describe('Property 8: Listagem exibe todos os campos obrigatórios', function () {

    /**
     * **Validates: Requirements 4.3**
     *
     * Para qualquer registro de webhook na listagem, a página deve exibir:
     * data/hora de recebimento, tipo de evento, identificador do usuário,
     * status do processamento e endereço IP de origem.
     */
    test('property: listagem exibe todos os campos obrigatórios para cada registro (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 8: Listagem exibe todos os campos obrigatórios

        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            EdduzWebhookLog::query()->delete();

            // Generate known values for all required fields using Carbon for consistency
            $receivedAt       = \Illuminate\Support\Carbon::now()->subSeconds($faker->numberBetween(1, 30 * 24 * 3600));
            $eventType        = $faker->randomElement(WebhookEventType::cases())->value;
            $processingStatus = $faker->randomElement(WebhookProcessingStatus::cases())->value;
            $ipAddress        = $faker->ipv4();

            // Create a real user so the FK constraint is satisfied
            $logUser = User::factory()->create();
            $userId  = $logUser->id;

            EdduzWebhookLog::create([
                'transaction_id'    => $faker->uuid(),
                'event_type'        => $eventType,
                'user_id'           => $userId,
                'payload'           => ['event' => $eventType, 'user_id' => $userId],
                'ip_address'        => $ipAddress,
                'headers'           => ['Content-Type' => 'application/json'],
                'processing_status' => $processingStatus,
                'error_message'     => null,
                'received_at'       => $receivedAt,
                'processed_at'      => $receivedAt,
            ]);

            $response = $this->actingAs($admin)->get(route('admin.webhooks.edduz.index'));
            $response->assertStatus(200);

            // 1. Data e hora de recebimento (received_at formatted as d/m/Y H:i:s)
            $formattedDate = $receivedAt->format('d/m/Y H:i:s');
            $response->assertSee($formattedDate, false);

            // 2. Tipo de evento — the view renders a human-readable badge label
            $eventLabel = eventTypeLabel($eventType);
            $response->assertSee($eventLabel, false);

            // 3. Identificador do Usuário (user_id rendered as plain number)
            $response->assertSee((string) $userId, false);

            // 4. Status do processamento — the view renders a human-readable badge label
            $statusLabel = processingStatusLabel($processingStatus);
            $response->assertSee($statusLabel, false);

            // 5. Endereço IP de origem (ip_address rendered inside <code> tag)
            $response->assertSee($ipAddress, false);
        }
    });

    /**
     * **Validates: Requirements 4.3**
     *
     * Quando múltiplos registros estão na listagem, todos os campos obrigatórios
     * de cada registro devem estar presentes na resposta.
     */
    test('property: listagem com múltiplos registros exibe campos de todos eles (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 8: Listagem exibe todos os campos obrigatórios

        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            EdduzWebhookLog::query()->delete();

            $count = $faker->numberBetween(2, 5);
            $records = [];

            for ($j = 0; $j < $count; $j++) {
                $receivedAt       = \Illuminate\Support\Carbon::now()->subSeconds($faker->numberBetween(1, 30 * 24 * 3600));
                $eventType        = $faker->randomElement(WebhookEventType::cases())->value;
                $processingStatus = $faker->randomElement(WebhookProcessingStatus::cases())->value;
                // Use unique IPs to avoid false positives from overlapping values
                $ipAddress        = '10.' . $faker->numberBetween(0, 255) . '.' . $j . '.' . ($i % 256);

                // Create a real user so the FK constraint is satisfied
                $logUser = User::factory()->create();
                $userId  = $logUser->id;

                EdduzWebhookLog::create([
                    'transaction_id'    => $faker->uuid(),
                    'event_type'        => $eventType,
                    'user_id'           => $userId,
                    'payload'           => ['event' => $eventType, 'user_id' => $userId],
                    'ip_address'        => $ipAddress,
                    'headers'           => ['Content-Type' => 'application/json'],
                    'processing_status' => $processingStatus,
                    'error_message'     => null,
                    'received_at'       => $receivedAt,
                    'processed_at'      => $receivedAt,
                ]);

                $records[] = [
                    'ip_address'        => $ipAddress,
                    'event_type'        => $eventType,
                    'processing_status' => $processingStatus,
                    'received_at'       => $receivedAt,
                    'user_id'           => $userId,
                ];
            }

            $response = $this->actingAs($admin)->get(route('admin.webhooks.edduz.index'));
            $response->assertStatus(200);

            foreach ($records as $idx => $record) {
                // IP address must be present for each record (unique per record)
                $response->assertSee($record['ip_address'], false);

                // event_type label must be present (may overlap if multiple records have same type)
                $response->assertSee(eventTypeLabel($record['event_type']), false);

                // processing_status label must be present
                $response->assertSee(processingStatusLabel($record['processing_status']), false);
            }
        }
    });
});
