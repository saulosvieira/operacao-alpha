<?php

use App\Domain\Exam\Models\Exam;
use App\Domain\Auth\Models\User;
use App\Domain\Career\Models\Career;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('GET /api/exams — Pagination (BD-1)', function () {

    test('returns paginated results with default per_page of 20', function () {
        $user = User::factory()->create();
        Exam::factory()->count(25)->create(['active' => true]);

        $response = $this->actingAs($user)->getJson('/api/exams');

        $response->assertStatus(200)
            ->assertJsonCount(20, 'data')
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 20)
            ->assertJsonPath('meta.total', 25)
            ->assertJsonPath('meta.last_page', 2);
    });

    test('respects per_page parameter', function () {
        $user = User::factory()->create();
        Exam::factory()->count(15)->create(['active' => true]);

        $response = $this->actingAs($user)->getJson('/api/exams?per_page=5');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.total', 15)
            ->assertJsonPath('meta.last_page', 3);
    });

    test('respects page parameter', function () {
        $user = User::factory()->create();
        Exam::factory()->count(25)->create(['active' => true]);

        $response = $this->actingAs($user)->getJson('/api/exams?page=2');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 2);
    });

    test('returns empty data array for page beyond last_page', function () {
        $user = User::factory()->create();
        Exam::factory()->count(5)->create(['active' => true]);

        $response = $this->actingAs($user)->getJson('/api/exams?page=2&per_page=20');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 1)
            ->assertJsonPath('meta.total', 5);
    });

    test('clamps per_page to maximum of 100', function () {
        $user = User::factory()->create();
        Exam::factory()->count(5)->create(['active' => true]);

        $response = $this->actingAs($user)->getJson('/api/exams?per_page=200');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 100);
    });

    test('clamps per_page minimum to 1', function () {
        $user = User::factory()->create();
        Exam::factory()->count(5)->create(['active' => true]);

        $response = $this->actingAs($user)->getJson('/api/exams?per_page=0');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 1);
    });

    test('works with career_id filter and pagination', function () {
        $user = User::factory()->create();
        $career = Career::factory()->create();
        Exam::factory()->count(10)->create(['active' => true, 'career_id' => $career->id]);
        Exam::factory()->count(5)->create(['active' => true]); // other careers

        $response = $this->actingAs($user)->getJson("/api/exams?career_id={$career->id}&per_page=3");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.total', 10)
            ->assertJsonPath('meta.last_page', 4);
    });

    test('excludes inactive exams from pagination total', function () {
        $user = User::factory()->create();
        Exam::factory()->count(5)->create(['active' => true]);
        Exam::factory()->count(3)->create(['active' => false]);

        $response = $this->actingAs($user)->getJson('/api/exams');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 5);
    });

    test('returns correct data structure per exam item', function () {
        $user = User::factory()->create();
        Exam::factory()->create(['active' => true]);

        $response = $this->actingAs($user)->getJson('/api/exams');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'durationMin', 'numQuestions', 'isFree'],
                ],
            ]);
    });
});
