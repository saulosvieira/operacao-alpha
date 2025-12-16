<?php

namespace Database\Seeders;

use App\Domain\Auth\Models\User;
use App\Domain\Career\Models\Career;
use App\Domain\Exam\Models\Attempt;
use App\Domain\Exam\Models\ExamResult;
use App\Domain\Ranking\Models\Ranking;
use App\Domain\Ranking\Enums\RankingType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RankingSeeder extends Seeder
{
    public function run(): void
    {
        // First, create exam results from attempts
        $this->createExamResults();
        
        // Then create rankings
        $this->createRankings();
    }

    private function createExamResults(): void
    {
        $attempts = Attempt::whereNotNull('finished_at')->get();

        foreach ($attempts as $attempt) {
            $questions = DB::table('questions')
                ->where('exam_id', $attempt->exam_id)
                ->count();

            ExamResult::create([
                'user_id' => $attempt->user_id,
                'exam_id' => $attempt->exam_id,
                'score' => $attempt->score,
                'total_questions' => $questions,
                'correct_answers' => $attempt->correct_answers,
                'total_time_seconds' => $attempt->duration_seconds,
                'finished_at' => $attempt->finished_at,
            ]);
        }
    }

    private function createRankings(): void
    {
        $users = User::where('role', '!=', 'admin')->get();

        foreach ($users as $user) {
            // Get user's exam results for daily score
            $dailyResults = ExamResult::where('user_id', $user->id)
                ->where('created_at', '>=', now()->startOfDay())
                ->get();

            // Get user's exam results for weekly score
            $weeklyResults = ExamResult::where('user_id', $user->id)
                ->where('created_at', '>=', now()->startOfWeek())
                ->get();

            $dailyScore = $dailyResults->sum('score');
            $weeklyScore = $weeklyResults->sum('score');

            // Only create ranking if user has results
            if ($dailyResults->isNotEmpty() || $weeklyResults->isNotEmpty()) {
                Ranking::create([
                    'user_id' => $user->id,
                    'daily_score' => round($dailyScore, 2),
                    'weekly_score' => round($weeklyScore, 2),
                    'calculated_at' => now()->toDateString(),
                ]);
            }
        }
    }
}
