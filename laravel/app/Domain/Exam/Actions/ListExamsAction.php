<?php

namespace App\Domain\Exam\Actions;

use App\Domain\Exam\Repositories\ExamRepository;
use Illuminate\Support\Collection;

class ListExamsAction
{
    public function __construct(
        private ExamRepository $repository
    ) {}
    
    public function execute(?string $careerId = null): Collection
    {
        if ($careerId) {
            return $this->repository->findByCareer($careerId);
        }
        
        return $this->repository->findAll();
    }
}
