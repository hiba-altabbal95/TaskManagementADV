<?php


namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TaskServiceTest extends TestCase
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
    }

    /** @test 
     * This test verifies the ListTask method, ensuring it can list tasks based on filters
    */
    public function it_can_list_tasks_with_filters()
    {
        $taskService = new TaskService();

        // Create sample tasks
        Task::factory()->count(5)->create();

        $filters = ['status' => 'Open'];
        $tasks = $taskService->ListTask($filters, 10, 1);

        $this->assertNotEmpty($tasks);
    }

    /** @test
     * This test checks the createTask method to ensure tasks are created with dependencies
     */
    public function it_can_create_a_task_with_dependencies()
    {
        $taskService = new TaskService();

        // Create dependent tasks
        $dependentTask1 = Task::factory()->create();
        $dependentTask2 = Task::factory()->create();

        // Create a new task with dependencies
        $taskData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'type' => 'type1',
            'status' => 'Open',
            'priority' => 'high',
            'date_due' => now()->addDays(10),
            'dependencies' => [$dependentTask1->id, $dependentTask2->id]
        ];

        $task = $taskService->createTask($taskData);

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
        $this->assertDatabaseCount('task_dependencies', 2);
    }

    /** @test
     * 
     * This test verifies the getTask method to ensure it can retrieve a task by ID along with its comment
     */
    public function it_can_get_a_task_with_comments()
    {
        $taskService = new TaskService();

        // Create a sample task
        $task = Task::factory()->create();

        // Get the task by ID
        $retrievedTask = $taskService->getTask($task->id);

        $this->assertEquals($task->id, $retrievedTask->id);
    }

    /** @test 
     * This test ensures the updateTask method can update the task detail
    */
    public function it_can_update_a_task()
    {
        $taskService = new TaskService();

        // Create a sample task
        $task = Task::factory()->create(['title' => 'Old Title']);

        // Update the task
        $updatedData = ['title' => 'New Title'];
        $updatedTask = $taskService->updateTask($updatedData, $task->id);

        $this->assertEquals('New Title', $updatedTask->title);
    }

    /** @test 
     * This test checks the deleteTask method to ensure tasks are soft delete
    */
    public function it_can_delete_a_task()
    {
        $taskService = new TaskService();

        // Create a sample task
        $task = Task::factory()->create();

        // Delete the task
        $taskService->deleteTask($task->id);

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }
}
