<?php

namespace App\Http\Controllers\Api\Ranking;

use App\Http\Controllers\Controller;
use App\Domain\Ranking\Actions\GetRankingAction;
use App\Domain\Ranking\Actions\GetUserPositionAction;
use App\Domain\Ranking\Enums\RankingType;
use App\Http\Resources\Ranking\RankingResource;
use App\Http\Resources\Ranking\RankingEntryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RankingController extends Controller
{
    /**
     * Get ranking list
     * 
     * @param Request $request
     * @param GetRankingAction $action
     * @return RankingResource
     */
    public function index(Request $request, GetRankingAction $action): RankingResource
    {
        $request->validate([
            'type' => 'sometimes|string|in:daily,weekly,monthly',
            'career_id' => 'sometimes|string|exists:careers,id',
            'limit' => 'sometimes|integer|min:1|max:500',
        ]);

        $type = RankingType::from($request->input('type', 'weekly'));
        $careerId = $request->input('career_id');
        $limit = $request->integer('limit', 100);

        $ranking = $action->execute($type, $careerId, $limit);

        return new RankingResource($ranking);
    }

    /**
     * Get current user's position in ranking
     * 
     * @param Request $request
     * @param GetUserPositionAction $action
     * @return JsonResponse
     */
    public function myPosition(Request $request, GetUserPositionAction $action): JsonResponse
    {
        $request->validate([
            'type' => 'sometimes|string|in:daily,weekly,monthly',
            'career_id' => 'sometimes|string|exists:careers,id',
        ]);

        $type = RankingType::from($request->input('type', 'weekly'));
        $careerId = $request->input('career_id');
        $userId = $request->user()->id;

        $position = $action->execute($userId, $type, $careerId);

        if (!$position) {
            return response()->json([
                'message' => 'User not found in ranking. Complete some exams to appear in the ranking.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'data' => new RankingEntryResource($position),
        ]);
    }
}
