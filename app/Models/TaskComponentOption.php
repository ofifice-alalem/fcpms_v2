<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskComponentOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_component_id',
        'option_label',
        'option_value',
        'display_order',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'is_default' => 'boolean',
        ];
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(TaskComponent::class, 'task_component_id');
    }
}
