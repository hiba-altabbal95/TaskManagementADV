<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskDependency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Use an in-memory SQLite database
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        // Run the migrations for the test database
        $this->artisan('migrate');

        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    /** @test */
    public function it_can_list_tasks()
    {
        // Create some tasks
        Task::factory()->count(3)->create();

        // Call the index method
        $response = $this->getJson(route('tasks.index'));

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [['id', 'title', 'description', 'type', 'status', 'priority', 'date_due', 'assigned_to', 'attachment']]]);
    }

    /** @test */
    public function it_can_create_a_task()
    {
        $taskData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'type' => 'type1',
            'status' => 'Open',
            'priority' => 'High',
            'date_due' => now()->addDays(10),
            'dependencies' => []
        ];

        $response = $this->postJson(route('tasks.store'), $taskData);

        $response->assertStatus(201)
                 ->assertJsonStructure(['data' => ['id', 'title', 'description', 'type', 'status', 'priority', 'date_due', 'assigned_to', 'attachment']]);
    }

    /** @test */
    public function it_can_view_task_details()
    {
        $task = Task::factory()->create();

        $response = $this->getJson(route('tasks.show', ['task' => $task->id]));

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['id', 'title', 'description', 'type', 'status', 'priority', 'date_due', 'assigned_to', 'attachment']]);
    }

    /** @test */
    public function it_can_update_a_task()
    {
        $task = Task::factory()->create(['title' => 'Old Title']);

        $updatedData = ['title' => 'New Title'];

        $response = $this->putJson(route('tasks.update', ['task' => $task->id]), $updatedData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'New Title']);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson(route('tasks.destroy', ['task' => $task->id]));

        $response->assertStatus(200);
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }
}
