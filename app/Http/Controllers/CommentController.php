<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest\StoreCommentRequest as CommentRequestStoreCommentRequest;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Models;
use App\Services\ApiResponseService;
use Illuminate\Database\Eloquent;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentRequestStoreCommentRequest $request,$id)
    { $task=Task::findorFail($id);
       //   $task=Task::where('id',$request->task_id);
        $validrequest=$request->validated();
     //   $task=Task::where('id',$validrequest->task_id);
        
        $comment=$task->comments()->create([
             'content' => $validrequest['content'],
             'user_id' => Auth()->user()->id,
        ]);
      //  $comment->save();
        $task->comments()->save($comment);
        return ApiResponseService::success($comment, 'comment added successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
