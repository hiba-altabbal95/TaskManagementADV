<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskDependencyController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//To apply rate limiter that we difined in AppServiceProvider
//Route::middleware('throttle:global')->group(function(){


Route::post('login', [AuthController::class, 'login']);

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');

//Route::apiResource('users', UserController::class);


//Route::apiResource('tasks', TaskController::class);


Route::middleware(['auth:api'])->group(function () {
    Route::get('/tasks', [TaskController::class, 'index'])->middleware('can:viewAny,App\Models\Task');
    Route::post('/tasks', [TaskController::class, 'store'])->middleware('can:create,App\Models\Task');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->middleware('can:view,task');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->middleware('can:update,task');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->middleware('can:delete,task');
    Route::get('Tasks/trashed', [TaskController::class, 'trashed']);
    Route::post('Tasks/{id}/restore', [TaskController::class, 'restore']);
    Route::delete('Tasks/{id}/forceDelete', [TaskController::class, 'forceDelete']);
    
    Route::post('/tasks/{task}/assign', [TaskController::class, 'AssignTask'])->middleware('can:assign,App\Models\Task');
    Route::put('/tasks/{task}/reassign', [TaskController::class, 'ReAssignTask'])->middleware('can:assign,App\Models\Task');
    Route::post('/tasks/{task}/attachement', [TaskController::class, 'storeAttachment']);
    
    ; Route::post('/task-dependencies', [TaskDependencyController::class, 'store']);

    Route::apiResource('users', UserController::class);
});

Route::middleware(['auth:api'])->group(function (){
Route::post('tasks/{id}/comments',[CommentController::class,'store']);
Route::patch('tasks/{task}/status',[TaskController::class,'updateStatus']);
});


Route::get('/reports/daily', [ReportController::class, 'dailyReport']);
Route::get('/reports/dispatch-daily', [ReportController::class, 'triggerDailyReport']);

Route::get('/reports/late', [ReportController::class, 'lateTasks']);
Route::get('/reports/user/{userId}', [ReportController::class, 'tasksByUser']);



