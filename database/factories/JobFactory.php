<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\Professor;
use App\Models\Trader;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employeeType = $this->faker->randomElement(['professor', 'trader']);
        $employeeModel = $employeeType === 'professor' ? Professor::class : Trader::class;
        $employeeId = $employeeModel::inRandomOrder()->first()->id;

        return [
            'employee_type' => $employeeType,
            'employee_id' => $employeeId,
            'date' => $this->faker->date(),
            'total_hours' => $this->faker->randomFloat(2, 1, 12),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'deleted_at' => null,
        ];
    }
}
