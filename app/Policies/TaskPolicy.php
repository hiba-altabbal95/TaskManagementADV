<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\Contracts\Providers\Auth;

class TaskPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        // Only allow users with the 'admin' role to view any tasks
        return $user->hasRole('admin');
    }
    
    public function view(User $user, Task $task)
    {  
        $user= $user=User::findorfail(Auth()->user());
        //users can view thier own task
        return $user->id === $task->user_id;
    }

    public function create(User $user)
    {
       //only admin can create a task
        return $user->hasRole('admin');
    }

    public function updateAll(User $user)
    {
       //only admin can update all task info
        return $user->hasRole('admin');
    }

    public function update(User $user, Task $task)
    {
        return $user->hasRole('admin');
    }

    public function updateStatus(User $user, Task $task)
    {
        // Get the authenticated user
        $authUser =Auth()->user();
    
        // Ensure that the authenticated user can update the status of the task they are assigned to
        return $authUser->id === $task->assigned_to;
    }
    

    public function delete(User $user, Task $task)
    {
        //only admin can delete a task
        return $user->hasRole('admin');
    }

    public function assign(User $user)
    {
        //only admin can assign a task to user
        return $user->hasRole('admin');
    }

    public function upload(User $user)
    {
        //only admin can assign a task to user
        return $user->hasRole('admin');
    }
    
    public function trash(User $user)
    {
      
        return $user->hasRole('admin');
    }

    public function restore(User $user)
    {
      
        return $user->hasRole('admin');
    }

    public function force(User $user)
    {
      
        return $user->hasRole('admin');
    }
}
