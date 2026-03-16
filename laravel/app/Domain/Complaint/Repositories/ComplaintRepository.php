<?php

namespace App\Domain\Complaint\Repositories;

use App\Domain\Complaint\Enums\ComplaintStatus;
use App\Domain\Complaint\Models\Complaint;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ComplaintRepository
{
    public function create(array $data): Complaint
    {
        return Complaint::create($data);
    }

    public function update(int $id, array $data): Complaint
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->update($data);

        return $complaint->fresh();
    }

    public function findOrFail(int $id): Complaint
    {
        return Complaint::findOrFail($id);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Complaint::query()
            ->join('questions', 'complaints.question_id', '=', 'questions.id')
            ->join('exams', 'questions.exam_id', '=', 'exams.id')
            ->join('users', 'complaints.admin_id', '=', 'users.id')
            ->select(
                'complaints.*',
                'questions.question_number',
                'exams.title as exam_title',
                'users.name as admin_name'
            )
            ->orderBy('complaints.created_at', 'desc');

        if (!empty($filters['status'])) {
            $query->where('complaints.status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('complaints.type', $filters['type']);
        }

        if (!empty($filters['priority'])) {
            $query->where('complaints.priority', $filters['priority']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Count pending complaints (OPEN or IN_REVIEW) for questions belonging to a given exam.
     */
    public function countPendingByExamId(int $examId): int
    {
        return Complaint::join('questions', 'complaints.question_id', '=', 'questions.id')
            ->where('questions.exam_id', $examId)
            ->whereIn('complaints.status', [
                ComplaintStatus::OPEN->value,
                ComplaintStatus::IN_REVIEW->value,
            ])
            ->count();
    }

    /**
     * Count pending complaints (OPEN or IN_REVIEW) grouped by question_id.
     *
     * @param  array  $questionIds
     * @return Collection  Keyed by question_id with the count as value.
     */
    public function countPendingByQuestionIds(array $questionIds): Collection
    {
        if (empty($questionIds)) {
            return collect();
        }

        return Complaint::whereIn('question_id', $questionIds)
            ->whereIn('status', [
                ComplaintStatus::OPEN->value,
                ComplaintStatus::IN_REVIEW->value,
            ])
            ->selectRaw('question_id, count(*) as pending_count')
            ->groupBy('question_id')
            ->pluck('pending_count', 'question_id');
    }
}
