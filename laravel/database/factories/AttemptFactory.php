<?php

namespace Database\Factories;

use App\Domain\Exam\Models\Attempt;
use App\Domain\Exam\Models\Exam;
use App\Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Exam\Models\Attempt>
 */
class AttemptFactory extends Factory
{
    protected $model = Attempt::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'exam_id' => Exam::factory(),
            'started_at' => now(),
            'finished_at' => null,
            'duration_seconds' => null,
            'correct_answers' => null,
            'score' => null,
        ];
    }

    /**
     * Indicate that the attempt is finished.
     */
    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'finished_at' => now(),
            'duration_seconds' => $this->faker->numberBetween(600, 3600),
            'correct_answers' => $this->faker->numberBetween(10, 20),
            'score' => $this->faker->randomFloat(2, 50, 100),
        ]);
    }
}
