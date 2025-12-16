<?php

namespace App\Http\Controllers\Api\Performance;

use App\Http\Controllers\Controller;
use App\Domain\Performance\Actions\GetStatisticsAction;
use App\Domain\Performance\Actions\GetHistoryAction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PerformanceController extends Controller
{
    public function statistics(Request $request, GetStatisticsAction $action): JsonResponse
    {
        $userId = $request->user()->id;
        $statistics = $action->execute($userId);

        return response()->json([
            'data' => $statistics->toArray(),
        ]);
    }

    public function history(Request $request, GetHistoryAction $action): JsonResponse
    {
        $userId = $request->user()->id;
        $limit = $request->query('limit', 20);
        
        // Validate limit
        $limit = min(max((int) $limit, 1), 100); // Between 1 and 100

        $history = $action->execute($userId, $limit);

        return response()->json([
            'data' => $history->map(fn($item) => $item->toArray())->values(),
        ]);
    }
}
