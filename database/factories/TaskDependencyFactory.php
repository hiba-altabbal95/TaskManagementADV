<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskDependency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskDependency>
 */
class TaskDependencyFactory extends Factory
{
    protected $model = TaskDependency::class;
     public function definition() { 
        return [ 'task_id' => Task::factory(), // Create a related task 
        'dependent_task_id' => Task::factory(), // Create a related dependent task
        ]; }
}
