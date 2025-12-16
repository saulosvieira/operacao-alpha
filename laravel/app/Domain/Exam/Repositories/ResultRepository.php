<?php

namespace App\Domain\Exam\Repositories;

use App\Domain\Exam\Models\ExamResult;
use App\Domain\Exam\DTOs\ResultData;
use Illuminate\Support\Collection;

class ResultRepository
{
    public function findById(string $id): ?ResultData
    {
        $result = ExamResult::find($id);
        
        return $result ? $this->toDTO($result) : null;
    }
    
    public function findByAttempt(string $attemptId): ?ResultData
    {
        $result = ExamResult::where('attempt_id', $attemptId)->first();
        
        return $result ? $this->toDTO($result) : null;
    }
    
    public function findByUser(string $userId): Collection
    {
        return ExamResult::where('user_id', $userId)
            ->with(['exam', 'attempt'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($result) => $this->toDTO($result));
    }
    
    public function create(array $data): ResultData
    {
        $result = ExamResult::create($data);
        
        return $this->toDTO($result);
    }
    
    private function toDTO(ExamResult $result): ResultData
    {
        return ResultData::fromArray([
            'total_questions' => $result->total_questions,
            'total_correct' => $result->correct_answers,
            'final_score' => $result->score,
            'total_time_seconds' => $result->total_time_seconds,
        ]);
    }
}
