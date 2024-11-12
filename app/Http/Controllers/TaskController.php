<?php

namespace App\Http\Controllers;


use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskRequest\AssignTaskUser;
use App\Http\Requests\TaskRequest\StoreTaskRequest;
use App\Http\Requests\TaskRequest\TaskAttachementRequest;
use App\Http\Requests\TaskRequest\UpdateTaskRequest;
use App\Http\Requests\TaskRequest\UpdateTaskStatusRequest;
use App\Models\Task;
use App\Models\TaskStatusUpdate;
use App\Services\ApiResponseService;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {   //extractfilter from request
        $filters = $request->only(['per_page','type', 'status', 'assigned_to', 'date_due', 'priority']);
        
        $task = $this->taskService->listTask($filters);
         
        return ApiResponseService::paginated($task, 'tasks retrieved successfully');
    }

    /**
     * Store a new Task.
     *
     * @param StoreTaskRequest $request
     * @return JsonResponse
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        // Validate the request data
        $data = $request->validated();

        // Create a new Task with the validated data
        $task = $this->taskService->createTask($data);

        $this->authorize('create', Task::class);

        // Return a success response with the created Task data
        return ApiResponseService::success($task, 'Task created successfully', 201);
    }

    /**
     * Show details of a specific Task.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        
        // Retrieve the details of the book by its ID
        $task = $this->taskService->getTask($id);
        
        $this->authorize('view', $task);

        // Return a success response with the Task details
        return ApiResponseService::success($task, 'Task details retrieved successfully');
    }

     /**
     * Update the specified resource in storage.
     * @param UpdateuserRequest $request
     * @param int $id
     * @return JsonResponse
     * 
     */
    public function update(UpdateTaskRequest $request, string $id)
    {
        
        // Validate the request data
        $data = $request->validated();

        // Update the user with the validated data
        $task = $this->taskService->updateTask($data, $id);

        $this->authorize('update', $task);

        // Return a success response with the updated user data
        return ApiResponseService::success($task, 'task updated successfully');
    }

    /**
     * Delete a specific Task.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorize('delete', Task::findOrfail($id));
        // Delete the Task by its ID
        $this->taskService->deleteTask($id);

        // Return a success response indicating the Task was deleted
        return ApiResponseService::success(null, 'Task deleted successfully');
    }


     /**
     * Display a paginated listing of the trashed (soft deleted) resources.
     */
    public function trashed(Request $request)
    {   $this->authorize('trash');
        $perPage = $request->input('per_page', 10);
        $trashedTasks = $this->taskService->trashedListTask($perPage);

        return ApiResponseService::success($trashedTasks, 'trashed tasks');
    }

    /**
     * Restore a trashed (soft deleted) resource by its ID.
     */
    public function restore($id)
    {  $this->authorize('restore', Task::findOrfail($id));
        $Task = $this->taskService->restoreTask($id);
        return ApiResponseService::success($Task, 'task restore successfully');
    }

    /**
     * Permanently delete a trashed (soft deleted) resource by its ID.
     */
    public function forceDelete($id)
    {  $this->authorize('force', Task::findOrfail($id));
        $this->taskService->forceDeleteTask($id);
        return ApiResponseService::success(null, 'task deleted permenantly');
    }

    /**
     * Assign task to user
     * @param UpdateuserRequest $request
     * @param int $id
     * @return JsonResponse
     * 
     */
    public function AssignTask(AssignTaskUser $request,int $id)
    {
      $data=$request->validated();
      $task=$this->taskService->assignTaskUser($data,$id);
      $this->authorize('assign',$task);
      
      return ApiResponseService::success($task, 'task Assigned to user  successfully');
    }

    /**
     * reAssign task to another user 
     * @param UpdateuserRequest $request
     * @param int $id
     * @return JsonResponse
     * 
     */
    public function ReAssignTask(AssignTaskUser $request,int $id)
    {
      $data=$request->validated();
      $task=$this->taskService->assignTaskUser($data,$id);
      $this->authorize('assign',$task);
      
      return ApiResponseService::success($task, 'task Assigned to another user  successfully');
    }

    /**
     * add attachment to task by admin
     * @param UpdateuserRequest $request
     * @param int $id
     * @return JsonResponse
     * 
     */
    public function storeAttachment(TaskAttachementRequest $request, $taskId)
    {
        $data=$request->validated();
        $task = Task::findOrFail($taskId);
        $this->authorize('upload',$task);

        $path = $this->taskService->uploadAttachment($data);

        // Store the path in the database
        $task->attachment = $path;
        $task->save();

        return ApiResponseService::success($task, 'task attachment added  successfully');
    }

   /**
     * update task status
     * @param UpdateuserRequest $request
     * @param int $id
     * @return JsonResponse
    * 
    */
    public function updateStatus(UpdateTaskStatusRequest $request, $taskId)
     { 
        $data = $request->validated(); 
      //  $task = Task::findOrFail($taskId); 
      

        $task=$this->taskService->updateStatus($data, $taskId);
        $this->authorize('updateStatus', $task); 
        // Use TaskStatusUpdateModel to update the task status 
    //    $statusUpdate =TaskStatusUpdate::updateStatus($data, $task); 
        return ApiResponseService::success($task, 'Task status updated successfully'); }

}
