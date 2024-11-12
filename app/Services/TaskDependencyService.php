<?php


namespace App\Services;

use App\Models\TaskDependency;
use Illuminate\Support\Facades\Validator;

class TaskDependencyService
{
    public function createTaskDependency(array $data)
    {
        $validator = Validator::make($data, [
            'task_id' => 'required|exists:tasks,id',
            'dependent_task_id' => 'required|exists:tasks,id|different:task_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return TaskDependency::create($data);
    }
}
