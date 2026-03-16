<?php

use App\Domain\Auth\Models\User;
use App\Domain\Complaint\Actions\CreateComplaintAction;
use App\Domain\Complaint\Enums\ComplaintPriority;
use App\Domain\Complaint\Enums\ComplaintStatus;
use App\Domain\Complaint\Enums\ComplaintType;
use App\Domain\Complaint\Models\Complaint;
use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\Question;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('creates a complaint with all required fields and status OPEN', function () {
    $exam = Exam::factory()->create();
    $question = Question::create([
        'exam_id' => $exam->id,
        'question_number' => 1,
        'statement' => 'Test statement',
        'option_a' => 'A',
        'option_b' => 'B',
        'option_c' => 'C',
        'option_d' => 'D',
        'option_e' => 'E',
        'correct_answer' => 'A',
        'explanation' => 'Explanation',
    ]);
    $admin = User::factory()->create(['role' => 'admin']);

    $action = app(CreateComplaintAction::class);
    $complaint = $action->execute(
        $question->id,
        $admin->id,
        ComplaintType::INCORRECT_ANSWER,
        'The correct answer should be B',
        ComplaintPriority::HIGH
    );

    expect($complaint)->toBeInstanceOf(Complaint::class);
    expect($complaint->question_id)->toBe($question->id);
    expect($complaint->admin_id)->toBe($admin->id);
    expect($complaint->type)->toBe(ComplaintType::INCORRECT_ANSWER);
    expect($complaint->description)->toBe('The correct answer should be B');
    expect($complaint->priority)->toBe(ComplaintPriority::HIGH);
    expect($complaint->status)->toBe(ComplaintStatus::OPEN);
    expect($complaint->exists)->toBeTrue();
});

test('throws ModelNotFoundException when question does not exist', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $action = app(CreateComplaintAction::class);
    $action->execute(
        99999,
        $admin->id,
        ComplaintType::OTHER,
        'Some description',
        ComplaintPriority::LOW
    );
})->throws(ModelNotFoundException::class);

test('persists complaint to database', function () {
    $exam = Exam::factory()->create();
    $question = Question::create([
        'exam_id' => $exam->id,
        'question_number' => 1,
        'statement' => 'Statement',
        'option_a' => 'A',
        'option_b' => 'B',
        'option_c' => 'C',
        'option_d' => 'D',
        'option_e' => 'E',
        'correct_answer' => 'C',
        'explanation' => 'Explanation',
    ]);
    $admin = User::factory()->create(['role' => 'admin']);

    $action = app(CreateComplaintAction::class);
    $action->execute(
        $question->id,
        $admin->id,
        ComplaintType::AMBIGUOUS_STATEMENT,
        'Ambiguous wording',
        ComplaintPriority::MEDIUM
    );

    $this->assertDatabaseHas('complaints', [
        'question_id' => $question->id,
        'admin_id' => $admin->id,
        'type' => 'ambiguous_statement',
        'description' => 'Ambiguous wording',
        'priority' => 'medium',
        'status' => 'open',
    ]);
});
