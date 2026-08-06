<?php

namespace App\Repositories\Eloquent;

use App\Models\DailyRecord;
use App\Models\SiteVisit;
use App\Models\TaskAttachment;
use App\Models\TaskDefinition;
use App\Models\TaskResponse;
use App\Models\TaskResponseValue;
use App\Repositories\Contracts\SiteVisitRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SiteVisitRepository extends BaseRepository implements SiteVisitRepositoryInterface
{
    public function model(): string
    {
        return SiteVisit::class;
    }

    public function getActiveVisit(DailyRecord $dailyRecord): ?SiteVisit
    {
        return SiteVisit::where('daily_record_id', $dailyRecord->id)
            ->where(function ($query) {
                $query->where('status', 'in_progress')
                    ->orWhereNull('visit_finished_at');
            })
            ->with([
                'site',
                'taskResponses.taskDefinition.components.options',
                'taskResponses.values',
                'taskResponses.attachments',
            ])
            ->first();
    }

    public function hasVisitedSiteToday(DailyRecord $dailyRecord, int $siteId): bool
    {
        return SiteVisit::where('daily_record_id', $dailyRecord->id)
            ->where('site_id', $siteId)
            ->exists();
    }

    public function openVisit(DailyRecord $dailyRecord, int $siteId, ?string $notes = null): SiteVisit
    {
        return DB::transaction(function () use ($dailyRecord, $siteId, $notes) {
            $visit = SiteVisit::firstOrCreate([
                'daily_record_id' => $dailyRecord->id,
                'site_id' => $siteId,
            ], [
                'status' => 'in_progress',
                'visit_started_at' => Carbon::now(),
                'notes' => $notes,
            ]);

            // Auto-load all active 'daily' tasks for this site or consultant (BR-019)
            $dailyTasks = TaskDefinition::where('is_active', true)
                ->where('task_type', 'daily')
                ->where(function ($q) use ($siteId, $dailyRecord) {
                    $q->whereHas('siteAssignments', fn ($sq) => $sq->where('site_id', $siteId))
                      ->orWhereHas('consultantAssignments', fn ($cq) => $cq->where('consultant_id', $dailyRecord->consultant_id))
                      ->orWhere(function ($gq) {
                          $gq->doesntHave('siteAssignments')->doesntHave('consultantAssignments');
                      });
                })
                ->get();

            foreach ($dailyTasks as $task) {
                TaskResponse::firstOrCreate([
                    'site_visit_id' => $visit->id,
                    'task_definition_id' => $task->id,
                ], [
                    'status' => 'draft',
                ]);
            }

            // Recalculate required tasks on daily record
            $totalDaily = SiteVisit::where('daily_record_id', $dailyRecord->id)
                ->withCount(['taskResponses as daily_count' => function ($q) {
                    $q->whereHas('taskDefinition', fn ($tq) => $tq->where('task_type', 'daily'));
                }])
                ->get()
                ->sum('daily_count');

            $dailyRecord->update(['required_daily_tasks' => $totalDaily]);

            return $this->findWithDetails($visit->id);
        });
    }

    public function triggerOnDemandTask(SiteVisit $siteVisit, int $taskDefinitionId): TaskResponse
    {
        return TaskResponse::firstOrCreate([
            'site_visit_id' => $siteVisit->id,
            'task_definition_id' => $taskDefinitionId,
        ], [
            'status' => 'draft',
        ]);
    }

    public function findWithDetails(int $visitId): ?SiteVisit
    {
        return SiteVisit::where('id', $visitId)
            ->with([
                'site',
                'dailyRecord',
                'taskResponses.taskDefinition.components.options',
                'taskResponses.values',
                'taskResponses.attachments',
            ])
            ->first();
    }

    public function cancelVisit(SiteVisit $siteVisit): bool
    {
        return (bool) DB::transaction(function () use ($siteVisit) {
            $dailyRecord = $siteVisit->dailyRecord;

            $responses = $siteVisit->taskResponses()->get();
            foreach ($responses as $resp) {
                $resp->values()->delete();
                $resp->attachments()->delete();
                $resp->delete();
            }

            $deleted = $siteVisit->delete();

            if ($dailyRecord) {
                $completedCount = TaskResponse::whereHas('siteVisit', fn ($q) => $q->where('daily_record_id', $dailyRecord->id))
                    ->where('status', 'submitted')
                    ->count();

                $totalRequired = max(1, $dailyRecord->required_daily_tasks);
                $percentage = min(100, round(($completedCount / $totalRequired) * 100, 2));

                $dailyRecord->update([
                    'completed_daily_tasks' => $completedCount,
                    'completion_percentage' => $percentage,
                ]);
            }

            return $deleted;
        });
    }

    public function saveTaskResponses(SiteVisit $siteVisit, array $responsesData, array $attachmentsData = []): SiteVisit
    {
        return DB::transaction(function () use ($siteVisit, $responsesData, $attachmentsData) {
            foreach ($responsesData as $key => $resItem) {
                if (!is_array($resItem) || empty($resItem['task_definition_id'])) {
                    continue;
                }

                $taskDefinitionId = $resItem['task_definition_id'];
                $values = $resItem['values'] ?? [];

                $taskResponse = TaskResponse::firstOrCreate([
                    'site_visit_id' => $siteVisit->id,
                    'task_definition_id' => $taskDefinitionId,
                ]);

                // Save or update component values
                $hasNonEmptyValue = false;
                foreach ($values as $componentId => $val) {
                    $valStr = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : (string) $val;

                    if (is_array($val)) {
                        if (!empty($val)) {
                            $hasNonEmptyValue = true;
                        }
                    } else if ($val !== null && trim((string)$val) !== '' && trim((string)$val) !== '[]' && trim((string)$val) !== 'null') {
                        $hasNonEmptyValue = true;
                    }

                    TaskResponseValue::updateOrCreate(
                        [
                            'task_response_id' => $taskResponse->id,
                            'task_component_id' => $componentId,
                        ],
                        [
                            'value' => $valStr,
                        ]
                    );
                }

                // Check DB if task has valid non-empty values
                $dbHasValues = TaskResponseValue::where('task_response_id', $taskResponse->id)
                    ->whereNotNull('value')
                    ->whereRaw("TRIM(value) != '' AND value != '[]' AND value != 'null'")
                    ->exists();

                // Update response status based on whether it has actual values
                if ($hasNonEmptyValue || $dbHasValues) {
                    $taskResponse->update([
                        'status' => 'submitted',
                        'submitted_at' => $taskResponse->submitted_at ?? Carbon::now(),
                        'completed_at' => $taskResponse->completed_at ?? Carbon::now(),
                    ]);
                } else {
                    $taskResponse->update([
                        'status' => 'draft',
                        'submitted_at' => null,
                        'completed_at' => null,
                    ]);
                }
            }

            // Handle file attachments if provided in request
            if (!empty($attachmentsData)) {
                foreach ($attachmentsData as $taskDefId => $files) {
                    $taskResponse = TaskResponse::where('site_visit_id', $siteVisit->id)
                        ->where('task_definition_id', $taskDefId)
                        ->first();

                    if ($taskResponse) {
                        $filesList = is_array($files) ? $files : [$files];
                        foreach ($filesList as $file) {
                            if ($file && $file->isValid()) {
                                $path = $file->store('task_attachments', 'public');
                                TaskAttachment::create([
                                    'task_response_id' => $taskResponse->id,
                                    'file_name' => $file->getClientOriginalName(),
                                    'file_path' => Storage::url($path),
                                    'mime_type' => $file->getClientMimeType(),
                                    'file_size' => $file->getSize(),
                                    'uploaded_at' => Carbon::now(),
                                ]);
                            }
                        }
                    }
                }
            }

            // If user selected complete visit
            if (isset($responsesData['_complete_visit']) && $responsesData['_complete_visit']) {
                $siteVisit->update([
                    'status' => 'completed',
                    'visit_finished_at' => Carbon::now(),
                ]);
            }

            // Update completion stats on daily record (counting ONLY filled standard daily tasks, excluding on-demand)
            $dailyRecord = $siteVisit->dailyRecord;
            if ($dailyRecord) {
                $completedCount = TaskResponse::whereHas('siteVisit', fn ($q) => $q->where('daily_record_id', $dailyRecord->id))
                    ->whereHas('taskDefinition', fn ($q) => $q->where('task_type', 'daily'))
                    ->where('status', 'submitted')
                    ->whereHas('values', function ($vq) {
                        $vq->whereNotNull('value')->whereRaw("TRIM(value) != '' AND value != '[]' AND value != 'null'");
                    })
                    ->count();

                $totalRequired = max(1, $dailyRecord->required_daily_tasks);
                $percentage = min(100, round(($completedCount / $totalRequired) * 100, 2));

                $dailyRecord->update([
                    'completed_daily_tasks' => $completedCount,
                    'completion_percentage' => $percentage,
                ]);
            }

            return $this->findWithDetails($siteVisit->id);
        });
    }

    public function removeOnDemandTask(SiteVisit $siteVisit, int $responseId): bool
    {
        return (bool) DB::transaction(function () use ($siteVisit, $responseId) {
            $taskResponse = TaskResponse::where('site_visit_id', $siteVisit->id)
                ->where('id', $responseId)
                ->whereHas('taskDefinition', fn ($q) => $q->where('task_type', 'on_demand'))
                ->first();

            if ($taskResponse) {
                $taskResponse->values()->delete();
                $taskResponse->attachments()->delete();
                return $taskResponse->delete();
            }

            return false;
        });
    }
}
