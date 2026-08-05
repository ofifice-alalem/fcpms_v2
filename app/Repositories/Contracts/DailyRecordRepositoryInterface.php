<?php

namespace App\Repositories\Contracts;

use App\Models\Consultant;
use App\Models\DailyRecord;
use Illuminate\Database\Eloquent\Collection;

interface DailyRecordRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get or create today's DailyRecord for a consultant.
     */
    public function getTodayRecord(Consultant $consultant): DailyRecord;

    /**
     * Start the daily record for a consultant for today.
     */
    public function startDay(Consultant $consultant, ?string $notes = null): DailyRecord;

    /**
     * Get consultant's daily records history.
     */
    public function getConsultantHistory(Consultant $consultant, int $limit = 30): Collection;
}
