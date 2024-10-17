<?php

namespace App\Services;


use App\Models\Task;
use App\Models\TaskDependency;
use Exception;
use Faker\Core\File;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\File as HttpFile;
use Illuminate\Http\Testing\File as TestingFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Contracts\Providers\Storage as ProvidersStorage;
use Psy\Readline\Hoa\FileException;
use Symfony\Component\Mime\Part\File as PartFile;

class TaskService{



    public function ListTask(array $filters,$per_page = 10, $page = 1)
    {
        try {
             // Generate a unique cache key based on the filters
             $cacheKey = $this->generateCacheKey($filters);

        // Try to get the tasks from the cache
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($filters,$per_page) {
           return Task::filter($filters)->with('comments')->paginate($per_page);});
           

        } catch (Exception $e) {
         Log::error('Error Listing Tasks '. $e->getMessage());
            throw new Exception(ApiResponseService::error('Error Listing Tasks'));
        }
    }

    private function generateCacheKey(array $filters)
    {
        // Create a unique key based on the filters
        return 'tasks_' . md5(json_encode($filters));
    }

     /**
     * Create a new task.
     *
     * @param array $task
     * @return \App\Models\Task
     */
    public function createTask(array $data)
    {
        try {

            $cacheKey = $this->generateCacheKey($data);
            // Create a new Task record with the provided data
            $task= Task::create([
                'title'=> $data['title'],
                'description'=> $data['description'] ?? null,
                'type'=> $data['type'] ,
                'status'=> $data['status'],
                'priority'=> $data['priority'],
                'date_due'=> $data['date_due'],
             
            ]);

            //to forget old cashed when a new task is added
            Cache::forget($cacheKey);

            return $task;
           // Check for dependencies
        if ($request->has('dependencies')) {
            foreach ($request->dependencies as $dependency) {
                TaskDependency::create([
                    'task_id' => $task->id,
                    'dependent_task_id' => $dependency,
                ]);
            }
            $task->setStatus('blocked');
            $task->save();
        } else {
            $task->setStatus('open');
            $task->save();
        }
        } catch (Exception $e) {
          Log::error('Error creating Task: ' . $e->getMessage());
          throw new Exception(ApiResponseService::error('Error Creating Task'));
         
        }
    }

     /**
     * Get the details of a specific Task by its ID.
     *
     * @param int $id
     * @return \App\Models\Task
     */
    public function getTask(int $id)
    {
        try {
            // Find the Task by ID or fail with a 404 error if not found
            return Task::findOrFail($id)->load('comments');
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new Exception('Task not found.');
        } catch (Exception $e) {
            Log::error('Error retrieving Task: ' . $e->getMessage());
            throw new Exception(ApiResponseService::error('Error retrieving Task'));
        }
    }

     /**
     * Update the details of a specific task.
     *
     * @param array $data
     * @param int $id
     * @return \App\Models\Task
     */
    public function updateTask(array $data, int $id)
    {
        try {
            // Find the task by ID or fail with a 404 error if not found
            $task = Task::findOrFail($id);

            // Update the user with the provided data, filtering out null values
            $task->update(array_filter([
                'title'=> $data['title'] ?? $task->title,
                'description'=> $data['description'] ?? $task->description,
                'type'=> $data['type'] ?? $task->type,
                'status'=> $data['status'] ?? $task->status,
                'priority'=> $data['priority']?? $task->priority,
                'date_due'=> $data['date_due'] ?? $task->date_due,
                           ]));

            // Return the updated task
            return $task;
        } catch (ModelNotFoundException $e) {
            Log::error('task not found: ' . $e->getMessage());
            throw new Exception('task not found.');
        } catch (Exception $e) {
            Log::error('Error updating task: ' . $e->getMessage());
            throw new Exception(ApiResponseService::error('Error updating task:'));
        }
    }
    /**
     * Delete a specific Task by its ID.
     *
     * @param int $id
     * @return void
     */
    public function deleteTask(int $id)
    {
        try {
            // Find the Task by ID or fail with a 404 error if not found
            $Task = Task::findOrFail($id);

            // Delete the Task
            $Task->delete();
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new Exception('Task not found.');
        } catch (Exception $e) {
            Log::error('Error deleting Task ' . $e->getMessage());
            throw new Exception(ApiResponseService::error('Error deleting Task'));
        }
    }


     /**
     * Assign user to a specific Task
     *
     * @param  array  $data  The validated input data containing user ID.
     * @param  \App\Models\Task  $Task  The task instance to which users will be assigned.
     * @return \App\Models\Task  The updated task instance with the users loaded.
     * @throws \HttpResponseException  If there is an error during the operation.
     */
    public function assignTaskUser(array $data, $id)
    {
        try{            
             $task = Task::findOrFail($id);
             $task->assigned_to=$data['assigned_to'];
             $task->save();

             //when admin assign task to user ,task status become InProgress.
             $task->setStatus('InProgress');
             $task->save();

             return $task;
        }
         catch(Exception $e) {
            Log::error('Error Assigning Task to user' . $e->getMessage());
            throw new Exception(ApiResponseService::error('Error Assigning Task to user'));
        }
    }


    /**
     * Function to Upload Attachment totask
     * @param file we want to upload
     * @return path of that file
     * 
     * 
    */
    public function uploadAttachment($data)
    { 
          $file = $data['attachment'];
    
        $originalName = $file->getClientOriginalName();

         // Ensure the file extension is valid and there is no path traversal in the file name
         if (preg_match('/\.[^.]+\./', $originalName)) {
            throw new Exception(trans('general.notAllowedAction'), 403);
        }

        // Check for path traversal attack (e.g., using ../ or ..\ or / to go up directories)
        if (strpos($originalName, '..') !== false || strpos($originalName, '/') !== false || strpos($originalName, '\\') !== false) {
            throw new Exception(trans('general.pathTraversalDetected'), 403);
        }

         // Generate a safe, random file name
         $fileName = Str::random(32);
         $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '', $fileName);

         $extension = $file->getClientOriginalExtension(); // Safe way to get file extension
         $filePath = "Files/{$fileName}.{$extension}";

          // Store the file in the 'public' disk
          $path = $file->storeAs('Files', $fileName . '.' . $extension, 'public'); 
        
       
        return $path;
    }

   /**
    * update status of task
    *@param array $data
    * @param int $id
    * @return \App\Models\Task 
    *
    */
    public function updateStatus(array $data, $id)
    {
         // Find the task by ID or fail with a 404 error if not found
         $task = Task::findOrFail($id);
        try{
            if($data['status']==='Completed')
            {
               $task->markAsCompleted();
            }
        else{
               $task->setStatus($data['status']);
               $task->save();}

        return $task;
        }
        catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new Exception('Task not found.');
        } catch (Exception $e) {
            Log::error('Error deleting Task ' . $e->getMessage());
            throw new Exception(ApiResponseService::error('Error deleting Task'));
        }

    }


     /**
     * Display a paginated listing of the trashed (soft deleted) resources.
     */
    public function trashedListTask($perPage)
    {
        try {
            return Task::onlyTrashed()->paginate($perPage);
        } catch (Exception $e) {
            Log::error('Error Trashing Task ' . $e->getMessage());
            throw new Exception(ApiResponseService::error('Something error'));
        }
    }

    /**
     * Restore a trashed (soft deleted) resource by its ID.
     *
     * @param  int  $id  The ID of the trashed Task to be restored.
     * @return \App\Models\Task
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the Task with the given ID is not found.
     * @throws \Exception If there is an error during the restore process.
     */
    public function restoreTask($id)
    {
        try {
            $Task = Task::onlyTrashed()->findOrFail($id);
            $Task->restore();
            return $Task;
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new Exception('Task not found.');
        } catch (Exception $e) {
            Log::error('Error restoring Task: ' . $e->getMessage());
            throw new Exception(ApiResponseService::error('Something error'));
        }
    }

    /**
     * Permanently delete a trashed (soft deleted) resource by its ID.
     *
     * @param  int  $id  The ID of the trashed Task to be permanently deleted.
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the Task with the given ID is not found.
     * @throws \Exception If there is an error during the force delete process.
     */
    public function forceDeleteTask($id)
    {
        try {
            $Task = Task::onlyTrashed()->findOrFail($id);

            $Task->forceDelete();
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new Exception('Task not found.');
        } catch (Exception $e) {
            Log::error('Error force deleting Task ' . $e->getMessage());
            throw new Exception(ApiResponseService::error('Something error'));
        }
    }

}