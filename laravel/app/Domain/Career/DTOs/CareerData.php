<?php

declare(strict_types=1);

namespace App\Domain\Career\DTOs;

class CareerData
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public bool $active,
        public string $createdAt,
        public string $updatedAt,
        public string $slug = '',
        public int $examsCount = 0,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'active' => $this->active,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'slug' => $this->slug,
            'exams_count' => $this->examsCount,
        ];
    }
    
    // Propriedades para compatibilidade com views antigas
    public function __get(string $name): mixed
    {
        return match($name) {
            'nome' => $this->name,
            'descricao' => $this->description,
            'ativa' => $this->active,
            'simulados_count' => $this->examsCount,
            default => null,
        };
    }
}
