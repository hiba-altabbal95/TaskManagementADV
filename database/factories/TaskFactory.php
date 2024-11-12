<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition() { return [ 
        'title' => $this->faker->sentence, 
        'description' => $this->faker->paragraph, 
        'type' => $this->faker->randomElement(['Feature', 'Bug', 'Improvement']), 
       // 'status' => 'Open',
        'status' => $this->faker->randomElement(['Open','InProgress','Completed','Blocked']), 
        'priority' => $this->faker->randomElement(['low', 'medium', 'high']), 
        'date_due' => $this->faker->date(), 
        'assigned_to' => User::factory(), 
        'attachment' => $this->faker->word . '.pdf',
             ]; }
}
