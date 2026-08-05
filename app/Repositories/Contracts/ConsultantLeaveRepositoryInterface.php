<?php

namespace App\Repositories\Contracts;

use App\Models\ConsultantLeave;
use Illuminate\Database\Eloquent\Collection;

interface ConsultantLeaveRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Record a new leave for a consultant.
     */
    public function recordConsultantLeave(array $leaveData): ConsultantLeave;

    /**
     * Get all consultant leaves loaded with consultant relation.
     */
    public function getLeavesWithConsultant(): Collection;

    /**
     * Update a consultant leave.
     */
    public function updateConsultantLeave(ConsultantLeave $leave, array $leaveData): ConsultantLeave;

    /**
     * Delete a consultant leave.
     */
    public function deleteConsultantLeave(ConsultantLeave $leave): bool;
}
