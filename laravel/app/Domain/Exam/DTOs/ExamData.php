<?php

namespace App\Domain\Exam\DTOs;

class ExamData
{
    public function __construct(
        public string $id,
        public string $careerId,
        public string $title,
        public ?string $description,
        public int $timeLimitMinutes,
        public bool $active,
        public int $totalQuestions,
        public ?object $career = null,
    ) {}
    
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            careerId: $data['career_id'],
            title: $data['title'],
            description: $data['description'] ?? null,
            timeLimitMinutes: $data['time_limit_minutes'],
            active: $data['active'],
            totalQuestions: $data['total_questions'] ?? 0,
            career: isset($data['career']) ? (object)$data['career'] : null,
        );
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'careerId' => $this->careerId,
            'title' => $this->title,
            'description' => $this->description,
            'timeLimitMinutes' => $this->timeLimitMinutes,
            'active' => $this->active,
            'totalQuestions' => $this->totalQuestions,
        ];
    }
    
    // Propriedades para compatibilidade com views antigas
    public function __get(string $name): mixed
    {
        return match($name) {
            'titulo' => $this->title,
            'descricao' => $this->description,
            'tempo_limite_minutos' => $this->timeLimitMinutes,
            'ativo' => $this->active,
            'questoes_count' => $this->totalQuestions,
            'carreira' => $this->career,
            default => null,
        };
    }
}
