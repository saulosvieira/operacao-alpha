<?php

namespace App\Domain\Exam\Repositories;

use App\Domain\Exam\Models\Question;
use App\Domain\Exam\DTOs\QuestionData;
use Illuminate\Support\Collection;

class QuestionRepository
{
    public function findById(string $id): ?QuestionData
    {
        $question = Question::find($id);
        
        return $question ? $this->toDTO($question) : null;
    }
    
    public function findByExam(string $examId): Collection
    {
        return Question::where('exam_id', $examId)
            ->orderBy('question_number')
            ->get()
            ->map(fn($question) => $this->toDTO($question));
    }
    
    public function create(array $data): QuestionData
    {
        $question = Question::create($data);
        
        return $this->toDTO($question);
    }
    
    public function update(string $id, array $data): ?QuestionData
    {
        $question = Question::find($id);
        
        if (!$question) {
            return null;
        }
        
        $question->update($data);
        
        return $this->toDTO($question);
    }
    
    public function delete(string $id): bool
    {
        $question = Question::find($id);
        
        if (!$question) {
            return false;
        }
        
        return $question->delete();
    }
    
    private function toDTO(Question $question): QuestionData
    {
        return QuestionData::fromArray([
            'id' => $question->id,
            'exam_id' => $question->exam_id,
            'question_number' => $question->question_number,
            'statement' => $question->statement,
            'statement_image' => $question->statement_image,
            'option_a' => $question->option_a,
            'option_a_image' => $question->option_a_image,
            'option_b' => $question->option_b,
            'option_b_image' => $question->option_b_image,
            'option_c' => $question->option_c,
            'option_c_image' => $question->option_c_image,
            'option_d' => $question->option_d,
            'option_d_image' => $question->option_d_image,
            'option_e' => $question->option_e,
            'option_e_image' => $question->option_e_image,
            'correct_answer' => $question->correct_answer,
            'explanation' => $question->explanation,
        ]);
    }
}
