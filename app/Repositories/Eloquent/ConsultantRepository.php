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
     * Get paginated consultants with filters.
     */
    public function getFilteredConsultants(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['user', 'workScheduleTemplate']);

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
}
