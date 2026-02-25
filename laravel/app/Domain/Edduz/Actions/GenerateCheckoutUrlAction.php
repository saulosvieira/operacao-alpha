<?php

namespace App\Domain\Edduz\Actions;

use App\Domain\Subscription\Repositories\SubscriptionRepository;
use App\Services\EdduzApiClient;
use Illuminate\Support\Facades\Log;

class GenerateCheckoutUrlAction
{
    public function __construct(
        private EdduzApiClient $client,
        private SubscriptionRepository $subscriptionRepo,
    ) {}

    /**
     * Valida o plano, gera a URL de checkout e retorna.
     *
     * @throws \InvalidArgumentException se o plano for gratuito.
     * @throws \Exception se a API da Edduz falhar.
     */
    public function execute(string $userId, string $planId): string
    {
        if (in_array(strtolower($planId), ['free', 'gratuito'], strict: true)) {
            throw new \InvalidArgumentException(
                "O plano '{$planId}' é gratuito e não pode ser processado via checkout."
            );
        }

        try {
            return $this->client->createCheckoutSession($userId, $planId);
        } catch (\Throwable $e) {
            Log::error('Falha ao gerar URL de checkout via Edduz.', [
                'user_id' => $userId,
                'plan_id' => $planId,
                'error'   => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
