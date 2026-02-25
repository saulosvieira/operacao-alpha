<?php

namespace App\Http\Controllers\Api;

use App\Domain\Edduz\Actions\ProcessWebhookAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EdduzWebhookController extends Controller
{
    /**
     * POST /api/webhooks/edduz
     * Endpoint público (sem auth middleware).
     */
    public function handle(Request $request, ProcessWebhookAction $action): JsonResponse
    {
        $payload = $request->all();
        $ip = $request->ip();
        $headers = $request->headers->all();
        $token = $request->header('X-Edduz-Token', '') ?: $request->query('token', '');

        $result = $action->execute($payload, $ip, $headers, $token);

        return response()->json(['message' => $result->message], $result->httpStatus);
    }
}
