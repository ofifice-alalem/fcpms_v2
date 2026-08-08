<?php

namespace App\Services;

use App\Enums\ConsultantStatus;
use App\Models\ConsultantLeave;
use App\Models\OfficialHoliday;
use App\Models\WorkScheduleTemplate;
use App\Repositories\Contracts\ConsultantLeaveRepositoryInterface;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use App\Repositories\Contracts\WorkScheduleRepositoryInterface;
use App\Helpers\ActivityLogger;
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

            $template = $this->scheduleRepo->createTemplateWithDays($data, $days);

            ActivityLogger::log(
                'create_schedule_template',
                'WorkScheduleTemplate',
                $template->id,
                "تم إنشاء قالب دوام جديد: {$template->name}",
                null,
                $template->toArray()
            );

            return $template;
        });
    }

    /**
     * Update an existing work schedule template (BR-007, BR-008).
     */
    public function updateScheduleTemplate(WorkScheduleTemplate $template, array $data): WorkScheduleTemplate
    {
        $template->load('days');
        $oldData = $template->toArray();

        return DB::transaction(function () use ($template, $data, $oldData) {
            if (!empty($data['is_default']) && !$template->is_default) {
                $this->scheduleRepo->resetDefaultTemplates();
            }

            $days = $data['days'] ?? [];
            $updated = $this->scheduleRepo->updateTemplateWithDays($template, $data, $days);

            ActivityLogger::log(
                'update_schedule_template',
                'WorkScheduleTemplate',
                $updated->id,
                "تم تعديل قالب الدوام: {$updated->name}",
                $oldData,
                $updated->toArray()
            );

            return $updated;
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

        $templateData = $template->toArray();
        $templateId = $template->id;

        return DB::transaction(function () use ($template, $templateData, $templateId) {
            $deleted = $this->scheduleRepo->deleteTemplate($template);

            if ($deleted) {
                ActivityLogger::log(
                    'delete_schedule_template',
                    'WorkScheduleTemplate',
                    $templateId,
                    "تم حذف قالب الدوام: {$templateData['name']}",
                    $templateData,
                    null
                );
            }

            return $deleted;
        });
    }

    /**
     * Record an official holiday (BR-012).
     */
    public function addOfficialHoliday(array $data): OfficialHoliday
    {
        return DB::transaction(function () use ($data) {
            $holiday = $this->scheduleRepo->addOfficialHoliday($data);

            ActivityLogger::log(
                'add_official_holiday',
                'OfficialHoliday',
                $holiday->id,
                "تم إضافة عطلة رسمية: {$holiday->name} ({$holiday->start_date})",
                null,
                $holiday->toArray()
            );

            return $holiday;
        });
    }

    /**
     * Update an official holiday.
     */
    public function updateOfficialHoliday(OfficialHoliday $holiday, array $data): OfficialHoliday
    {
        $oldData = $holiday->toArray();

        return DB::transaction(function () use ($holiday, $data, $oldData) {
            $updated = $this->scheduleRepo->updateOfficialHoliday($holiday, $data);

            ActivityLogger::log(
                'update_official_holiday',
                'OfficialHoliday',
                $updated->id,
                "تم تعديل العطلة الرسمية: {$updated->name}",
                $oldData,
                $updated->toArray()
            );

            return $updated;
        });
    }

    /**
     * Delete an official holiday.
     */
    public function deleteOfficialHoliday(OfficialHoliday $holiday): bool
    {
        $holidayData = $holiday->toArray();
        $holidayId = $holiday->id;

        return DB::transaction(function () use ($holiday, $holidayData, $holidayId) {
            $deleted = $this->scheduleRepo->deleteOfficialHoliday($holiday);

            if ($deleted) {
                ActivityLogger::log(
                    'delete_official_holiday',
                    'OfficialHoliday',
                    $holidayId,
                    "تم حذف العطلة الرسمية: {$holidayData['name']}",
                    $holidayData,
                    null
                );
            }

            return $deleted;
        });
    }

    /**
     * Record a consultant leave and update status to 'vacation' (BR-015).
     */
    public function recordConsultantLeave(array $data): ConsultantLeave
    {
        return DB::transaction(function () use ($data) {
            $leave = $this->leaveRepo->recordConsultantLeave($data);

            // Update consultant status to 'vacation' automatically ONLY if leave is active today
            $today = now()->toDateString();
            $consultant = $leave->consultant;
            if ($consultant) {
                if ($leave->start_date <= $today && $leave->end_date >= $today) {
                    $consultant->update([
                        'employment_status' => ConsultantStatus::VACATION,
                    ]);
                }
            }

            ActivityLogger::log(
                'record_consultant_leave',
                'ConsultantLeave',
                $leave->id,
                "تم تسجيل إجازة استشاري: " . ($consultant ? $consultant->full_name : $leave->consultant_id),
                null,
                $leave->toArray()
            );

            return $leave;
        });
    }

    /**
     * Update a consultant leave.
     */
    public function updateConsultantLeave(ConsultantLeave $leave, array $data): ConsultantLeave
    {
        $leave->load('consultant');
        $oldData = $leave->toArray();

        return DB::transaction(function () use ($leave, $data, $oldData) {
            $updated = $this->leaveRepo->updateConsultantLeave($leave, $data);
            $updated->load('consultant');

            ActivityLogger::log(
                'update_consultant_leave',
                'ConsultantLeave',
                $updated->id,
                "تم تعديل إجازة الاستشاري: " . ($updated->consultant ? $updated->consultant->full_name : ''),
                $oldData,
                $updated->toArray()
            );

            return $updated;
        });
    }

    /**
     * Delete a consultant leave.
     */
    public function deleteConsultantLeave(ConsultantLeave $leave): bool
    {
        $leaveData = $leave->toArray();
        $leaveId = $leave->id;

        return DB::transaction(function () use ($leave, $leaveData, $leaveId) {
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

            if ($deleted) {
                ActivityLogger::log(
                    'delete_consultant_leave',
                    'ConsultantLeave',
                    $leaveId,
                    "تم حذف إجازة استشاري",
                    $leaveData,
                    null
                );
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
