<?php

namespace App\Domain\Complaint\Actions;

use App\Domain\Complaint\Enums\ComplaintPriority;
use App\Domain\Complaint\Enums\ComplaintStatus;
use App\Domain\Complaint\Enums\ComplaintType;
use App\Domain\Complaint\Models\Complaint;
use App\Domain\Complaint\Repositories\ComplaintRepository;
use App\Domain\Exam\Models\Question;

class CreateComplaintAction
{
    public function __construct(
        private ComplaintRepository $repository
    ) {}

    public function execute(
        int $questionId,
        int $adminId,
        ComplaintType $type,
        string $description,
        ComplaintPriority $priority
    ): Complaint {
        Question::findOrFail($questionId);

        return $this->repository->create([
            'question_id' => $questionId,
            'admin_id' => $adminId,
            'type' => $type->value,
            'description' => $description,
            'priority' => $priority->value,
            'status' => ComplaintStatus::OPEN->value,
        ]);
    }
}
