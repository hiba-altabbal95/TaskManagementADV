<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content','user_id'
    ];

   /**
    *Get the user who add this comment
    *@return \Illuminate\Database\Eloquent\Relations\BelongsTo 
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
     /**
     * Get all of the models that own comments.
     */
    public function commentable()
    {
        return $this->morphTo();
    }
}
