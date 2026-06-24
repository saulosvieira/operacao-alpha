<?php

// Feature: edduz-subscription-integration, Property 1: Parâmetros do checkout contêm identificadores corretos

use App\Domain\Edduz\Actions\GenerateCheckoutUrlAction;
use App\Domain\Subscription\Repositories\SubscriptionRepository;
use App\Services\EdduzApiClient;
use Faker\Factory as Faker;
use Mockery\MockInterface;

/**
 * Validates: Requirements 1.1, 1.4
 *
 * Property 1: Parâmetros do checkout contêm identificadores corretos
 *
 * For any valid user and paid plan, the checkout request sent to the Edduz API
 * must contain the user identifier and the plan identifier.
 */

test('Property 1: userId e planId são passados corretamente para createCheckoutSession()', function () {
    $faker = Faker::create();
    $iterations = 100;

    for ($i = 0; $i < $iterations; $i++) {
        $userId = (string) $faker->numberBetween(1, 999999);
        // Paid plans: anything that is not 'free' or 'gratuito'
        $paidPlans = ['monthly', 'yearly', $faker->slug(2), $faker->uuid()];
        $planId = $paidPlans[array_rand($paidPlans)];

        $capturedUserId = null;
        $capturedPlanId = null;
        $expectedUrl = 'https://checkout.edduz.com/' . $faker->uuid();

        /** @var EdduzApiClient&MockInterface $clientMock */
        $clientMock = Mockery::mock(EdduzApiClient::class);
        $clientMock
            ->shouldReceive('isConfigured')
            ->once()
            ->andReturn(true);
        $clientMock
            ->shouldReceive('createCheckoutSession')
            ->once()
            ->withArgs(function (string $uid, string $pid) use (&$capturedUserId, &$capturedPlanId) {
                $capturedUserId = $uid;
                $capturedPlanId = $pid;
                return true;
            })
            ->andReturn($expectedUrl);

        $subscriptionRepoMock = Mockery::mock(SubscriptionRepository::class);

        $action = new GenerateCheckoutUrlAction($clientMock, $subscriptionRepoMock);
        $result = $action->execute($userId, $planId);

        expect($capturedUserId)->toBe($userId,
            "userId passado à API deve ser '{$userId}' (iteração {$i})"
        );
        expect($capturedPlanId)->toBe($planId,
            "planId passado à API deve ser '{$planId}' (iteração {$i})"
        );
        expect($result)->toBe($expectedUrl,
            "A URL retornada deve ser a URL de checkout da API (iteração {$i})"
        );

        Mockery::close();
    }
});

test('Property 10: rejeita checkout quando integração Edduz não está configurada', function () {
    $faker = Faker::create();
    $userId = (string) $faker->numberBetween(1, 999999);
    $planId = 'monthly';

    $clientMock = Mockery::mock(EdduzApiClient::class);
    $clientMock
        ->shouldReceive('isConfigured')
        ->once()
        ->andReturn(false);
    $clientMock->shouldNotReceive('createCheckoutSession');

    $subscriptionRepoMock = Mockery::mock(SubscriptionRepository::class);

    $action = new GenerateCheckoutUrlAction($clientMock, $subscriptionRepoMock);

    try {
        $action->execute($userId, $planId);
    } catch (\InvalidArgumentException $e) {
        expect($e->getMessage())->toContain('Integração Edduz não está configurada');
        Mockery::close();
        return;
    }

    $this->fail('Deveria ter lançado InvalidArgumentException quando a integração não está configurada.');
});

test('Property 1: planos gratuitos (free/gratuito) são rejeitados com InvalidArgumentException', function () {
    $faker = Faker::create();
    $iterations = 100;

    $freePlanIds = ['free', 'gratuito', 'FREE', 'GRATUITO', 'Free', 'Gratuito'];

    for ($i = 0; $i < $iterations; $i++) {
        $userId = (string) $faker->numberBetween(1, 999999);
        $planId = $freePlanIds[$i % count($freePlanIds)];

        $clientMock = Mockery::mock(EdduzApiClient::class);
        $clientMock->shouldNotReceive('createCheckoutSession');

        $subscriptionRepoMock = Mockery::mock(SubscriptionRepository::class);

        $action = new GenerateCheckoutUrlAction($clientMock, $subscriptionRepoMock);

        $threw = false;
        try {
            $action->execute($userId, $planId);
        } catch (\InvalidArgumentException $e) {
            $threw = true;
        }

        expect($threw)->toBeTrue(
            "Plano '{$planId}' deve lançar InvalidArgumentException (iteração {$i})"
        );

        Mockery::close();
    }
});
