<?php

namespace App\Domain\Performance\Repositories;

use App\Domain\Exam\Models\ExamResult;
use App\Domain\Exam\Models\Attempt;
use App\Domain\Performance\DTOs\StatisticsData;
use App\Domain\Performance\DTOs\HistoryData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PerformanceRepository
{
    public function getStatistics(string $userId): StatisticsData
    {
        // Get completed attempts for the user
        $attempts = Attempt::where('user_id', $userId)
            ->whereNotNull('finished_at')
            ->with('exam.career')
            ->get();

        if ($attempts->isEmpty()) {
            return StatisticsData::fromArray([
                'total_exams_completed' => 0,
                'average_score' => 0.0,
                'total_correct_answers' => 0,
                'total_questions' => 0,
                'accuracy_percentage' => 0.0,
                'total_time_spent_minutes' => 0,
                'strongest_career' => null,
                'weakest_career' => null,
                'career_breakdown' => [],
            ]);
        }

        // Calculate overall statistics
        $totalExamsCompleted = $attempts->count();
        $averageScore = $attempts->avg('score') ?? 0.0;
        $totalCorrectAnswers = $attempts->sum('correct_answers') ?? 0;
        
        // Calculate total questions from all attempts
        $totalQuestions = $attempts->sum(function ($attempt) {
            return $attempt->exam->questions()->count();
        });

        $accuracyPercentage = $totalQuestions > 0 
            ? round(($totalCorrectAnswers / $totalQuestions) * 100, 2)
            : 0.0;

        $totalTimeSpentMinutes = round($attempts->sum('duration_seconds') / 60);

        // Calculate career breakdown
        $careerBreakdown = $attempts->groupBy(function ($attempt) {
            return $attempt->exam->career->name ?? 'Unknown';
        })->map(function ($careerAttempts, $careerName) {
            $avgScore = $careerAttempts->avg('score') ?? 0.0;
            return [
                'career_name' => $careerName,
                'exams_completed' => $careerAttempts->count(),
                'average_score' => round($avgScore, 2),
                'total_correct' => $careerAttempts->sum('correct_answers'),
            ];
        })->values()->toArray();

        // Find strongest and weakest careers (if there are multiple careers)
        $strongestCareer = null;
        $weakestCareer = null;

        if (count($careerBreakdown) > 0) {
            $sortedByScore = collect($careerBreakdown)->sortByDesc('average_score');
            $strongestCareer = $sortedByScore->first()['career_name'] ?? null;
            $weakestCareer = $sortedByScore->last()['career_name'] ?? null;
            
            // If only one career, don't set weakest
            if (count($careerBreakdown) === 1) {
                $weakestCareer = null;
            }
        }

        return StatisticsData::fromArray([
            'total_exams_completed' => $totalExamsCompleted,
            'average_score' => round($averageScore, 2),
            'total_correct_answers' => $totalCorrectAnswers,
            'total_questions' => $totalQuestions,
            'accuracy_percentage' => $accuracyPercentage,
            'total_time_spent_minutes' => $totalTimeSpentMinutes,
            'strongest_career' => $strongestCareer,
            'weakest_career' => $weakestCareer,
            'career_breakdown' => $careerBreakdown,
        ]);
    }

    public function getHistory(string $userId, int $limit = 20): Collection
    {
        $attempts = Attempt::where('user_id', $userId)
            ->whereNotNull('finished_at')
            ->with(['exam.career'])
            ->orderBy('finished_at', 'desc')
            ->limit($limit)
            ->get();

        return $attempts->map(function ($attempt) {
            $totalQuestions = $attempt->exam->questions()->count();
            
            return HistoryData::fromArray([
                'exam_id' => $attempt->exam_id,
                'exam_title' => $attempt->exam->title,
                'career_name' => $attempt->exam->career->name ?? 'Unknown',
                'score' => $attempt->score ?? 0.0,
                'correct_answers' => $attempt->correct_answers ?? 0,
                'total_questions' => $totalQuestions,
                'time_spent_minutes' => round($attempt->duration_seconds / 60),
                'completed_at' => $attempt->finished_at->toIso8601String(),
            ]);
        });
    }
}
