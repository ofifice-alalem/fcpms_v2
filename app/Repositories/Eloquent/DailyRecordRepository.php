<?php

namespace App\Repositories\Eloquent;

use App\Models\Consultant;
use App\Models\DailyRecord;
use App\Repositories\Contracts\DailyRecordRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class DailyRecordRepository extends BaseRepository implements DailyRecordRepositoryInterface
{
    public function model(): string
    {
        return DailyRecord::class;
    }

    public function getTodayRecord(Consultant $consultant): DailyRecord
    {
        $today = Carbon::today()->toDateString();

        return DailyRecord::firstOrCreate(
            [
                'consultant_id' => $consultant->id,
                'work_date' => $today,
            ],
            [
                'required_daily_tasks' => 0,
                'completed_daily_tasks' => 0,
                'completion_percentage' => 0.00,
            ]
        );
    }

    public function startDay(Consultant $consultant, ?string $notes = null): DailyRecord
    {
        $record = $this->getTodayRecord($consultant);

        if (!$record->check_in_time) {
            $record->update([
                'check_in_time' => Carbon::now(),
                'notes' => $notes ?: $record->notes,
            ]);
        }

        return $record->fresh(['consultant', 'siteVisits.site']);
    }

    public function getConsultantHistory(Consultant $consultant, int $limit = 30): Collection
    {
        return DailyRecord::where('consultant_id', $consultant->id)
            ->with(['siteVisits.site', 'siteVisits.taskResponses'])
            ->orderBy('work_date', 'desc')
            ->limit($limit)
            ->get();
    }
}
