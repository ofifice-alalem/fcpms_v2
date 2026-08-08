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
        $site = \App\Models\Site::find($siteId);
        $siteName = $site ? $site->name : "موقع #{$siteId}";

        \App\Helpers\ActivityLogger::log(
            'open_site_visit',
            'SiteVisit',
            $visit->id,
            "تم فتح زيارة ميدانية جديدة في موقع: {$siteName}",
            ['status' => 'pending', 'site_name' => $siteName],
            [
                'site_visit_id' => $visit->id,
                'site_id' => $siteId,
                'site_name' => $siteName,
                'status' => $visit->status ?? 'in_progress',
                'notes' => $notes
            ]
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
        $siteName = $siteVisit->site ? $siteVisit->site->name : "موقع #{$siteVisit->site_id}";
        $oldStatus = $siteVisit->getOriginal('status') ?? 'in_progress';
        $oldResponsesCount = \App\Models\TaskResponse::where('site_visit_id', $siteVisit->id)->count();
        $oldResponsesSummary = \App\Models\TaskResponse::where('site_visit_id', $siteVisit->id)
            ->with(['taskDefinition', 'values.component'])
            ->get()
            ->map(function ($r) {
                $taskTitle = $r->taskDefinition ? $r->taskDefinition->title : 'مهمة ميدانية';
                if ($r->values && $r->values->isNotEmpty()) {
                    $vals = $r->values->map(fn($v) => ($v->component ? $v->component->label : 'إدخال') . ': ' . ($v->value ?? '—'))->implode('، ');
                    return "{$taskTitle} ({$vals})";
                }
                return "{$taskTitle}: تم الإنجاز والتنفيذ";
            })
            ->implode(' | ');

        $visit = $this->siteVisitRepo->saveTaskResponses($siteVisit, $responsesData, $attachmentsData);

        $newResponsesSummary = \App\Models\TaskResponse::where('site_visit_id', $visit->id)
            ->with(['taskDefinition', 'values.component'])
            ->get()
            ->map(function ($r) {
                $taskTitle = $r->taskDefinition ? $r->taskDefinition->title : 'مهمة ميدانية';
                if ($r->values && $r->values->isNotEmpty()) {
                    $vals = $r->values->map(fn($v) => ($v->component ? $v->component->label : 'إدخال') . ': ' . ($v->value ?? '—'))->implode('، ');
                    return "{$taskTitle} ({$vals})";
                }
                return "{$taskTitle}: تم الإنجاز والتنفيذ";
            })
            ->implode(' | ');

        \App\Helpers\ActivityLogger::log(
            'execute_site_visit',
            'SiteVisit',
            $visit->id,
            "تم تنفيذ واستكمال الزيارة الميدانية في موقع: {$siteName} (حالة: {$visit->status})",
            [
                'site_id' => $visit->site_id,
                'site_name' => $siteName,
                'status' => $oldStatus,
                'previous_responses_count' => $oldResponsesCount,
                'previous_tasks_details' => $oldResponsesSummary ?: '// لا توجد مهام منفذة سابقة'
            ],
            [
                'site_id' => $visit->site_id,
                'site_name' => $siteName,
                'status' => $visit->status,
                'submitted_responses_count' => count($responsesData),
                'tasks_details' => $newResponsesSummary ?: 'تم تسجيل الاستجابات والمرفقات بنجاح'
            ]
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
