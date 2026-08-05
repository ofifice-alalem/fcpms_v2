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
