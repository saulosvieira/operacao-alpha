<?php

namespace Database\Factories;

use App\Domain\Exam\Models\Exam;
use App\Domain\Career\Models\Career;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Exam\Models\Exam>
 */
class ExamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Exam::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'career_id' => Career::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'time_limit_minutes' => fake()->numberBetween(60, 180),
            'active' => true,
            'is_free' => fake()->boolean(),
            'feedback_mode' => fake()->randomElement(['immediate', 'end', 'none']),
        ];
    }
}