<?php

namespace App\Http\Controllers\Api;

use App\Domain\Edduz\Actions\GenerateCheckoutUrlAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EdduzCheckoutController extends Controller
{
    /**
     * POST /api/edduz/checkout
     * Requer autenticação via Sanctum.
     */
    public function checkout(Request $request, GenerateCheckoutUrlAction $action): JsonResponse
    {
        $request->validate([
            'plan_id' => ['required', 'string'],
        ]);

        try {
            $url = $action->execute(
                (string) $request->user()->id,
                $request->input('plan_id')
            );

            return response()->json(['checkout_url' => $url], 200);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Falha inesperada ao gerar URL de checkout.', [
                'user_id' => $request->user()?->id,
                'plan_id' => $request->input('plan_id'),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => $e->getMessage()], 502);
        }
    }
}
