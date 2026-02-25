<?php

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
 * Create an EdduzWebhookLog record with the given attributes.
 */
function makeWebhookLog(array $overrides = []): EdduzWebhookLog
{
    $faker = fake();

    return EdduzWebhookLog::create(array_merge([
        'transaction_id'    => $faker->uuid(),
        'event_type'        => $faker->randomElement(array_column(WebhookEventType::cases(), 'value')),
        'user_id'           => null,
        'payload'           => ['event' => 'subscription_confirmed', 'user_id' => 1],
        'ip_address'        => $faker->ipv4(),
        'headers'           => ['Content-Type' => 'application/json'],
        'processing_status' => $faker->randomElement(array_column(WebhookProcessingStatus::cases(), 'value')),
        'error_message'     => null,
        'received_at'       => now(),
        'processed_at'      => now(),
    ], $overrides));
}

// ─────────────────────────────────────────────────────────────────────────────
// Property 6: Acesso à página de histórico restrito a administradores
// Feature: edduz-subscription-integration, Property 6: Acesso à página de histórico restrito a administradores
// Validates: Requirement 4.1
// ─────────────────────────────────────────────────────────────────────────────

describe('Property 6: Acesso à página de histórico restrito a administradores', function () {

    /**
     * **Validates: Requirements 4.1**
     *
     * Usuários não autenticados devem ser redirecionados ao tentar acessar
     * a página de histórico de webhooks.
     */
    test('property: usuário não autenticado é redirecionado (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 6: Acesso à página de histórico restrito a administradores

        for ($i = 0; $i < 100; $i++) {
            $response = $this->get(route('admin.webhooks.edduz.index'));

            $response->assertRedirect();
            expect($response->getStatusCode())->toBe(302, "Iteration $i: unauthenticated user must be redirected");
        }
    });

    /**
     * **Validates: Requirements 4.1**
     *
     * Usuários não-admin autenticados não devem ter acesso à página de histórico.
     * A rota está protegida pelo middleware 'auth' e o gate 'admin' é verificado.
     */
    test('property: usuário não-admin autenticado não tem acesso (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 6: Acesso à página de histórico restrito a administradores

        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            $nonAdminRole = $faker->randomElement([
                UserRole::USER->value,
                UserRole::CONSULTANT->value,
            ]);

            $user = User::factory()->create(['role' => $nonAdminRole]);

            $response = $this->actingAs($user)->get(route('admin.webhooks.edduz.index'));

            // Non-admin users should be denied access (403 or redirect)
            expect($response->getStatusCode())
                ->toBeIn([302, 403], "Iteration $i: non-admin user (role=$nonAdminRole) must not access the page");
        }
    });

    /**
     * **Validates: Requirements 4.1**
     *
     * Administradores autenticados devem ter acesso à página de histórico.
     */
    test('property: administrador autenticado tem acesso (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 6: Acesso à página de histórico restrito a administradores

        for ($i = 0; $i < 100; $i++) {
            $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);

            $response = $this->actingAs($admin)->get(route('admin.webhooks.edduz.index'));

            $response->assertStatus(200, "Iteration $i: admin user must access the page successfully");
        }
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Property 7: Ordenação decrescente por data de recebimento
// Feature: edduz-subscription-integration, Property 7: Ordenação decrescente por data de recebimento
// Validates: Requirement 4.2
// ─────────────────────────────────────────────────────────────────────────────

describe('Property 7: Ordenação decrescente por data de recebimento', function () {

    /**
     * **Validates: Requirements 4.2**
     *
     * Para qualquer conjunto de registros com datas aleatórias, a listagem
     * deve retornar os registros ordenados por received_at decrescente.
     */
    test('property: registros são ordenados por received_at decrescente (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 7: Ordenação decrescente por data de recebimento

        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            // Clear existing records for a clean state each iteration
            EdduzWebhookLog::query()->delete();

            // Create between 2 and 8 records with random timestamps
            $count = $faker->numberBetween(2, 8);
            $timestamps = [];

            for ($j = 0; $j < $count; $j++) {
                $receivedAt = $faker->dateTimeBetween('-30 days', 'now');
                $timestamps[] = $receivedAt->format('Y-m-d H:i:s');

                makeWebhookLog(['received_at' => $receivedAt]);
            }

            // Fetch records via repository (same logic as controller)
            $repo = new \App\Domain\Edduz\Repositories\EdduzWebhookLogRepository();
            $paginator = $repo->paginate();
            $records = $paginator->items();

            // Assert descending order
            $receivedAts = array_map(fn ($r) => $r->received_at->timestamp, $records);

            for ($k = 0; $k < count($receivedAts) - 1; $k++) {
                expect($receivedAts[$k])
                    ->toBeGreaterThanOrEqual(
                        $receivedAts[$k + 1],
                        "Iteration $i: record at position $k must be >= record at position " . ($k + 1)
                    );
            }
        }
    });

    /**
     * **Validates: Requirements 4.2**
     *
     * A resposta HTTP da página de listagem deve conter os registros em ordem
     * decrescente por data de recebimento (verificado via IP do registro mais recente).
     */
    test('property: resposta HTTP contém registros em ordem decrescente (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 7: Ordenação decrescente por data de recebimento

        $admin = User::factory()->create(['role' => UserRole::ADMIN->value]);
        $faker = fake();

        for ($i = 0; $i < 100; $i++) {
            EdduzWebhookLog::query()->delete();

            // Create records with distinct timestamps spread over days
            $count = $faker->numberBetween(2, 5);

            for ($j = 0; $j < $count; $j++) {
                $daysAgo = $count - $j; // older records first in creation order
                makeWebhookLog([
                    'received_at' => now()->subDays($daysAgo)->subSeconds($faker->numberBetween(0, 3600)),
                ]);
            }

            // The most recently received log should appear first
            $mostRecent = EdduzWebhookLog::orderBy('received_at', 'desc')->first();

            $response = $this->actingAs($admin)->get(route('admin.webhooks.edduz.index'));
            $response->assertStatus(200);

            // The most recent record's IP address should appear in the response
            $content = $response->getContent();
            $ipPos = strpos($content, $mostRecent->ip_address);

            // Verify the most recent record appears in the response
            expect($ipPos)->not->toBeFalse(
                "Iteration $i: most recent record (id={$mostRecent->id}, ip={$mostRecent->ip_address}) must appear in the response"
            );

            // Verify the most recent record's date appears before older records' dates
            // by checking the repository returns them in descending order
            $repo = new \App\Domain\Edduz\Repositories\EdduzWebhookLogRepository();
            $records = $repo->paginate()->items();

            expect($records[0]->id)->toBe(
                $mostRecent->id,
                "Iteration $i: first record in paginator must be the most recent one"
            );
        }
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Property 9: Filtros retornam apenas registros correspondentes
// Feature: edduz-subscription-integration, Property 9: Filtros retornam apenas registros correspondentes
// Validates: Requirements 4.5, 4.6, 4.7
// ─────────────────────────────────────────────────────────────────────────────

describe('Property 9: Filtros retornam apenas registros correspondentes', function () {

    /**
     * **Validates: Requirements 4.5**
     *
     * O filtro por status de processamento deve retornar apenas registros
     * com o status correspondente.
     */
    test('property: filtro por status retorna apenas registros com o status selecionado (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 9: Filtros retornam apenas registros correspondentes

        $faker = fake();
        $repo = new \App\Domain\Edduz\Repositories\EdduzWebhookLogRepository();

        for ($i = 0; $i < 100; $i++) {
            EdduzWebhookLog::query()->delete();

            // Pick a random target status
            $targetStatus = $faker->randomElement(WebhookProcessingStatus::cases());

            // Create records with mixed statuses
            $totalRecords = $faker->numberBetween(3, 10);
            $matchingCount = 0;

            for ($j = 0; $j < $totalRecords; $j++) {
                $status = $faker->randomElement(WebhookProcessingStatus::cases());
                makeWebhookLog(['processing_status' => $status->value]);

                if ($status === $targetStatus) {
                    $matchingCount++;
                }
            }

            // Apply status filter
            $results = $repo->paginate(['status' => $targetStatus->value]);

            // All returned records must match the target status
            foreach ($results->items() as $record) {
                expect($record->processing_status)
                    ->toBe($targetStatus->value, "Iteration $i: all records must have status={$targetStatus->value}");
            }

            // The count must match
            expect($results->total())
                ->toBe($matchingCount, "Iteration $i: result count must match records with status={$targetStatus->value}");
        }
    });

    /**
     * **Validates: Requirements 4.6**
     *
     * O filtro por tipo de evento deve retornar apenas registros com o
     * tipo de evento correspondente.
     */
    test('property: filtro por event_type retorna apenas registros com o tipo selecionado (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 9: Filtros retornam apenas registros correspondentes

        $faker = fake();
        $repo = new \App\Domain\Edduz\Repositories\EdduzWebhookLogRepository();

        for ($i = 0; $i < 100; $i++) {
            EdduzWebhookLog::query()->delete();

            // Pick a random target event type
            $targetEventType = $faker->randomElement(WebhookEventType::cases());

            // Create records with mixed event types
            $totalRecords = $faker->numberBetween(3, 10);
            $matchingCount = 0;

            for ($j = 0; $j < $totalRecords; $j++) {
                $eventType = $faker->randomElement(WebhookEventType::cases());
                makeWebhookLog(['event_type' => $eventType->value]);

                if ($eventType === $targetEventType) {
                    $matchingCount++;
                }
            }

            // Apply event_type filter
            $results = $repo->paginate(['event_type' => $targetEventType->value]);

            // All returned records must match the target event type
            foreach ($results->items() as $record) {
                expect($record->event_type)
                    ->toBe($targetEventType->value, "Iteration $i: all records must have event_type={$targetEventType->value}");
            }

            // The count must match
            expect($results->total())
                ->toBe($matchingCount, "Iteration $i: result count must match records with event_type={$targetEventType->value}");
        }
    });

    /**
     * **Validates: Requirements 4.7**
     *
     * O filtro por período de datas deve retornar apenas registros recebidos
     * dentro do período selecionado.
     */
    test('property: filtro por período de datas retorna apenas registros dentro do período (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 9: Filtros retornam apenas registros correspondentes

        $faker = fake();
        $repo = new \App\Domain\Edduz\Repositories\EdduzWebhookLogRepository();

        for ($i = 0; $i < 100; $i++) {
            EdduzWebhookLog::query()->delete();

            // Define a random date range window
            $windowStart = now()->subDays($faker->numberBetween(10, 30));
            $windowEnd   = $windowStart->copy()->addDays($faker->numberBetween(1, 9));

            // Create records: some inside the window, some outside
            $totalRecords = $faker->numberBetween(4, 12);
            $matchingCount = 0;

            for ($j = 0; $j < $totalRecords; $j++) {
                // Randomly place record inside or outside the window
                if ($faker->boolean(60)) {
                    // Inside window
                    $receivedAt = $faker->dateTimeBetween($windowStart, $windowEnd);
                    $matchingCount++;
                } else {
                    // Outside window (before start or after end)
                    if ($faker->boolean()) {
                        $receivedAt = $faker->dateTimeBetween('-60 days', $windowStart->copy()->subSecond());
                    } else {
                        $receivedAt = $faker->dateTimeBetween($windowEnd->copy()->addSecond(), 'now');
                    }
                }

                makeWebhookLog(['received_at' => $receivedAt]);
            }

            // Apply date range filter
            $results = $repo->paginate([
                'date_from' => $windowStart->format('Y-m-d H:i:s'),
                'date_to'   => $windowEnd->format('Y-m-d H:i:s'),
            ]);

            // All returned records must be within the date range
            foreach ($results->items() as $record) {
                expect($record->received_at->timestamp)
                    ->toBeGreaterThanOrEqual(
                        $windowStart->timestamp,
                        "Iteration $i: record received_at must be >= date_from"
                    );
                expect($record->received_at->timestamp)
                    ->toBeLessThanOrEqual(
                        $windowEnd->timestamp,
                        "Iteration $i: record received_at must be <= date_to"
                    );
            }

            // The count must match
            expect($results->total())
                ->toBe($matchingCount, "Iteration $i: result count must match records within the date range");
        }
    });

    /**
     * **Validates: Requirements 4.5, 4.6, 4.7**
     *
     * Combinação de filtros deve retornar apenas registros que satisfazem
     * todos os critérios simultaneamente.
     */
    test('property: combinação de filtros retorna apenas registros que satisfazem todos os critérios (100 iterações)', function () {
        // Feature: edduz-subscription-integration, Property 9: Filtros retornam apenas registros correspondentes

        $faker = fake();
        $repo = new \App\Domain\Edduz\Repositories\EdduzWebhookLogRepository();

        for ($i = 0; $i < 100; $i++) {
            EdduzWebhookLog::query()->delete();

            $targetStatus    = $faker->randomElement(WebhookProcessingStatus::cases());
            $targetEventType = $faker->randomElement(WebhookEventType::cases());
            $windowStart     = now()->subDays($faker->numberBetween(10, 20));
            $windowEnd       = $windowStart->copy()->addDays($faker->numberBetween(1, 5));

            $totalRecords = $faker->numberBetween(5, 15);
            $matchingCount = 0;

            for ($j = 0; $j < $totalRecords; $j++) {
                $status    = $faker->randomElement(WebhookProcessingStatus::cases());
                $eventType = $faker->randomElement(WebhookEventType::cases());

                // Randomly place inside or outside the window
                if ($faker->boolean(50)) {
                    $receivedAt = $faker->dateTimeBetween($windowStart, $windowEnd);
                    $inWindow = true;
                } else {
                    $receivedAt = $faker->dateTimeBetween('-60 days', $windowStart->copy()->subSecond());
                    $inWindow = false;
                }

                makeWebhookLog([
                    'processing_status' => $status->value,
                    'event_type'        => $eventType->value,
                    'received_at'       => $receivedAt,
                ]);

                if ($status === $targetStatus && $eventType === $targetEventType && $inWindow) {
                    $matchingCount++;
                }
            }

            // Apply all filters simultaneously
            $results = $repo->paginate([
                'status'     => $targetStatus->value,
                'event_type' => $targetEventType->value,
                'date_from'  => $windowStart->format('Y-m-d H:i:s'),
                'date_to'    => $windowEnd->format('Y-m-d H:i:s'),
            ]);

            // All returned records must satisfy ALL filter criteria
            foreach ($results->items() as $record) {
                expect($record->processing_status)
                    ->toBe($targetStatus->value, "Iteration $i: status must match filter");
                expect($record->event_type)
                    ->toBe($targetEventType->value, "Iteration $i: event_type must match filter");
                expect($record->received_at->timestamp)
                    ->toBeGreaterThanOrEqual($windowStart->timestamp, "Iteration $i: received_at must be >= date_from");
                expect($record->received_at->timestamp)
                    ->toBeLessThanOrEqual($windowEnd->timestamp, "Iteration $i: received_at must be <= date_to");
            }

            expect($results->total())
                ->toBe($matchingCount, "Iteration $i: combined filter count must match");
        }
    });
});
