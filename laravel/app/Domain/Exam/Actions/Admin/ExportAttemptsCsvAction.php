<?php

declare(strict_types=1);

namespace App\Domain\Exam\Actions\Admin;

use App\Domain\Complaint\Enums\ComplaintStatus;
use App\Domain\Exam\Models\Attempt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ExportAttemptsCsvAction
{
    public function execute(array $filters): StreamedResponse
    {
        $query = $this->buildQuery($filters);

        $headers = [
            'ID',
            'Nome do Usuário',
            'E-mail',
            'Título do Simulado',
            'Carreira',
            'Nota',
            'Acertos',
            'Total Questões',
            'Duração (min)',
            'Data Finalização',
        ];

        $response = new StreamedResponse(function () use ($query, $headers) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, $headers, ',');

            foreach ($query->cursor() as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->user_name,
                    $row->user_email,
                    $row->exam_title,
                    $row->career_name,
                    $row->score,
                    $row->correct_answers,
                    $row->total_questions,
                    (int) ceil((int) $row->duration_seconds / 60),
                    $row->finished_at,
                ], ',');
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="tentativas_export.csv"');

        return $response;
    }

    private function buildQuery(array $filters): Builder
    {
        $query = Attempt::query()
            ->select([
                'attempts.id',
                'users.name as user_name',
                'users.email as user_email',
                'exams.title as exam_title',
                'careers.name as career_name',
                'attempts.score',
                'attempts.correct_answers',
                DB::raw('(SELECT COUNT(*) FROM questions WHERE questions.exam_id = exams.id) as total_questions'),
                'attempts.duration_seconds',
                'attempts.finished_at',
            ])
            ->join('users', 'users.id', '=', 'attempts.user_id')
            ->join('exams', 'exams.id', '=', 'attempts.exam_id')
            ->join('careers', 'careers.id', '=', 'exams.career_id')
            ->whereNotNull('attempts.finished_at')
            ->orderBy('attempts.finished_at', 'desc');

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
