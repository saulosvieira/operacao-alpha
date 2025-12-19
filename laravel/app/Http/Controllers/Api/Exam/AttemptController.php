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
        GetExamDetailsAction $examAction,
        \App\Domain\Exam\Repositories\AnswerRepository $answerRepository,
        \App\Domain\Exam\Repositories\QuestionRepository $questionRepository
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
        
        // Buscar detalhes do exame
        $exam = $examAction->execute($attempt->examId, false);
        
        // Check if immediate feedback mode - if so, include feedback for answered questions
        $isImmediateMode = $exam && $exam->feedback_mode === \App\Domain\Exam\Enums\FeedbackMode::IMMEDIATE;
        $includeAnswers = $attempt->finishedAt !== null;
        
        // Use ExamResource with proper answer visibility
        $examResource = (new ExamResource($exam))
            ->includeQuestions(true)
            ->showAnswers($includeAnswers);
        
        // Calculate remaining time
        $totalSeconds = $exam->time_limit_minutes * 60;
        $elapsedSeconds = now()->diffInSeconds($attempt->startedAt);
        $remainingSeconds = max(0, $totalSeconds - $elapsedSeconds);
        
        $responseData = [
            'attempt' => new AttemptResource($attempt),
            'exam' => $examResource,
            'initialTimerSeconds' => $remainingSeconds,
        ];
        
        // For immediate mode, include feedback data for answered questions
        if ($isImmediateMode && !$attempt->finishedAt) {
            $answers = $answerRepository->findByAttempt($id);
            $feedbackData = [];
            
            foreach ($answers as $answer) {
                $question = $questionRepository->findById($answer->questionId);
                if ($question) {
                    $feedbackData[$answer->questionId] = [
                        'isCorrect' => $answer->correct,
                        'correctAnswer' => $question->correctAnswer,
                        'explanation' => $question->explanation,
                        'chosenAnswer' => $answer->chosenAnswer,
                    ];
                }
            }
            
            $responseData['feedbackData'] = $feedbackData;
        }
        
        return response()->json([
            'data' => $responseData,
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
