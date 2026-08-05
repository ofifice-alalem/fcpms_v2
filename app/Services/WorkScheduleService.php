<?php

namespace App\Services;

use App\Enums\ConsultantStatus;
use App\Models\ConsultantLeave;
use App\Models\OfficialHoliday;
use App\Models\WorkScheduleTemplate;
use App\Repositories\Contracts\ConsultantLeaveRepositoryInterface;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use App\Repositories\Contracts\WorkScheduleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkScheduleService
{
    public function __construct(
        protected WorkScheduleRepositoryInterface $scheduleRepo,
        protected ConsultantLeaveRepositoryInterface $leaveRepo,
        protected ConsultantRepositoryInterface $consultantRepo
    ) {}

    /**
     * Get all page data for work schedules overview.
     */
    public function getWorkSchedulesPageData(): array
    {
        return [
            'templates' => $this->scheduleRepo->getAllTemplatesWithDays(),
            'officialHolidays' => $this->scheduleRepo->getOfficialHolidays(),
            'consultantLeaves' => $this->leaveRepo->getLeavesWithConsultant(),
            'activeConsultants' => $this->consultantRepo->getActiveConsultants(),
        ];
    }

    /**
     * Create a new work schedule template (BR-007, BR-008).
     */
    public function createScheduleTemplate(array $data): WorkScheduleTemplate
    {
        return DB::transaction(function () use ($data) {
            if (!empty($data['is_default'])) {
                $this->scheduleRepo->resetDefaultTemplates();
            }

            // Default to 7 days if not fully provided
            $days = $data['days'] ?? $this->getDefaultDaysConfig();

            return $this->scheduleRepo->createTemplateWithDays($data, $days);
        });
    }

    /**
     * Update an existing work schedule template (BR-007, BR-008).
     */
    public function updateScheduleTemplate(WorkScheduleTemplate $template, array $data): WorkScheduleTemplate
    {
        return DB::transaction(function () use ($template, $data) {
            if (!empty($data['is_default']) && !$template->is_default) {
                $this->scheduleRepo->resetDefaultTemplates();
            }

            $days = $data['days'] ?? [];

            return $this->scheduleRepo->updateTemplateWithDays($template, $data, $days);
        });
    }

    /**
     * Delete a work schedule template (BR-006: Protection check).
     */
    public function deleteScheduleTemplate(WorkScheduleTemplate $template): bool
    {
        // BR-006: Check if template is assigned to any active consultants
        $assignedCount = $template->consultants()->where('employment_status', ConsultantStatus::ACTIVE)->count();

        if ($assignedCount > 0) {
            throw ValidationException::withMessages([
                'template' => 'لا يمكن حذف قالب الدوام لوجود استشاريين نشطين مسندين إليه حالياً. يرجى إعادة تعيينهم أولاً.',
            ]);
        }

        return DB::transaction(function () use ($template) {
            return $this->scheduleRepo->deleteTemplate($template);
        });
    }

    /**
     * Record an official holiday (BR-012).
     */
    public function addOfficialHoliday(array $data): OfficialHoliday
    {
        return DB::transaction(function () use ($data) {
            return $this->scheduleRepo->addOfficialHoliday($data);
        });
    }

    /**
     * Update an official holiday.
     */
    public function updateOfficialHoliday(OfficialHoliday $holiday, array $data): OfficialHoliday
    {
        return DB::transaction(function () use ($holiday, $data) {
            return $this->scheduleRepo->updateOfficialHoliday($holiday, $data);
        });
    }

    /**
     * Delete an official holiday.
     */
    public function deleteOfficialHoliday(OfficialHoliday $holiday): bool
    {
        return DB::transaction(function () use ($holiday) {
            return $this->scheduleRepo->deleteOfficialHoliday($holiday);
        });
    }

    /**
     * Record a consultant leave and update status to 'vacation' (BR-015).
     */
    public function recordConsultantLeave(array $data): ConsultantLeave
    {
        return DB::transaction(function () use ($data) {
            $leave = $this->leaveRepo->recordConsultantLeave($data);

            // Update consultant status to 'vacation' automatically (BR-015)
            $consultant = $leave->consultant;
            if ($consultant) {
                $consultant->update([
                    'employment_status' => ConsultantStatus::VACATION,
                ]);
            }

            return $leave;
        });
    }

    /**
     * Update a consultant leave.
     */
    public function updateConsultantLeave(ConsultantLeave $leave, array $data): ConsultantLeave
    {
        return DB::transaction(function () use ($leave, $data) {
            return $this->leaveRepo->updateConsultantLeave($leave, $data);
        });
    }

    /**
     * Delete a consultant leave.
     */
    public function deleteConsultantLeave(ConsultantLeave $leave): bool
    {
        return DB::transaction(function () use ($leave) {
            $consultant = $leave->consultant;
            $deleted = $this->leaveRepo->deleteConsultantLeave($leave);

            if ($consultant) {
                // Check if consultant still has any active/future leave
                $today = now()->toDateString();
                $hasActiveLeave = $consultant->leaves()
                    ->where('end_date', '>=', $today)
                    ->exists();

                if (!$hasActiveLeave && $consultant->employment_status === ConsultantStatus::VACATION) {
                    $consultant->update([
                        'employment_status' => ConsultantStatus::ACTIVE,
                    ]);
                }
            }

            return $deleted;
        });
    }

    /**
     * Generate standard 7 days array (0=Sun to 6=Sat).
     */
    protected function getDefaultDaysConfig(): array
    {
        return [
            ['day_of_week' => 0, 'is_working_day' => true],  // Sunday
            ['day_of_week' => 1, 'is_working_day' => true],  // Monday
            ['day_of_week' => 2, 'is_working_day' => true],  // Tuesday
            ['day_of_week' => 3, 'is_working_day' => true],  // Wednesday
            ['day_of_week' => 4, 'is_working_day' => true],  // Thursday
            ['day_of_week' => 5, 'is_working_day' => false], // Friday
            ['day_of_week' => 6, 'is_working_day' => false], // Saturday
        ];
    }
}
