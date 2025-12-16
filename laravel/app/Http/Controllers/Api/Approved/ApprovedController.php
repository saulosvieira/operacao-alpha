<?php

namespace App\Http\Controllers\Api\Approved;

use App\Domain\Approved\Actions\ListApprovedAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Approved\ApprovedResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApprovedController extends Controller
{
    /**
     * List all approved candidates with optional filters
     *
     * Query parameters:
     * - career_id: Filter by career ID
     * - year: Filter by year
     */
    public function index(Request $request, ListApprovedAction $action): JsonResponse
    {
        $careerId = $request->query('career_id') ? (int) $request->query('career_id') : null;
        $year = $request->query('year') ? (int) $request->query('year') : null;

        $approved = $action->execute($careerId, $year);

        return response()->json([
            'data' => ApprovedResource::collection($approved),
        ]);
    }
}
