<?php

namespace App\Models;

use App\Enums\TaskType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class TaskDefinition extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'title',
        'description',
        'task_type',
        'is_active',
        'display_order',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'task_type' => TaskType::class,
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function components(): HasMany
    {
        return $this->hasMany(TaskComponent::class)->orderBy('display_order');
    }

    public function taskComponents(): HasMany
    {
        return $this->hasMany(TaskComponent::class)->orderBy('display_order');
    }

    public function siteAssignments(): HasMany
    {
        return $this->hasMany(TaskSiteAssignment::class);
    }

    public function consultantAssignments(): HasMany
    {
        return $this->hasMany(TaskConsultantAssignment::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(TaskResponse::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
