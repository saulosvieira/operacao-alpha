<?php

namespace App\Domain\Ranking\DTOs;

readonly class RankingEntryData
{
    public function __construct(
        public string $userId,
        public string $userName,
        public ?string $userAvatar,
        public float $score,
        public int $totalExams,
        public int $correctAnswers,
        public int $position,
        public ?string $careerId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            userName: $data['user_name'],
            userAvatar: $data['user_avatar'] ?? null,
            score: (float) $data['score'],
            totalExams: (int) $data['total_exams'],
            correctAnswers: (int) $data['correct_answers'],
            position: (int) $data['position'],
            careerId: $data['career_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'user_avatar' => $this->userAvatar,
            'score' => $this->score,
            'total_exams' => $this->totalExams,
            'correct_answers' => $this->correctAnswers,
            'position' => $this->position,
            'career_id' => $this->careerId,
        ];
    }
}
