<?php

namespace App\Domain\Exam\Repositories;

use App\Domain\Exam\Models\UserAnswer;
use App\Domain\Exam\DTOs\AnswerData;
use Illuminate\Support\Collection;

class AnswerRepository
{
    public function findById(string $id): ?AnswerData
    {
        $answer = UserAnswer::find($id);
        
        return $answer ? $this->toDTO($answer) : null;
    }
    
    public function findByAttempt(string $attemptId): Collection
    {
        return UserAnswer::where('attempt_id', $attemptId)
            ->with('question')
            ->orderBy('created_at')
            ->get()
            ->map(fn($answer) => $this->toDTO($answer));
    }
    
    public function findByAttemptAndQuestion(string $attemptId, string $questionId): ?AnswerData
    {
        $answer = UserAnswer::where('attempt_id', $attemptId)
            ->where('question_id', $questionId)
            ->first();
        
        return $answer ? $this->toDTO($answer) : null;
    }
    
    public function create(array $data): AnswerData
    {
        $answer = UserAnswer::create($data);
        
        return $this->toDTO($answer);
    }
    
    public function createOrUpdate(array $data): AnswerData
    {
        $answer = UserAnswer::updateOrCreate(
            [
                'attempt_id' => $data['attempt_id'],
                'question_id' => $data['question_id'],
            ],
            $data
        );
        
        return $this->toDTO($answer);
    }
    
    public function update(string $id, array $data): ?AnswerData
    {
        $answer = UserAnswer::find($id);
        
        if (!$answer) {
            return null;
        }
        
        $answer->update($data);
        
        return $this->toDTO($answer);
    }
    
    private function toDTO(UserAnswer $answer): AnswerData
    {
        return AnswerData::fromArray([
            'id' => $answer->id,
            'attempt_id' => $answer->attempt_id,
            'question_id' => $answer->question_id,
            'chosen_answer' => $answer->chosen_answer,
            'correct' => $answer->correct,
            'time_seconds' => $answer->time_seconds,
        ]);
    }
}
