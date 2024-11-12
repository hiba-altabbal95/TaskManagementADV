<?php


// app/Http/Controllers/TaskDependencyController.php

namespace App\Http\Controllers;

use App\Services\TaskDependencyService;
use Illuminate\Http\Request;

class TaskDependencyController extends Controller
{
    protected $taskDependencyService;

    public function __construct(TaskDependencyService $taskDependencyService)
    {
        $this->taskDependencyService = $taskDependencyService;
    }

    public function store(Request $request)
    {
        $result = $this->taskDependencyService->createTaskDependency($request->validate());

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        return response()->json(['message' => 'Task dependency created successfully', 'data' => $result], 201);
    }
}
