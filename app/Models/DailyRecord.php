<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class DailyRecord extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'consultant_id',
        'work_date',
        'check_in_time',
        'check_out_time',
        'notes',
        'required_daily_tasks',
        'completed_daily_tasks',
        'completion_percentage',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'check_in_time' => 'datetime',
            'check_out_time' => 'datetime',
            'required_daily_tasks' => 'integer',
            'completed_daily_tasks' => 'integer',
            'completion_percentage' => 'decimal:2',
        ];
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(Consultant::class);
    }

    public function siteVisits(): HasMany
    {
        return $this->hasMany(SiteVisit::class);
    }
}
