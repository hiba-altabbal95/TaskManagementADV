<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStatus\HasStatuses;

class Task extends Model
{
    use HasFactory,HasStatuses,SoftDeletes;
    

    protected $fillable=['title','description','type','status','priority','date_due','assigned_to','attachment'];

    /**
     * Get the user to whom the task is assigned.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
 

    /**
     * Get all status updates for the task.
     */
    public function statusUpdates(): HasMany
    {
        return $this->hasMany(TaskStatusUpdate::class);
    }

    /** * Method to change the status of a task and log the update. */
    public function changeStatus(string $newStatus)
     {
         $this->update(['status' => $newStatus]); 
         $this->statusUpdates()->create(['status' => $newStatus]);
     }


     /**
     * Get all of the task's comments.
     */
    public function comments() :MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get all tasks that the current task is dependent on
     */
    public function dependencies()
    {
        return $this->hasMany(TaskDependency::class, 'task_id');
    }

    /**
     * Get all tasks that dependend on the current task
     */
    public function dependents()
    {
        return $this->hasMany(TaskDependency::class, 'dependent_task_id');
    }
   
    /**
     * when this task status become completed 
     * all tast dependent on it will change thier statu from block to open
     */
    public function markAsCompleted()
    {
        $this->changeStatus('Completed');
    
        // Update status of dependent tasks
        foreach ($this->dependents as $dependency) {
            $dependentTask = $dependency->task;
            if ($dependentTask->status === 'Blocked') {
                $dependentTask->changeStatus('Open');
            }
        }
    }
    


    /**
     * Scope a query to only include tasks of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterType($query, $type)
    {
        return $query->where('type', $type);
    }


    /**
     * Scope a query to only include tasks of a given status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterStatus($query, $status)
    {
        return $query->where('status', $status);
    }


    /**
     * Scope a query to only include tasks assigned to user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int assigned_to
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterAssignedTo($query, $assignedTo)
    {
        return $query->where('assigned_to', $assignedTo);
    }

    /**
     * Scope a query to only include tasks of a given date_due.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  date $dateDue
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterDateDue($query, $dateDue)
    {
        return $query->whereDate('date_due', $dateDue);
    }


    /**
     * Scope a query to only include tasks of a given priority.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $priority
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Combine all filters
    public function scopeFilter($query, $filters)
    {
        return $query->when($filters['type'] ?? null, function ($query, $type) {
            $query->filterType($type);
        })->when($filters['status'] ?? null, function ($query, $status) {
            $query->filterStatus($status);
        })->when($filters['assigned_to'] ?? null, function ($query, $assignedTo) {
            $query->filterAssignedTo($assignedTo);
        })->when($filters['date_due'] ?? null, function ($query, $dateDue) {
            $query->filterDateDue($dateDue);
        })->when($filters['priority'] ?? null, function ($query, $priority) {
            $query->filterPriority($priority);
        });
    }

}
