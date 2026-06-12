<?php

namespace App\Domain\Exam\Actions;

use App\Domain\Exam\Repositories\ExamRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListExamsAction
{
    public function __construct(
        private ExamRepository $repository
    ) {}
    
    public function execute(?string $careerId = null, int $page = 1, int $perPage = 20): LengthAwarePaginator
    {
        if ($careerId) {
            return $this->repository->paginateByCareer($careerId, $page, $perPage);
        }
        
        return $this->repository->paginate($page, $perPage);
    }
}
