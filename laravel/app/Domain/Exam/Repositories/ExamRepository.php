<?php

namespace App\Domain\Exam\Repositories;

use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\DTOs\ExamData;
use Illuminate\Support\Collection;

class ExamRepository
{
    public function findAll(): Collection
    {
        return Exam::with('career')
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($exam) => $this->toDTO($exam));
    }
    
    public function findById(string $id): ?ExamData
    {
        $exam = Exam::with(['career', 'questions'])->find($id);
        
        return $exam ? $this->toDTO($exam) : null;
    }
    
    public function findByCareer(string $careerId): Collection
    {
        return Exam::where('career_id', $careerId)
            ->where('active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($exam) => $this->toDTO($exam));
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
    
    private function toDTO(Exam $exam): ExamData
    {
        $career = $exam->relationLoaded('career') && $exam->career 
            ? (object)['id' => $exam->career->id, 'nome' => $exam->career->name]
            : null;
            
        return ExamData::fromArray([
            'id' => $exam->id,
            'career_id' => $exam->career_id,
            'title' => $exam->title,
            'description' => $exam->description,
            'time_limit_minutes' => $exam->time_limit_minutes,
            'active' => $exam->active,
            'total_questions' => $exam->questions_count ?? $exam->questions->count(),
            'career' => $career,
        ]);
    }
}
