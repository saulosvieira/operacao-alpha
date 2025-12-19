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
        $exams = $action->execute($request->query('career_id'));
        
        return response()->json([
            'data' => ExamResource::collection($exams),
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
