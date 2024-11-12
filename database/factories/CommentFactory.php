<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{protected $model = Comment::class;
 public function definition() {
     return [
          'content' => $this->faker->paragraph,
          'user_id' => User::factory(), // Creating a related user 
          'commentable_id' => Task::factory(), // Creating a related task for polymorphic relation
          'commentable_type' => Task::class, // Assuming the comment is related to a Task 
           ]; }
}
