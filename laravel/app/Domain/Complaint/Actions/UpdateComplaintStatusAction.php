<?php

namespace App\Domain\Complaint\Actions;

use App\Domain\Complaint\Enums\ComplaintStatus;
use App\Domain\Complaint\Models\Complaint;
use App\Domain\Complaint\Repositories\ComplaintRepository;
use Carbon\Carbon;

class UpdateComplaintStatusAction
{
    public function __construct(
        private ComplaintRepository $repository
    ) {}

    public function execute(
        int $complaintId,
        ComplaintStatus $status,
        ?string $resolutionNote = null
    ): Complaint {
        $data = [
            'status' => $status->value,
        ];

        if ($resolutionNote !== null) {
            $data['resolution_note'] = $resolutionNote;
        }

        if (in_array($status, [ComplaintStatus::RESOLVED, ComplaintStatus::REJECTED])) {
            $data['resolved_at'] = Carbon::now();
        }

        return $this->repository->update($complaintId, $data);
    }
}
