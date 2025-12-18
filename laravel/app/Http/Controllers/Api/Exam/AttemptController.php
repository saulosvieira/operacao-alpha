<?php

namespace App\Http\Controllers\Api\Exam;

use App\Http\Controllers\Controller;
use App\Domain\Exam\Actions\StartAttemptAction;
use App\Domain\Exam\Actions\SubmitAnswerAction;
use App\Domain\Exam\Actions\FinishAttemptAction;
use App\Domain\Exam\Actions\GetExamDetailsAction;
use App\Domain\Exam\Repositories\AttemptRepository;
use App\Http\Requests\Exam\StartAttemptRequest;
use App\Http\Requests\Exam\SubmitAnswerRequest;
use App\Http\Resources\Exam\AttemptResource;
use App\Http\Resources\Exam\ExamResource;
use App\Http\Resources\Exam\StartAttemptResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttemptController extends Controller
{
    public function start(
        string $examId,
        StartAttemptRequest $request,
        StartAttemptAction $action
    ): JsonResponse {
        try {
            $result = $action->execute($examId, $request->user()->id);
            
            return response()->json([
                'data' => new StartAttemptResource($result),
                'message' => 'Attempt started successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
    public function show(
        string $id,
        AttemptRepository $repository,
        GetExamDetailsAction $examAction
    ): JsonResponse {
        $attempt = $repository->findById($id);
        
        if (!$attempt) {
            return response()->json([
                'message' => 'Attempt not found',
            ], 404);
        }
        
        // Verificar se o usuário é dono da tentativa
        if ($attempt->userId != auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }
        
        // Buscar detalhes do exame (sem respostas se ainda não finalizou)
        $includeAnswers = $attempt->finishedAt !== null;
        $exam = $examAction->execute($attempt->examId, $includeAnswers);
        
        // Use ExamResource with proper answer visibility
        $examResource = (new ExamResource($exam))
            ->includeQuestions(true)
            ->showAnswers($includeAnswers);
        
        return response()->json([
            'data' => [
                'attempt' => new AttemptResource($attempt),
                'exam' => $examResource,
            ],
        ]);
    }
    
    public function answer(
        string $id,
        SubmitAnswerRequest $request,
        SubmitAnswerAction $action
    ): JsonResponse {
        try {
            $result = $action->execute(
                $id,
                $request->input('question_id'),
                $request->input('answer'),
                $request->input('time_seconds', 0)
            );
            
            return response()->json([
                'data' => $result->toArray(),
                'message' => 'Answer submitted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
    public function finish(
        string $id,
        FinishAttemptAction $action
    ): JsonResponse {
        try {
            $result = $action->execute($id);
            
            return response()->json([
                'data' => $result->toArray(),
                'message' => 'Attempt finished successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
