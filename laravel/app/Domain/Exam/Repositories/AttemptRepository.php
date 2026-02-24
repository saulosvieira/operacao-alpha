<?php

namespace App\Domain\Exam\Repositories;

use App\Domain\Exam\Models\Attempt;
use App\Domain\Exam\DTOs\AttemptData;
use Illuminate\Support\Collection;

class AttemptRepository
{
    public function findById(string $id): ?AttemptData
    {
        $attempt = Attempt::with(['exam', 'user', 'answers'])->find($id);
        
        return $attempt ? $this->toDTO($attempt) : null;
    }
    
    public function findByUser(string $userId): Collection
    {
        return Attempt::where('user_id', $userId)
            ->with(['exam', 'answers'])
            ->orderBy('started_at', 'desc')
            ->get()
            ->map(fn($attempt) => $this->toDTO($attempt));
    }
    
    public function findByExam(string $examId): Collection
    {
        return Attempt::where('exam_id', $examId)
            ->with(['user', 'answers'])
            ->orderBy('started_at', 'desc')
            ->get()
            ->map(fn($attempt) => $this->toDTO($attempt));
    }
    
    public function findActiveByUser(string $userId): ?AttemptData
    {
        $attempt = Attempt::where('user_id', $userId)
            ->whereNull('finished_at')
            ->with(['exam', 'answers'])
            ->first();
        
        return $attempt ? $this->toDTO($attempt) : null;
    }
    
    public function findActiveByUserAndExam(string $userId, string $examId): ?AttemptData
    {
        $attempt = Attempt::where('user_id', $userId)
            ->where('exam_id', $examId)
            ->whereNull('finished_at')
            ->with(['exam', 'answers'])
            ->first();
        
        if (!$attempt) {
            return null;
        }
        
        // Verificar se a tentativa expirou
        // Usar floor() para truncar frações de segundo e evitar problemas de precisão
        $elapsedSeconds = (int) floor($attempt->started_at->diffInSeconds(now()));
        $timeLimitSeconds = $attempt->exam->time_limit_minutes * 60;
        
        if ($elapsedSeconds > $timeLimitSeconds) {
            // Tentativa expirada - auto-finalizar
            $this->autoFinishExpiredAttempt($attempt, $timeLimitSeconds);
            return null;
        }
        
        return $this->toDTO($attempt);
    }
    
    /**
     * Auto-finaliza uma tentativa expirada
     */
    private function autoFinishExpiredAttempt(Attempt $attempt, int $timeLimitSeconds): void
    {
        $finishedAt = now();
        
        // Calcular resultado parcial com as respostas já submetidas
        $totalQuestions = $attempt->exam->questions()->count();
        
        // MySQL usa 'correct', SQLite usa 'is_correct'
        $correctAnswers = \DB::getDriverName() === 'sqlite' 
            ? $attempt->answers()->where('is_correct', true)->count()
            : $attempt->answers()->where('correct', true)->count();
        
        $score = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
        
        // Atualizar tentativa com tempo limite completo como duração
        $attempt->update([
            'finished_at' => $finishedAt,
            'duration_seconds' => $timeLimitSeconds,
            'correct_answers' => $correctAnswers,
            'score' => $score,
        ]);
        
        // Criar registro em exam_results para manter histórico
        \DB::table('exam_results')->insert([
            'user_id' => $attempt->user_id,
            'exam_id' => $attempt->exam_id,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'score' => $score,
            'total_time_seconds' => $timeLimitSeconds,
            'finished_at' => $finishedAt,
            'created_at' => $finishedAt,
            'updated_at' => $finishedAt,
        ]);
        
        // Log para debugging e monitoramento
        \Log::info('Auto-finalized expired attempt', [
            'attempt_id' => $attempt->id,
            'user_id' => $attempt->user_id,
            'exam_id' => $attempt->exam_id,
            'elapsed_time' => $attempt->started_at->diffInSeconds($finishedAt),
            'time_limit' => $timeLimitSeconds,
        ]);
    }
    
    public function create(array $data): AttemptData
    {
        $attempt = Attempt::create($data);
        $attempt->load(['exam', 'user', 'answers']);
        
        return $this->toDTO($attempt);
    }
    
    public function update(string $id, array $data): ?AttemptData
    {
        $attempt = Attempt::find($id);
        
        if (!$attempt) {
            return null;
        }
        
        $attempt->update($data);
        $attempt->load(['exam', 'user', 'answers']);
        
        return $this->toDTO($attempt);
    }
    
    private function toDTO(Attempt $attempt): AttemptData
    {
        // Build answers map if answers relation is loaded
        $answersMap = null;
        if ($attempt->relationLoaded('answers') && $attempt->answers->isNotEmpty()) {
            $answersMap = [];
            foreach ($attempt->answers as $answer) {
                $answersMap[(string) $answer->question_id] = $answer->chosen_answer;
            }
        }
        
        return AttemptData::fromArray([
            'id' => $attempt->id,
            'user_id' => $attempt->user_id,
            'exam_id' => $attempt->exam_id,
            'started_at' => $attempt->started_at->toIso8601String(),
            'finished_at' => $attempt->finished_at?->toIso8601String(),
            'duration_seconds' => $attempt->duration_seconds,
            'correct_answers' => $attempt->correct_answers,
            'score' => $attempt->score,
            'answers' => $answersMap,
        ]);
    }
}
