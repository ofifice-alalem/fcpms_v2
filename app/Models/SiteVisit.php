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
