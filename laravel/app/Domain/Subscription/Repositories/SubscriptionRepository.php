<?php

namespace App\Domain\Subscription\Repositories;

use App\Domain\Auth\Enums\SubscriptionStatus;
use App\Domain\Auth\Models\User;
use App\Domain\Subscription\DTOs\PlanData;
use App\Domain\Subscription\DTOs\SubscriptionData;
use App\Domain\Subscription\Enums\PlanType;
use Illuminate\Support\Collection;

class SubscriptionRepository
{
    /**
     * Get all available subscription plans
     */
    public function getPlans(): Collection
    {
        // For now, return hardcoded plans
        // In the future, this could come from a database table
        return collect([
            PlanData::fromArray([
                'id' => 'free',
                'name' => 'Plano Gratuito',
                'description' => 'Acesso limitado a simulados básicos',
                'type' => PlanType::FREE,
                'price' => 0.00,
                'duration_days' => 0,
                'features' => [
                    '3 simulados por mês',
                    'Acesso a questões básicas',
                    'Ranking público',
                ],
            ]),
            PlanData::fromArray([
                'id' => 'monthly',
                'name' => 'Plano Mensal',
                'description' => 'Acesso completo por 30 dias',
                'type' => PlanType::MONTHLY,
                'price' => 29.90,
                'duration_days' => 30,
                'features' => [
                    'Simulados ilimitados',
                    'Todas as questões disponíveis',
                    'Estatísticas detalhadas',
                    'Ranking premium',
                    'Suporte prioritário',
                ],
            ]),
            PlanData::fromArray([
                'id' => 'yearly',
                'name' => 'Plano Anual',
                'description' => 'Acesso completo por 365 dias com desconto',
                'type' => PlanType::YEARLY,
                'price' => 299.90,
                'duration_days' => 365,
                'features' => [
                    'Simulados ilimitados',
                    'Todas as questões disponíveis',
                    'Estatísticas detalhadas',
                    'Ranking premium',
                    'Suporte prioritário',
                    '2 meses grátis',
                ],
            ]),
        ]);
    }

    /**
     * Get plan by ID
     */
    public function getPlanById(string $planId): ?PlanData
    {
        return $this->getPlans()->firstWhere('id', $planId);
    }

    /**
     * Get user's subscription status
     */
    public function getUserSubscription(string $userId): SubscriptionData
    {
        $user = User::findOrFail($userId);

        return SubscriptionData::fromArray([
            'user_id' => $user->id,
            'status' => $user->subscription_status,
            'plan_type' => $this->determinePlanType($user),
            'expires_at' => $user->subscription_expires_at ? $user->subscription_expires_at->toIso8601String() : null,
            'platform_id' => $user->subscription_platform_id,
        ]);
    }

    /**
     * Create or update user subscription
     */
    public function createSubscription(
        string $userId,
        PlanType $planType,
        string $platformId
    ): SubscriptionData {
        $user = User::findOrFail($userId);
        $plan = $this->getPlans()->firstWhere('type.value', $planType->value);

        if (!$plan) {
            throw new \Exception('Plan not found');
        }

        $expiresAt = $plan->durationDays > 0
            ? now()->addDays($plan->durationDays)
            : null;

        $user->update([
            'subscription_status' => SubscriptionStatus::ACTIVE,
            'subscription_expires_at' => $expiresAt,
            'subscription_platform_id' => $platformId,
        ]);

        return SubscriptionData::fromArray([
            'user_id' => $user->id,
            'status' => SubscriptionStatus::ACTIVE,
            'plan_type' => $planType,
            'expires_at' => $expiresAt ? $expiresAt->toIso8601String() : null,
            'platform_id' => $platformId,
        ]);
    }

    /**
     * Cancel user subscription
     */
    public function cancelSubscription(string $userId): SubscriptionData
    {
        $user = User::findOrFail($userId);

        $user->update([
            'subscription_status' => SubscriptionStatus::CANCELLED,
        ]);

        return SubscriptionData::fromArray([
            'user_id' => $user->id,
            'status' => SubscriptionStatus::CANCELLED,
            'plan_type' => $this->determinePlanType($user),
            'expires_at' => $user->subscription_expires_at ? $user->subscription_expires_at->toIso8601String() : null,
            'platform_id' => $user->subscription_platform_id,
        ]);
    }

    /**
     * Determine plan type from user data
     */
    private function determinePlanType(User $user): ?string
    {
        if ($user->subscription_status === SubscriptionStatus::INACTIVE) {
            return PlanType::FREE;
        }

        if (!$user->subscription_expires_at) {
            return PlanType::FREE;
        }

        $daysRemaining = now()->diffInDays($user->subscription_expires_at, false);

        if ($daysRemaining <= 0) {
            return PlanType::FREE;
        }

        // Determine if monthly or yearly based on remaining days
        if ($daysRemaining > 180) {
            return PlanType::YEARLY;
        }

        return PlanType::MONTHLY;
    }
}
