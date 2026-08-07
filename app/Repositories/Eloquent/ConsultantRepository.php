<?php

namespace App\Repositories\Eloquent;

use App\Models\Consultant;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConsultantRepository extends BaseRepository implements ConsultantRepositoryInterface
{
    public function model(): string
    {
        return Consultant::class;
    }

    /**
     * Synchronize consultant leave status automatically based on current date.
     */
    public function syncLeaveStatuses(): void
    {
        $today = now()->toDateString();

        // 1. Revert consultants marked as 'vacation' whose leave expired or has not started
        $vacationConsultants = $this->model->newQuery()
            ->where('employment_status', \App\Enums\ConsultantStatus::VACATION)
            ->get();

        foreach ($vacationConsultants as $consultant) {
            $hasActiveLeave = $consultant->leaves()
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->exists();

            if (!$hasActiveLeave) {
                $consultant->update([
                    'employment_status' => \App\Enums\ConsultantStatus::ACTIVE,
                ]);
            }
        }

        // 2. Mark active consultants as 'vacation' if they have an active leave today
        $activeConsultants = $this->model->newQuery()
            ->where('employment_status', \App\Enums\ConsultantStatus::ACTIVE)
            ->get();

        foreach ($activeConsultants as $consultant) {
            $hasActiveLeave = $consultant->leaves()
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->exists();

            if ($hasActiveLeave) {
                $consultant->update([
                    'employment_status' => \App\Enums\ConsultantStatus::VACATION,
                ]);
            }
        }
    }

    /**
     * Get paginated consultants with filters.
     */
    public function getFilteredConsultants(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $this->syncLeaveStatuses();

        $query = $this->model->newQuery()
            ->with(['user', 'workScheduleTemplate.days', 'leaves']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('employment_status', $filters['status']);
        }

        if (!empty($filters['specialization'])) {
            $query->where('specialization', $filters['specialization']);
        }

        // Apply Sort
        $sort = $filters['sort'] ?? 'latest';
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('full_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('full_name', 'desc');
                break;
            case 'emp_asc':
                $query->orderBy('employee_number', 'asc');
                break;
            case 'oldest':
                $query->orderBy('id', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Find consultant by employee number.
     */
    public function findByEmployeeNumber(string $employeeNumber): ?Consultant
    {
        return $this->model->where('employee_number', $employeeNumber)->first();
    }

    /**
     * Get next sequential employee number sequence integer for BR-003.
     */
    public function getNextEmployeeNumberSequence(): int
    {
        $maxId = $this->model->withTrashed()->max('id') ?? 0;
        return $maxId + 1001;
    }

    /**
     * Check if consultant has pending unfinished visits (BR-022).
     */
    public function hasPendingVisits(int $consultantId): bool
    {
        if (!Schema::hasTable('site_visits') || !Schema::hasTable('daily_records')) {
            return false;
        }

        return DB::table('site_visits')
            ->join('daily_records', 'site_visits.daily_record_id', '=', 'daily_records.id')
            ->where('daily_records.consultant_id', $consultantId)
            ->whereNull('site_visits.visit_finished_at')
            ->exists();
    }

    /**
     * Get all active consultants.
     */
    public function getActiveConsultants(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->newQuery()
            ->where('employment_status', \App\Enums\ConsultantStatus::ACTIVE)
            ->orderBy('full_name', 'asc')
            ->get();
    }
}
