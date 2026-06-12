<?php

namespace App\Http\Controllers\Api\Exam;

use App\Http\Controllers\Controller;
use App\Domain\Exam\Actions\ListExamsAction;
use App\Domain\Exam\Actions\GetExamDetailsAction;
use App\Http\Resources\Exam\ExamResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExamController extends Controller
{
    public function index(Request $request, ListExamsAction $action): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 20);
        $perPage = max(1, min($perPage, 100)); // clamp between 1 and 100

        $page = (int) $request->query('page', 1);
        $page = max(1, $page);

        $paginator = $action->execute(
            careerId: $request->query('career_id'),
            page: $page,
            perPage: $perPage,
        );

        return response()->json([
            'data' => ExamResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
    
    public function show(string $id, GetExamDetailsAction $action): JsonResponse
    {
        $exam = $action->execute($id, includeAnswers: false);
        
        if (!$exam) {
            return response()->json([
                'message' => 'Exam not found'
            ], 404);
        }
        
        return response()->json([
            'data' => new ExamResource($exam),
        ]);
    }
}
