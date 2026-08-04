<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskResponseValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_response_id',
        'task_component_id',
        'value',
    ];

    public function taskResponse(): BelongsTo
    {
        return $this->belongsTo(TaskResponse::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(TaskComponent::class, 'task_component_id');
    }
}
