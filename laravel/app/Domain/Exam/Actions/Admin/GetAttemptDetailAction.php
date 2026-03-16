<?php

declare(strict_types=1);

namespace App\Domain\Exam\Actions\Admin;

use App\Domain\Complaint\Repositories\ComplaintRepository;
use App\Domain\Exam\DTOs\Admin\AttemptDetailData;
use App\Domain\Exam\DTOs\Admin\QuestionAnswerData;
use App\Domain\Exam\Models\Attempt;
use App\Domain\Exam\Models\UserAnswer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class GetAttemptDetailAction
{
    public function __construct(
        private readonly ComplaintRepository $complaintRepository,
    ) {}

    /**
     * @return array{attempt: AttemptDetailData, answers: Collection, questionStats: Collection, complaints: Collection}
     */
    public function execute(int $attemptId): array
    {
        $attempt = Attempt::query()
            ->with(['user', 'exam.career'])
            ->find($attemptId);

        if (! $attempt || $attempt->finished_at === null) {
            abort(404, 'Tentativa não encontrada');
        }

        $attemptDetail = new AttemptDetailData(
            id: $attempt->id,
            userName: $attempt->user->name,
            userEmail: $attempt->user->email,
            examTitle: $attempt->exam->title,
            careerName: $attempt->exam->career->name,
            score: (float) $attempt->score,
            correctAnswers: (int) $attempt->correct_answers,
            totalQuestions: $attempt->exam->questions()->count(),
            durationMinutes: (int) ceil((int) $attempt->duration_seconds / 60),
            finishedAt: $attempt->finished_at->toDateTimeString(),
            examId: $attempt->exam_id,
        );

        // Get all user answers for this attempt with their questions
        $userAnswers = UserAnswer::query()
            ->where('attempt_id', $attemptId)
            ->with('question')
            ->get();

        // Calculate accuracy rate per question across ALL finished attempts of the same exam
        $questionStats = $this->calculateQuestionAccuracyRates($attempt->exam_id);

        // Get pending complaints per question
        $questionIds = $userAnswers->pluck('question.id')->filter()->toArray();
        $complaints = $this->complaintRepository->countPendingByQuestionIds($questionIds);

        // Build QuestionAnswerData DTOs
        $answers = $userAnswers
            ->sortBy('question.question_number')
            ->values()
            ->map(function (UserAnswer $answer) use ($questionStats, $complaints) {
                $question = $answer->question;
                $accuracyRate = $questionStats->get($question->id, 0.0);

                return new QuestionAnswerData(
                    questionId: $question->id,
                    questionNumber: (int) $question->question_number,
                    statement: $question->statement,
                    selectedOption: $answer->selected_option ?? '',
                    correctAnswer: $question->correct_answer,
                    isCorrect: (bool) $answer->is_correct,
                    optionA: $question->option_a ?? '',
                    optionB: $question->option_b ?? '',
                    optionC: $question->option_c ?? '',
                    optionD: $question->option_d ?? '',
                    optionE: $question->option_e ?? '',
                    explanation: $question->explanation,
                    accuracyRate: $accuracyRate,
                    hasPendingComplaints: ($complaints->get($question->id, 0) > 0),
                );
            });

        return [
            'attempt' => $attemptDetail,
            'answers' => $answers,
            'questionStats' => $questionStats,
            'complaints' => $complaints,
        ];
    }

    /**
     * Calculate accuracy rate per question for all finished attempts of the given exam.
     * Returns a Collection keyed by question_id with accuracy rate (0.0 to 100.0) as value.
     */
    private function calculateQuestionAccuracyRates(int $examId): Collection
    {
        return DB::table('user_answers')
            ->join('attempts', 'attempts.id', '=', 'user_answers.attempt_id')
            ->where('attempts.exam_id', $examId)
            ->whereNotNull('attempts.finished_at')
            ->select(
                'user_answers.question_id',
                DB::raw('COUNT(*) as total_answers'),
                DB::raw('SUM(CASE WHEN user_answers.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers'),
            )
            ->groupBy('user_answers.question_id')
            ->get()
            ->mapWithKeys(function ($row) {
                $rate = $row->total_answers > 0
                    ? round(($row->correct_answers / $row->total_answers) * 100, 2)
                    : 0.0;

                return [(int) $row->question_id => $rate];
            });
    }
}
