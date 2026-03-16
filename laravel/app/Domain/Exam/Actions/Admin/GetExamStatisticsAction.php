<?php

declare(strict_types=1);

namespace App\Domain\Exam\Actions\Admin;

use App\Domain\Exam\DTOs\Admin\ExamStatisticsData;
use App\Domain\Exam\Models\Attempt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class GetExamStatisticsAction
{
    /**
     * @return ExamStatisticsData
     */
    public function execute(array $filters): ExamStatisticsData
    {
        $query = $this->buildQuery($filters);

        $result = $query->select([
            DB::raw('COUNT(*) as total'),
            DB::raw('COALESCE(AVG(attempts.score), 0) as avg_score'),
            DB::raw(
                'COALESCE(AVG('
                . 'CASE WHEN (SELECT COUNT(*) FROM questions WHERE questions.exam_id = attempts.exam_id) > 0 '
                . 'THEN (attempts.correct_answers * 100.0 / (SELECT COUNT(*) FROM questions WHERE questions.exam_id = attempts.exam_id)) '
                . 'ELSE 0 END'
                . '), 0) as avg_accuracy'
            ),
            DB::raw('COALESCE(AVG(attempts.duration_seconds / 60.0), 0) as avg_duration_minutes'),
        ])->first();

        return new ExamStatisticsData(
            totalAttempts: (int) $result->total,
            avgScore: round((float) $result->avg_score, 2),
            avgAccuracy: round((float) $result->avg_accuracy, 2),
            avgDurationMinutes: round((float) $result->avg_duration_minutes, 2),
        );
    }

    private function buildQuery(array $filters): Builder
    {
        $query = Attempt::query()
            ->join('users', 'users.id', '=', 'attempts.user_id')
            ->join('exams', 'exams.id', '=', 'attempts.exam_id')
            ->join('careers', 'careers.id', '=', 'exams.career_id')
            ->whereNotNull('attempts.finished_at');

        if (! empty($filters['search'])) {
            $searchLower = mb_strtolower(trim($filters['search']));

            $query->where(function (Builder $q) use ($searchLower) {
                $q->whereRaw('LOWER(users.name) LIKE ?', ['%' . $searchLower . '%'])
                  ->orWhereRaw('LOWER(exams.title) LIKE ?', ['%' . $searchLower . '%']);
            });
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
}
