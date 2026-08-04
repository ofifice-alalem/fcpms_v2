<?php

namespace App\Rules;

use App\Enums\AttendanceStatus;

class BusinessRuleEvaluator
{
    /**
     * Evaluates attendance status using the strict BR-018 hierarchy:
     * Official Holiday -> Work Schedule -> Leave -> Activity -> Absence.
     *
     * @param bool $isOfficialHoliday  (BR-014)
     * @param bool $isWorkingDay       (BR-010)
     * @param bool $isOnLeave          (BR-016)
     * @param bool $hasRecordedActivity (BR-019)
     * @return AttendanceStatus
     */
    public static function evaluateAttendanceStatus(
        bool $isOfficialHoliday,
        bool $isWorkingDay,
        bool $isOnLeave,
        bool $hasRecordedActivity
    ): AttendanceStatus {
        if ($isOfficialHoliday) {
            return AttendanceStatus::HOLIDAY;
        }

        if (! $isWorkingDay) {
            return AttendanceStatus::NON_WORKING_DAY;
        }

        if ($isOnLeave) {
            return AttendanceStatus::LEAVE;
        }

        if ($hasRecordedActivity) {
            return AttendanceStatus::PRESENT;
        }

        return AttendanceStatus::ABSENT;
    }

    /**
     * Calculates completion percentage from daily tasks only.
     * Implements BR-043, BR-058.
     * On-Demand tasks are excluded (BR-048).
     *
     * @param int $completedDailyTasks
     * @param int $requiredDailyTasks
     * @return float
     */
    public static function calculateCompletionPercentage(int $completedDailyTasks, int $requiredDailyTasks): float
    {
        if ($requiredDailyTasks <= 0) {
            return 100.00;
        }

        $percentage = ($completedDailyTasks / $requiredDailyTasks) * 100;

        return round(min($percentage, 100.00), 2);
    }
}
