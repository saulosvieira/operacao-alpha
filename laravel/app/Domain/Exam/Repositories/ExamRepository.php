<?php

namespace App\Domain\Exam\Repositories;

use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\DTOs\ExamData;
use Illuminate\Support\Collection;

class ExamRepository
{
    public function findAll(): Collection
    {
        $self = $this;
        return Exam::with('career')
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($exam) use ($self) {
                return $self->toDTO($exam);
            });
    }
    
    public function findById(string $id): ?ExamData
    {
        $exam = Exam::with(['career', 'questions'])->find($id);
        
        return $exam ? $this->toDTO($exam) : null;
    }
    
    /**
     * Find an exam by ID with questions eager loaded.
     * Returns the Exam model directly for use with Resources.
     *
     * @param string $id
     * @return Exam|null
     */
    public function findByIdWithQuestions(string $id): ?Exam
    {
        return Exam::with(['questions' => function ($query) {
            $query->orderBy('question_number');
        }])->find($id);
    }
    
    public function findByCareer(string $careerId): Collection
    {
        $self = $this;
        return Exam::where('career_id', $careerId)
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($exam) use ($self) {
                return $self->toDTO($exam);
            });
    }
    
    public function create(array $data): ExamData
    {
        $exam = Exam::create($data);
        $exam->load(['career', 'questions']);
        
        return $this->toDTO($exam);
    }
    
    public function update(string $id, array $data): ?ExamData
    {
        $exam = Exam::find($id);
        
        if (!$exam) {
            return null;
        }
        
        $exam->update($data);
        $exam->load(['career', 'questions']);
        
        return $this->toDTO($exam);
    }
    
    public function delete(string $id): bool
    {
        $exam = Exam::find($id);
        
        if (!$exam) {
            return false;
        }
        
        return $exam->delete();
    }
    
    public function toDTO(Exam $exam): ExamData
    {
        $career = $exam->relationLoaded('career') && $exam->career 
            ? (object)['id' => $exam->career->id, 'nome' => $exam->career->name]
            : null;
        
        $feedbackMode = $exam->feedback_mode ? $exam->feedback_mode->value : 'final';
            
        return ExamData::fromArray([
            'id' => $exam->id,
            'career_id' => $exam->career_id,
            'title' => $exam->title,
            'description' => $exam->description,
            'time_limit_minutes' => $exam->time_limit_minutes,
            'active' => $exam->active,
            'is_free' => $exam->is_free,
            'total_questions' => $exam->questions_count ?? $exam->questions->count(),
            'career' => $career,
            'feedback_mode' => $feedbackMode,
        ]);
    }
}
