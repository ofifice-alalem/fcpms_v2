<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address',
        'city',
        'status',
        'notes',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(SiteVisit::class);
    }

    public function taskAssignments(): HasMany
    {
        return $this->hasMany(TaskSiteAssignment::class);
    }
}
