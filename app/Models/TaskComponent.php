<?php

namespace App\Models;

use App\Enums\ComponentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_definition_id',
        'component_type',
        'label',
        'placeholder',
        'display_order',
        'is_required',
        'conditional_parent_id',
        'conditional_value',
        'visibility_component_id',
        'visibility_option_id',
    ];

    protected function casts(): array
    {
        return [
            'component_type' => ComponentType::class,
            'is_required' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function taskDefinition(): BelongsTo
    {
        return $this->belongsTo(TaskDefinition::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(TaskComponentOption::class)->orderBy('display_order');
    }

    public function parentComponent(): BelongsTo
    {
        return $this->belongsTo(TaskComponent::class, 'conditional_parent_id');
    }

    public function childComponents(): HasMany
    {
        return $this->hasMany(TaskComponent::class, 'conditional_parent_id');
    }
}
