<?php

use App\Domain\Auth\Models\User;
use App\Domain\Career\Models\Career;
use App\Domain\Complaint\Enums\ComplaintPriority;
use App\Domain\Complaint\Enums\ComplaintStatus;
use App\Domain\Complaint\Enums\ComplaintType;
use App\Domain\Complaint\Models\Complaint;
use App\Domain\Exam\Actions\Admin\GetAttemptDetailAction;
use App\Domain\Exam\DTOs\Admin\AttemptDetailData;
use App\Domain\Exam\DTOs\Admin\QuestionAnswerData;
use App\Domain\Exam\Models\Attempt;
use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\Question;
use App\Domain\Exam\Models\UserAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────

function createExamWithQuestions(int $questionCount = 5): Exam
{
    $exam = Exam::factory()->create();

    for ($i = 1; $i <= $questionCount; $i++) {
        Question::create([
            'exam_id' => $exam->id,
            'question_number' => $i,
            'statement' => fake()->sentence(),
            'option_a' => fake()->sentence(),
            'option_b' => fake()->sentence(),
            'option_c' => fake()->sentence(),
            'option_d' => fake()->sentence(),
            'option_e' => fake()->sentence(),
            'correct_answer' => fake()->randomElement(['A', 'B', 'C', 'D', 'E']),
            'explanation' => fake()->paragraph(),
        ]);
    }

    return $exam;
}

function createFinishedAttemptWithAnswers(Exam $exam, ?User $user = null): Attempt
{
    $user = $user ?? User::factory()->create();
    $questions = $exam->questions;

    $correctCount = 0;
    $attempt = Attempt::factory()->finished()->create([
        'user_id' => $user->id,
        'exam_id' => $exam->id,
    ]);

    foreach ($questions as $question) {
        $isCorrect = fake()->boolean(60);
        if ($isCorrect) {
            $correctCount++;
        }

        UserAnswer::create([
            'user_id' => $user->id,
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'selected_option' => $isCorrect ? $question->correct_answer : fake()->randomElement(['A', 'B', 'C', 'D', 'E']),
            'is_correct' => $isCorrect,
        ]);
    }

    $attempt->update(['correct_answers' => $correctCount]);

    return $attempt;
}

// ─────────────────────────────────────────────────────────────────────────────
// Tests
// ─────────────────────────────────────────────────────────────────────────────

test('returns attempt detail with all required fields', function () {
    $exam = createExamWithQuestions(3);
    $attempt = createFinishedAttemptWithAnswers($exam);

    $action = app(GetAttemptDetailAction::class);
    $result = $action->execute($attempt->id);

    expect($result)->toHaveKeys(['attempt', 'answers', 'questionStats', 'complaints']);

    $attemptData = $result['attempt'];
    expect($attemptData)->toBeInstanceOf(AttemptDetailData::class);
    expect($attemptData->id)->toBe($attempt->id);
    expect($attemptData->userName)->not->toBeEmpty();
    expect($attemptData->userEmail)->not->toBeEmpty();
    expect($attemptData->examTitle)->not->toBeEmpty();
    expect($attemptData->careerName)->not->toBeEmpty();
    expect($attemptData->examId)->toBe($exam->id);
    expect($attemptData->totalQuestions)->toBe(3);
});

test('returns answers as QuestionAnswerData DTOs sorted by question number', function () {
    $exam = createExamWithQuestions(5);
    $attempt = createFinishedAttemptWithAnswers($exam);

    $action = app(GetAttemptDetailAction::class);
    $result = $action->execute($attempt->id);

    $answers = $result['answers'];
    expect($answers)->toHaveCount(5);

    $questionNumbers = $answers->pluck('questionNumber')->toArray();
    expect($questionNumbers)->toBe([1, 2, 3, 4, 5]);

    $answers->each(function ($answer) {
        expect($answer)->toBeInstanceOf(QuestionAnswerData::class);
        expect($answer->optionA)->not->toBeEmpty();
        expect($answer->optionB)->not->toBeEmpty();
        expect($answer->correctAnswer)->not->toBeEmpty();
    });
});

test('aborts 404 when attempt not found', function () {
    $action = app(GetAttemptDetailAction::class);
    $action->execute(99999);
})->throws(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

test('aborts 404 when attempt is not finished', function () {
    $attempt = Attempt::factory()->create([
        'finished_at' => null,
    ]);

    $action = app(GetAttemptDetailAction::class);
    $action->execute($attempt->id);
})->throws(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

test('calculates accuracy rate per question across all finished attempts', function () {
    $exam = createExamWithQuestions(1);
    $question = $exam->questions->first();

    // Create 4 finished attempts: 3 correct, 1 incorrect → 75% accuracy
    for ($i = 0; $i < 4; $i++) {
        $user = User::factory()->create();
        $attempt = Attempt::factory()->finished()->create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
        ]);

        $isCorrect = $i < 3; // first 3 correct, last incorrect
        UserAnswer::create([
            'user_id' => $user->id,
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'selected_option' => $isCorrect ? $question->correct_answer : 'X',
            'is_correct' => $isCorrect,
        ]);
    }

    // Also create an unfinished attempt (should NOT count)
    $unfinishedUser = User::factory()->create();
    $unfinishedAttempt = Attempt::factory()->create([
        'user_id' => $unfinishedUser->id,
        'exam_id' => $exam->id,
        'finished_at' => null,
    ]);
    UserAnswer::create([
        'user_id' => $unfinishedUser->id,
        'attempt_id' => $unfinishedAttempt->id,
        'question_id' => $question->id,
        'selected_option' => $question->correct_answer,
        'is_correct' => true,
    ]);

    // Use the first finished attempt to get detail
    $firstAttempt = Attempt::where('exam_id', $exam->id)->whereNotNull('finished_at')->first();
    $action = app(GetAttemptDetailAction::class);
    $result = $action->execute($firstAttempt->id);

    $accuracyRate = $result['questionStats']->get($question->id);
    expect($accuracyRate)->toBe(75.0);
});

test('includes pending complaints indicator per question', function () {
    $exam = createExamWithQuestions(2);
    $attempt = createFinishedAttemptWithAnswers($exam);
    $admin = User::factory()->create(['role' => 'admin']);
    $questions = $exam->questions;

    // Add pending complaint to first question
    Complaint::create([
        'question_id' => $questions[0]->id,
        'admin_id' => $admin->id,
        'type' => ComplaintType::INCORRECT_ANSWER->value,
        'description' => 'Test complaint',
        'priority' => ComplaintPriority::HIGH->value,
        'status' => ComplaintStatus::OPEN->value,
    ]);

    // Add resolved complaint to second question (should NOT count)
    Complaint::create([
        'question_id' => $questions[1]->id,
        'admin_id' => $admin->id,
        'type' => ComplaintType::OTHER->value,
        'description' => 'Resolved complaint',
        'priority' => ComplaintPriority::LOW->value,
        'status' => ComplaintStatus::RESOLVED->value,
    ]);

    $action = app(GetAttemptDetailAction::class);
    $result = $action->execute($attempt->id);

    $answers = $result['answers'];
    $firstAnswer = $answers->firstWhere('questionId', $questions[0]->id);
    $secondAnswer = $answers->firstWhere('questionId', $questions[1]->id);

    expect($firstAnswer->hasPendingComplaints)->toBeTrue();
    expect($secondAnswer->hasPendingComplaints)->toBeFalse();
});

test('marks questions with accuracy rate below 30% as potentially problematic', function () {
    $exam = createExamWithQuestions(1);
    $question = $exam->questions->first();

    // Create 10 finished attempts: 2 correct, 8 incorrect → 20% accuracy (< 30%)
    for ($i = 0; $i < 10; $i++) {
        $user = User::factory()->create();
        $attempt = Attempt::factory()->finished()->create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
        ]);

        $isCorrect = $i < 2;
        UserAnswer::create([
            'user_id' => $user->id,
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'selected_option' => $isCorrect ? $question->correct_answer : 'X',
            'is_correct' => $isCorrect,
        ]);
    }

    $firstAttempt = Attempt::where('exam_id', $exam->id)->whereNotNull('finished_at')->first();
    $action = app(GetAttemptDetailAction::class);
    $result = $action->execute($firstAttempt->id);

    $accuracyRate = $result['questionStats']->get($question->id);
    expect($accuracyRate)->toBe(20.0);
    expect($accuracyRate < 30)->toBeTrue();
});
