<?php

declare(strict_types=1);

namespace App\Domain\Exam\Actions\Admin;

use App\Domain\Complaint\Enums\ComplaintStatus;
use App\Domain\Exam\DTOs\Admin\AttemptListItemData;
use App\Domain\Exam\Models\Attempt;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class ListAttemptsForAdminAction
{
    public function execute(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->buildQuery($filters);

        return $query
            ->orderBy('attempts.finished_at', 'desc')
            ->paginate($perPage)
            ->through(fn ($row) => new AttemptListItemData(
                id: (int) $row->id,
                userName: $row->user_name,
                examTitle: $row->exam_title,
                careerName: $row->career_name,
                score: (float) $row->score,
                correctAnswers: (int) $row->correct_answers,
                totalQuestions: (int) $row->total_questions,
                durationMinutes: (int) ceil((int) $row->duration_seconds / 60),
                finishedAt: $row->finished_at,
                pendingComplaints: (int) $row->pending_complaints,
            ));
    }

    public function count(array $filters): int
    {
        return $this->buildQuery($filters)->count();
    }

    private function buildQuery(array $filters): Builder
    {
        $pendingStatuses = [
            ComplaintStatus::OPEN->value,
            ComplaintStatus::IN_REVIEW->value,
        ];

        $query = Attempt::query()
            ->select([
                'attempts.id',
                'users.name as user_name',
                'exams.title as exam_title',
                'careers.name as career_name',
                'attempts.score',
                'attempts.correct_answers',
                DB::raw('(SELECT COUNT(*) FROM questions WHERE questions.exam_id = exams.id) as total_questions'),
                'attempts.duration_seconds',
                'attempts.finished_at',
                DB::raw(
                    "(SELECT COUNT(*) FROM complaints"
                    . " INNER JOIN questions ON questions.id = complaints.question_id"
                    . " WHERE questions.exam_id = exams.id"
                    . " AND complaints.status IN ('" . implode("','", $pendingStatuses) . "'))"
                    . " as pending_complaints"
                ),
            ])
            ->join('users', 'users.id', '=', 'attempts.user_id')
            ->join('exams', 'exams.id', '=', 'attempts.exam_id')
            ->join('careers', 'careers.id', '=', 'exams.career_id')
            ->whereNotNull('attempts.finished_at');

        if (! empty($filters['search'])) {
            $this->applySearch($query, $filters['search']);
        }

        if (! empty($filters['exam_id'])) {
            $query->where('attempts.exam_id', $filters['exam_id']);
        }

        if (! empty($filters['career_id'])) {
            $query->where('exams.career_id', $filters['career_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('attempts.finished_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('attempts.finished_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    private function applySearch(Builder $query, string $search): void
    {
        $searchLower = mb_strtolower(trim($search));

        $query->where(function (Builder $q) use ($searchLower) {
            $q->whereRaw('LOWER(users.name) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('LOWER(exams.title) LIKE ?', ['%' . $searchLower . '%']);
        });
    }
}
