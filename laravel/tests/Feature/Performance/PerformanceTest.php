<?php

use App\Domain\Auth\Models\User;
use App\Domain\Exam\Models\Exam;
use App\Domain\Exam\Models\Attempt;
use App\Domain\Career\Models\Career;
use App\Domain\Exam\Enums\FeedbackMode;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->career = Career::factory()->create(['name' => 'Test Career']);
    $this->exam = Exam::factory()->create([
        'career_id' => $this->career->id,
        'title' => 'Test Exam',
        'feedback_mode' => FeedbackMode::FINAL,
    ]);
});

test('user can get performance statistics', function () {
    // Create some completed attempts
    Attempt::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'exam_id' => $this->exam->id,
        'finished_at' => now(),
        'score' => 75.0,
        'correct_answers' => 15,
        'duration_seconds' => 1800,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/performance/statistics');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'total_exams_completed',
                'average_score',
                'total_correct_answers',
                'total_questions',
                'accuracy_percentage',
                'total_time_spent_minutes',
                'strongest_career',
                'weakest_career',
                'career_breakdown',
            ],
        ]);

    expect($response->json('data.total_exams_completed'))->toBe(3);
});

test('user can get performance history', function () {
    // Create some completed attempts
    Attempt::factory()->count(5)->create([
        'user_id' => $this->user->id,
        'exam_id' => $this->exam->id,
        'finished_at' => now(),
        'score' => 80.0,
        'correct_answers' => 16,
        'duration_seconds' => 1200,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/performance/history');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'exam_id',
                    'exam_title',
                    'career_name',
                    'score',
                    'correct_answers',
                    'total_questions',
                    'time_spent_minutes',
                    'completed_at',
                ],
            ],
        ]);

    expect($response->json('data'))->toHaveCount(5);
});

test('user with no attempts gets empty statistics', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/performance/statistics');

    $response->assertOk();
    
    expect($response->json('data.total_exams_completed'))->toBe(0);
    expect($response->json('data.average_score'))->toBe(0);
});

test('user with no attempts gets empty history', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/performance/history');

    $response->assertOk();
    
    expect($response->json('data'))->toBeArray();
    expect($response->json('data'))->toHaveCount(0);
});

test('history respects limit parameter', function () {
    // Create 30 attempts
    Attempt::factory()->count(30)->create([
        'user_id' => $this->user->id,
        'exam_id' => $this->exam->id,
        'finished_at' => now(),
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/performance/history?limit=10');

    $response->assertOk();
    
    expect($response->json('data'))->toHaveCount(10);
});

test('unauthenticated user cannot access performance endpoints', function () {
    $this->getJson('/api/performance/statistics')
        ->assertUnauthorized();

    $this->getJson('/api/performance/history')
        ->assertUnauthorized();
});
