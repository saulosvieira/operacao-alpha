<?php

declare(strict_types=1);

namespace App\Domain\Career\DTOs;

class NoticeData
{
    public function __construct(
        public int $id,
        public int $careerId,
        public string $title,
        public ?string $description,
        public ?string $examDate,
        public ?string $registrationStart,
        public ?string $registrationEnd,
        public ?string $pdfUrl,
        public bool $active,
        public string $createdAt,
        public string $updatedAt,
        public ?object $career = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'career_id' => $this->careerId,
            'title' => $this->title,
            'description' => $this->description,
            'exam_date' => $this->examDate,
            'registration_start' => $this->registrationStart,
            'registration_end' => $this->registrationEnd,
            'pdf_url' => $this->pdfUrl,
            'active' => $this->active,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
    
    // Propriedades para compatibilidade com views antigas
    public function __get(string $name): mixed
    {
        return match($name) {
            'titulo' => $this->title,
            'descricao' => $this->description,
            'data_publicacao' => $this->examDate,
            'carreira' => $this->career,
            default => null,
        };
    }
}
