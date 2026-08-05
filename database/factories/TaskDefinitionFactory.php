<?php

namespace Database\Factories;

use App\Models\TaskDefinition;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskDefinitionFactory extends Factory
{
    protected $model = TaskDefinition::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'task_type' => $this->faker->randomElement(['daily', 'on_demand']),
            'is_active' => true,
            'display_order' => 0,
            'created_by' => User::factory(),
        ];
    }
}
