<?php

namespace App\Models;

use App\Enums\SiteStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'address',
        'city',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => SiteStatus::class,
        ];
    }

    public function visits(): HasMany
    {
        return $this->hasMany(SiteVisit::class);
    }

    public function taskAssignments(): HasMany
    {
        return $this->hasMany(TaskSiteAssignment::class);
    }
}
