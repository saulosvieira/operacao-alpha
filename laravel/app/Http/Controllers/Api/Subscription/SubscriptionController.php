<?php

namespace App\Http\Controllers\Api\Subscription;

use App\Domain\Subscription\Actions\CancelSubscriptionAction;
use App\Domain\Subscription\Actions\CreateSubscriptionAction;
use App\Domain\Subscription\Actions\GetSubscriptionStatusAction;
use App\Domain\Subscription\Actions\ListPlansAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\CreateSubscriptionRequest;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    /**
     * List all available subscription plans
     */
    public function plans(ListPlansAction $action): JsonResponse
    {
        $plans = $action->execute();

        return response()->json([
            'data' => $plans->map(function ($plan) {
                return $plan->toArray();
            }),
        ]);
    }

    /**
     * Create a new subscription for the authenticated user
     */
    public function subscribe(
        CreateSubscriptionRequest $request,
        CreateSubscriptionAction $action
    ): JsonResponse {
        try {
            $subscription = $action->execute(
                auth()->id(),
                $request->input('plan_id'),
                $request->input('platform_id')
            );

            return response()->json([
                'message' => 'Assinatura criada com sucesso',
                'data' => $subscription->toArray(),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get the authenticated user's subscription status
     */
    public function status(GetSubscriptionStatusAction $action): JsonResponse
    {
        try {
            $subscription = $action->execute(auth()->id());

            return response()->json([
                'data' => $subscription->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Cancel the authenticated user's subscription
     */
    public function cancel(CancelSubscriptionAction $action): JsonResponse
    {
        try {
            $subscription = $action->execute(auth()->id());

            return response()->json([
                'message' => 'Assinatura cancelada com sucesso',
                'data' => $subscription->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
