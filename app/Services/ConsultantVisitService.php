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

    public function startDay(Consultant $consultant, ?string $notes = null): DailyRecord
    {
        return $this->dailyRecordRepo->startDay($consultant, $notes);
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

    public function getAvailableOnDemandTasks(int $siteId, Consultant $consultant): Collection
    {
        return TaskDefinition::where('is_active', true)
            ->where('task_type', 'on_demand')
            ->where(function ($query) use ($siteId, $consultant) {
                $query->whereHas('siteAssignments', fn ($sq) => $sq->where('site_id', $siteId))
                    ->orWhereHas('consultantAssignments', fn ($cq) => $cq->where('consultant_id', $consultant->id))
                    ->orWhere(function ($gq) {
                        $gq->doesntHave('siteAssignments')->doesntHave('consultantAssignments');
                    });
            })
            ->get();
    }

    public function openSiteVisit(DailyRecord $dailyRecord, int $siteId, ?string $notes = null): SiteVisit
    {
        $existing = SiteVisit::where('daily_record_id', $dailyRecord->id)
            ->where('site_id', $siteId)
            ->first();

        if ($existing) {
            return $this->siteVisitRepo->findWithDetails($existing->id);
        }

        return $this->siteVisitRepo->openVisit($dailyRecord, $siteId, $notes);
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
        return $this->siteVisitRepo->saveTaskResponses($siteVisit, $responsesData, $attachmentsData);
    }

    public function getVisitDetails(int $visitId): ?SiteVisit
    {
        return $this->siteVisitRepo->findWithDetails($visitId);
    }

    public function cancelVisit(SiteVisit $siteVisit): bool
    {
        return $this->siteVisitRepo->cancelVisit($siteVisit);
    }
}
