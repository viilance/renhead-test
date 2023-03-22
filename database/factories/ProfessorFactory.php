<?php

namespace Database\Factories;

use App\Models\Professor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Professor>
 */
class ProfessorFactory extends Factory
{
    protected $model = Professor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'total_available_hours' => $this->faker->randomFloat(2, 1, 12),
            'payroll_per_hour' => $this->faker->randomFloat(2, 10, 50),
            'total_projects' => $this->faker->randomFloat(0, 0, 100),
            'office_number' => $this->faker->randomNumber(3),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'deleted_at' => null,
        ];
    }
}
