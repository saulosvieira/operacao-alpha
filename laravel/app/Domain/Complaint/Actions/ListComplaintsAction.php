<?php

namespace App\Domain\Complaint\Actions;

use App\Domain\Complaint\Repositories\ComplaintRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ListComplaintsAction
{
    public function __construct(
        private ComplaintRepository $repository
    ) {}

    public function execute(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }
}
