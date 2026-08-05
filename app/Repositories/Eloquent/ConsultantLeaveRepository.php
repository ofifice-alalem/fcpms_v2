<?php

namespace App\Repositories\Eloquent;

use App\Models\ConsultantLeave;
use App\Repositories\Contracts\ConsultantLeaveRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ConsultantLeaveRepository extends BaseRepository implements ConsultantLeaveRepositoryInterface
{
    public function model(): string
    {
        return ConsultantLeave::class;
    }

    public function recordConsultantLeave(array $leaveData): ConsultantLeave
    {
        /** @var ConsultantLeave $leave */
        $leave = $this->model->create([
            'consultant_id' => $leaveData['consultant_id'],
            'start_date' => $leaveData['start_date'],
            'end_date' => $leaveData['end_date'],
            'reason' => $leaveData['reason'] ?? null,
            'notes' => $leaveData['notes'] ?? null,
        ]);

        return $leave->load('consultant');
    }

    public function getLeavesWithConsultant(): Collection
    {
        return ConsultantLeave::with('consultant')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function updateConsultantLeave(ConsultantLeave $leave, array $leaveData): ConsultantLeave
    {
        $leave->update([
            'consultant_id' => $leaveData['consultant_id'] ?? $leave->consultant_id,
            'start_date' => $leaveData['start_date'] ?? $leave->start_date,
            'end_date' => $leaveData['end_date'] ?? $leave->end_date,
            'reason' => $leaveData['reason'] ?? null,
            'notes' => $leaveData['notes'] ?? null,
        ]);

        return $leave->fresh('consultant');
    }

    public function deleteConsultantLeave(ConsultantLeave $leave): bool
    {
        return (bool) $leave->delete();
    }
}
