<?php

namespace App\Services;

use App\Models\Consultant;
use App\Models\DailyRecord;
use App\Models\Site;
use App\Models\SiteVisit;
use App\Models\TaskDefinition;
use App\Repositories\Contracts\DailyRecordRepositoryInterface;
use App\Repositories\Contracts\SiteVisitRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ConsultantVisitService
{
    public function __construct(
        protected DailyRecordRepositoryInterface $dailyRecordRepo,
        protected SiteVisitRepositoryInterface $siteVisitRepo
    ) {}

    public function getTodayRecord(Consultant $consultant): DailyRecord
    {
        return $this->dailyRecordRepo->getTodayRecord($consultant);
    }

    public function getRecordForDate(Consultant $consultant, ?string $date = null): DailyRecord
    {
        return $this->dailyRecordRepo->getRecordForDate($consultant, $date);
    }

    public function startDay(Consultant $consultant, ?string $notes = null): DailyRecord
    {
        $record = $this->dailyRecordRepo->startDay($consultant, $notes);

        \App\Helpers\ActivityLogger::log(
            'start_day',
            'DailyRecord',
            $record->id,
            "قام الاستشاري {$consultant->full_name} ببدء يوميته الميدانية بتاريخ {$record->record_date}",
            null,
            ['daily_record_id' => $record->id, 'date' => $record->record_date]
        );

        return $record;
    }

    public function getAvailableSites(?DailyRecord $dailyRecord = null): Collection
    {
        $query = Site::where(function ($q) {
            $q->where('status', 'active')
              ->orWhere('status', \App\Enums\SiteStatus::ACTIVE ?? 'active');
        });

        if ($dailyRecord) {
            $visitedSiteIds = SiteVisit::where('daily_record_id', $dailyRecord->id)
                ->pluck('site_id')
                ->toArray();

            if (!empty($visitedSiteIds)) {
                $query->whereNotIn('id', $visitedSiteIds);
            }
        }

        return $query->select('id', 'name', 'code', 'address')->get();
    }

    public function getAvailableOnDemandTasks(int $siteId, Consultant $consultant, ?SiteVisit $siteVisit = null): Collection
    {
        $query = TaskDefinition::where('is_active', true)
            ->where('task_type', 'on_demand')
            ->where(function ($query) use ($siteId, $consultant) {
                $query->whereHas('siteAssignments', fn ($sq) => $sq->where('site_id', $siteId))
                    ->orWhereHas('consultantAssignments', fn ($cq) => $cq->where('consultant_id', $consultant->id))
                    ->orWhere(function ($gq) {
                        $gq->doesntHave('siteAssignments')->doesntHave('consultantAssignments');
                    });
            });

        if ($siteVisit) {
            $alreadyAddedIds = \App\Models\TaskResponse::where('site_visit_id', $siteVisit->id)
                ->pluck('task_definition_id')
                ->filter()
                ->toArray();

            if (!empty($alreadyAddedIds)) {
                $query->whereNotIn('id', $alreadyAddedIds);
            }
        }

        return $query->get();
    }

    public function openSiteVisit(DailyRecord $dailyRecord, int $siteId, ?string $notes = null): SiteVisit
    {
        $existing = SiteVisit::where('daily_record_id', $dailyRecord->id)
            ->where('site_id', $siteId)
            ->first();

        if ($existing) {
            return $this->siteVisitRepo->findWithDetails($existing->id);
        }

        $visit = $this->siteVisitRepo->openVisit($dailyRecord, $siteId, $notes);

        \App\Helpers\ActivityLogger::log(
            'open_site_visit',
            'SiteVisit',
            $visit->id,
            "تم فتح زيارة ميدانية جديدة في موقع: " . ($visit->site ? $visit->site->name : $siteId),
            null,
            ['site_visit_id' => $visit->id, 'site_id' => $siteId]
        );

        return $visit;
    }

    public function getActiveVisit(DailyRecord $dailyRecord): ?SiteVisit
    {
        return $this->siteVisitRepo->getActiveVisit($dailyRecord);
    }

    public function triggerOnDemandTask(SiteVisit $siteVisit, int $taskDefinitionId)
    {
        return $this->siteVisitRepo->triggerOnDemandTask($siteVisit, $taskDefinitionId);
    }

    public function saveTaskResponses(SiteVisit $siteVisit, array $responsesData, array $attachmentsData = []): SiteVisit
    {
        $visit = $this->siteVisitRepo->saveTaskResponses($siteVisit, $responsesData, $attachmentsData);

        \App\Helpers\ActivityLogger::log(
            'execute_site_visit',
            'SiteVisit',
            $visit->id,
            "تم تنفيذ واستكمال الزيارة الميدانية بحالة: {$visit->status}",
            null,
            ['site_visit_id' => $visit->id, 'status' => $visit->status, 'responses_count' => count($responsesData)]
        );

        return $visit;
    }

    public function getVisitDetails(int $visitId): ?SiteVisit
    {
        return $this->siteVisitRepo->findWithDetails($visitId);
    }

    public function cancelVisit(SiteVisit $siteVisit): bool
    {
        return $this->siteVisitRepo->cancelVisit($siteVisit);
    }

    public function removeOnDemandTask(SiteVisit $siteVisit, int $responseId): bool
    {
        return $this->siteVisitRepo->removeOnDemandTask($siteVisit, $responseId);
    }
}
