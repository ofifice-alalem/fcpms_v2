<?php

namespace App\Repositories\Contracts;

use App\Models\Consultant;
use Illuminate\Pagination\LengthAwarePaginator;

interface ConsultantRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get paginated consultants with filters (search, status, specialization, sort).
     */
    public function getFilteredConsultants(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find consultant by employee number.
     */
    public function findByEmployeeNumber(string $employeeNumber): ?Consultant;

    /**
     * Get next sequential employee number sequence integer.
     */
    public function getNextEmployeeNumberSequence(): int;

    /**
     * Check if consultant has pending unapproved visits.
     */
    public function hasPendingVisits(int $consultantId): bool;

    /**
     * Get all active consultants.
     */
    public function getActiveConsultants(): \Illuminate\Database\Eloquent\Collection;
}
