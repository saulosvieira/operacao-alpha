<?php

use App\Domain\Auth\Models\User;
use App\Domain\Complaint\Actions\UpdateComplaintStatusAction;
use App\Domain\Complaint\Enums\ComplaintPriority;
use App\Domain\Complaint\Enums\ComplaintStatus;
use App\Domain\Complaint\Enums\ComplaintType;
use App\Domain\Complaint\Models\Complaint;
use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\Question;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createTestComplaint(): Complaint
{
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

    return Complaint::create([
        'question_id' => $question->id,
        'admin_id' => $admin->id,
        'type' => ComplaintType::INCORRECT_ANSWER->value,
        'description' => 'Test complaint',
        'priority' => ComplaintPriority::HIGH->value,
        'status' => ComplaintStatus::OPEN->value,
    ]);
}

test('updates complaint status to IN_REVIEW', function () {
    $complaint = createTestComplaint();

    $action = app(UpdateComplaintStatusAction::class);
    $updated = $action->execute($complaint->id, ComplaintStatus::IN_REVIEW);

    expect($updated->status)->toBe(ComplaintStatus::IN_REVIEW);
    expect($updated->resolved_at)->toBeNull();
});

test('sets resolved_at when status changes to RESOLVED', function () {
    $complaint = createTestComplaint();

    $action = app(UpdateComplaintStatusAction::class);
    $updated = $action->execute($complaint->id, ComplaintStatus::RESOLVED, 'Fixed the answer');

    expect($updated->status)->toBe(ComplaintStatus::RESOLVED);
    expect($updated->resolved_at)->not->toBeNull();
    expect($updated->resolution_note)->toBe('Fixed the answer');
});

test('sets resolved_at when status changes to REJECTED', function () {
    $complaint = createTestComplaint();

    $action = app(UpdateComplaintStatusAction::class);
    $updated = $action->execute($complaint->id, ComplaintStatus::REJECTED, 'Not a valid complaint');

    expect($updated->status)->toBe(ComplaintStatus::REJECTED);
    expect($updated->resolved_at)->not->toBeNull();
    expect($updated->resolution_note)->toBe('Not a valid complaint');
});

test('persists resolution note when provided', function () {
    $complaint = createTestComplaint();

    $action = app(UpdateComplaintStatusAction::class);
    $action->execute($complaint->id, ComplaintStatus::RESOLVED, 'Answer corrected to B');

    $this->assertDatabaseHas('complaints', [
        'id' => $complaint->id,
        'status' => 'resolved',
        'resolution_note' => 'Answer corrected to B',
    ]);
});

test('does not set resolved_at for non-terminal statuses', function () {
    $complaint = createTestComplaint();

    $action = app(UpdateComplaintStatusAction::class);
    $updated = $action->execute($complaint->id, ComplaintStatus::IN_REVIEW);

    expect($updated->resolved_at)->toBeNull();

    $this->assertDatabaseHas('complaints', [
        'id' => $complaint->id,
        'status' => 'in_review',
    ]);
});

test('throws ModelNotFoundException when complaint does not exist', function () {
    $action = app(UpdateComplaintStatusAction::class);
    $action->execute(99999, ComplaintStatus::RESOLVED);
})->throws(ModelNotFoundException::class);
