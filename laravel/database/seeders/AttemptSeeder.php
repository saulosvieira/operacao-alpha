<?php

namespace Database\Seeders;

use App\Domain\Auth\Models\User;
use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\Attempt;
use App\Domain\Exam\Models\UserAnswer;
use App\Domain\Exam\Models\Question;
use App\Domain\Exam\Enums\AnswerOption;
use Illuminate\Database\Seeder;

class AttemptSeeder extends Seeder
{
    public function run(): void
    {
        // Get users (excluding admin)
        $users = User::where('role', '!=', 'admin')->get();
        
        // Get active exams
        $exams = Exam::where('active', true)->get();

        foreach ($users as $user) {
            // Each user attempts 2-5 random exams
            $numAttempts = rand(2, 5);
            $selectedExams = $exams->random(min($numAttempts, $exams->count()));

            foreach ($selectedExams as $exam) {
                $questions = Question::where('exam_id', $exam->id)->get();
                
                if ($questions->isEmpty()) {
                    continue;
                }

                // Create attempt
                $startedAt = now()->subDays(rand(1, 30))->subHours(rand(0, 23));
                $durationSeconds = rand(3600, $exam->time_limit_minutes * 60); // Between 1 hour and time limit
                $finishedAt = $startedAt->copy()->addSeconds($durationSeconds);

                $attempt = Attempt::create([
                    'user_id' => $user->id,
                    'exam_id' => $exam->id,
                    'started_at' => $startedAt,
                    'finished_at' => $finishedAt,
                    'duration_seconds' => $durationSeconds,
                ]);

                // Create answers for each question
                $correctCount = 0;
                $answerOptions = ['A', 'B', 'C', 'D', 'E'];

                foreach ($questions as $question) {
                    // Simulate realistic performance (60-90% correct for most users)
                    $isCorrect = rand(1, 100) <= 75;
                    
                    $chosenAnswer = $isCorrect 
                        ? $question->correct_answer 
                        : $answerOptions[array_rand($answerOptions)];

                    if ($isCorrect) {
                        $correctCount++;
                    }

                    UserAnswer::create([
                        'user_id' => $user->id,
                        'attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'chosen_answer' => $chosenAnswer,
                        'correct' => $isCorrect,
                        'time_seconds' => rand(30, 300), // 30 seconds to 5 minutes per question
                    ]);
                }

                // Update attempt with results
                $score = ($correctCount / $questions->count()) * 100;
                $attempt->update([
                    'correct_answers' => $correctCount,
                    'score' => round($score, 2),
                ]);
            }
        }
    }
}
