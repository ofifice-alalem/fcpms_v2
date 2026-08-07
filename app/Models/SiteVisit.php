<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class SiteVisit extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'daily_record_id',
        'site_id',
        'status',
        'visit_started_at',
        'visit_finished_at',
        'notes',
    ];

    protected $appends = [
        'status_name',
        'check_in_time',
        'check_out_time',
        'daily_tasks_count',
        'total_daily_tasks_count',
        'on_demand_tasks_count',
    ];

    protected function casts(): array
    {
        return [
            'visit_started_at' => 'datetime',
            'visit_finished_at' => 'datetime',
        ];
    }

    public function getStatusNameAttribute(): string
    {
        $status = $this->status ?? ($this->visit_finished_at ? 'completed' : 'in_progress');
        return $status === 'completed' ? 'مكتملة' : 'قيد التنفيذ';
    }

    public function getCheckInTimeAttribute(): ?string
    {
        if ($this->visit_started_at) {
            return $this->visit_started_at->toIso8601String();
        }
        if ($this->dailyRecord && $this->dailyRecord->check_in_time) {
            return \Illuminate\Support\Carbon::parse($this->dailyRecord->check_in_time)->toIso8601String();
        }
        return $this->created_at ? $this->created_at->toIso8601String() : null;
    }

    public function getCheckOutTimeAttribute(): ?string
    {
        if ($this->visit_finished_at) {
            return $this->visit_finished_at->toIso8601String();
        }

        if ($this->relationLoaded('taskResponses') && $this->taskResponses->isNotEmpty()) {
            $latestTask = $this->taskResponses
                ->map(fn($r) => $r->completed_at ?? $r->submitted_at ?? $r->updated_at)
                ->filter()
                ->max();

            if ($latestTask) {
                return \Illuminate\Support\Carbon::parse($latestTask)->toIso8601String();
            }
        }

        if ($this->updated_at && $this->created_at && $this->updated_at->gt($this->created_at)) {
            return $this->updated_at->toIso8601String();
        }

        if ($this->dailyRecord && $this->dailyRecord->check_out_time) {
            return \Illuminate\Support\Carbon::parse($this->dailyRecord->check_out_time)->toIso8601String();
        }

        return null;
    }

    public function getDailyTasksCountAttribute(): int
    {
        if ($this->relationLoaded('taskResponses')) {
            $firstResp = $this->taskResponses->first();
            if ($firstResp && $firstResp->relationLoaded('taskDefinition')) {
                return $this->taskResponses->filter(function ($resp) {
                    $taskDef = $resp->taskDefinition;
                    if (!$taskDef) {
                        return false;
                    }
                    $type = is_object($taskDef->task_type) ? $taskDef->task_type->value : (string) $taskDef->task_type;
                    $hasValues = $resp->relationLoaded('values') ? ($resp->values && $resp->values->count() > 0) : ($resp->values()->count() > 0);
                    return $type === 'daily' && ($resp->status === 'submitted' || $hasValues);
                })->count();
            }
        }
        return $this->taskResponses()
            ->whereHas('taskDefinition', fn($q) => $q->where('task_type', 'daily'))
            ->where(fn($q) => $q->where('status', 'submitted')->orWhereHas('values'))
            ->count();
    }

    public function getTotalDailyTasksCountAttribute(): int
    {
        if ($this->status === 'completed') {
            return $this->daily_tasks_count;
        }

        if ($this->relationLoaded('taskResponses')) {
            $firstResp = $this->taskResponses->first();
            if ($firstResp && $firstResp->relationLoaded('taskDefinition')) {
                return $this->taskResponses->filter(function ($resp) {
                    $taskDef = $resp->taskDefinition;
                    if (!$taskDef) {
                        return false;
                    }
                    $type = is_object($taskDef->task_type) ? $taskDef->task_type->value : (string) $taskDef->task_type;
                    return $type === 'daily';
                })->count();
            }
        }
        return $this->taskResponses()->whereHas('taskDefinition', fn($q) => $q->where('task_type', 'daily'))->count();
    }

    public function getOnDemandTasksCountAttribute(): int
    {
        if ($this->relationLoaded('taskResponses')) {
            return $this->taskResponses->filter(function ($resp) {
                $taskDef = $resp->taskDefinition ?? $resp->task_definition ?? null;
                if (!$taskDef) {
                    return false;
                }
                $type = is_object($taskDef->task_type) ? $taskDef->task_type->value : (string) $taskDef->task_type;
                return $type === 'on_demand';
            })->count();
        }
        return $this->taskResponses()->whereHas('taskDefinition', fn($q) => $q->where('task_type', 'on_demand'))->count();
    }

    public function dailyRecord(): BelongsTo
    {
        return $this->belongsTo(DailyRecord::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function taskResponses(): HasMany
    {
        return $this->hasMany(TaskResponse::class);
    }
}
