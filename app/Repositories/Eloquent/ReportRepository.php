<?php

namespace App\Repositories\Eloquent;

use App\Models\Consultant;
use App\Models\DailyRecord;
use App\Models\Site;
use App\Models\SiteVisit;
use App\Models\TaskResponse;
use App\Repositories\Contracts\ReportRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportRepository implements ReportRepositoryInterface
{
    /**
     * Get aggregated metrics and logs for a specific consultant.
     */
    public function getConsultantMetrics(int $consultantId, array $filters = []): array
    {
        $query = DailyRecord::where('consultant_id', $consultantId)
            ->with(['siteVisits.site', 'siteVisits.taskResponses.taskDefinition']);

        if (!empty($filters['date_from'])) {
            $query->whereDate('work_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('work_date', '<=', $filters['date_to']);
        }

        $records = $query->orderBy('work_date', 'desc')->get();

        $allVisits = $records->pluck('siteVisits')->flatten();

        if (!empty($filters['city'])) {
            $allVisits = $allVisits->filter(function ($visit) use ($filters) {
                return $visit->site && str_contains(mb_strtolower($visit->site->city), mb_strtolower($filters['city']));
            });
        }

        $totalVisits = $allVisits->count();
        $completedVisits = $allVisits->where('status', 'completed')->count();
        $inProgressVisits = $allVisits->where('status', 'in_progress')->count();

        $completionRate = $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100, 1) : 0;

        // Count on-demand tasks
        $onDemandTasksCount = 0;
        foreach ($allVisits as $visit) {
            foreach ($visit->taskResponses as $resp) {
                $typeStr = is_object($resp->task_type) ? $resp->task_type->value : (string) $resp->task_type;
                if ($typeStr === 'on_demand') {
                    $onDemandTasksCount++;
                }
            }
        }

        // City distribution
        $citiesDistribution = [];
        foreach ($allVisits as $visit) {
            if ($visit->site && $visit->site->city) {
                $city = $visit->site->city;
                $citiesDistribution[$city] = ($citiesDistribution[$city] ?? 0) + 1;
            }
        }

        return [
            'total_days_worked'    => $records->count(),
            'total_site_visits'    => $totalVisits,
            'completed_visits'     => $completedVisits,
            'in_progress_visits'   => $inProgressVisits,
            'completion_rate'      => $completionRate,
            'on_demand_tasks'      => $onDemandTasksCount,
            'cities_distribution'  => $citiesDistribution,
            'recent_visits_log'    => $allVisits->values()->toArray(),
            'daily_records'        => $records,
        ];
    }

    /**
     * Get enterprise-wide aggregated metrics and analytics for admin/HR.
     */
    public function getEnterpriseMetrics(array $filters = []): array
    {
        $visitQuery = SiteVisit::with(['dailyRecord.consultant', 'site', 'taskResponses.taskDefinition']);

        if (!empty($filters['date_from'])) {
            $visitQuery->whereHas('dailyRecord', function ($q) use ($filters) {
                $q->whereDate('work_date', '>=', $filters['date_from']);
            });
        }
        if (!empty($filters['date_to'])) {
            $visitQuery->whereHas('dailyRecord', function ($q) use ($filters) {
                $q->whereDate('work_date', '<=', $filters['date_to']);
            });
        }
        if (!empty($filters['consultant_id'])) {
            $visitQuery->whereHas('dailyRecord', function ($q) use ($filters) {
                $q->where('consultant_id', $filters['consultant_id']);
            });
        }
        if (!empty($filters['site_id'])) {
            $visitQuery->where('site_id', $filters['site_id']);
        }
        if (!empty($filters['city'])) {
            $visitQuery->whereHas('site', function ($q) use ($filters) {
                $q->where('city', $filters['city']);
            });
        }

        $allVisits = $visitQuery->orderBy('created_at', 'desc')->get();

        $totalVisits = $allVisits->count();
        $completedVisits = $allVisits->where('status', 'completed')->count();
        $inProgressVisits = $allVisits->where('status', 'in_progress')->count();

        $systemCompletionRate = $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100, 1) : 0;

        $activeConsultantsCount = Consultant::where('employment_status', 'active')->count();

        // On-demand tasks
        $onDemandCount = 0;
        foreach ($allVisits as $visit) {
            foreach ($visit->taskResponses as $resp) {
                $typeStr = is_object($resp->task_type) ? $resp->task_type->value : (string) $resp->task_type;
                if ($typeStr === 'on_demand') {
                    $onDemandCount++;
                }
            }
        }

        // Rankings by visits count
        $consultantStats = [];
        foreach ($allVisits as $visit) {
            $consultant = $visit->dailyRecord->consultant ?? null;
            if ($consultant) {
                $id = $consultant->id;
                if (!isset($consultantStats[$id])) {
                    $consultantStats[$id] = [
                        'id'              => $id,
                        'name'            => $consultant->full_name,
                        'employee_number' => $consultant->employee_number,
                        'total_visits'    => 0,
                        'completed'       => 0,
                    ];
                }
                $consultantStats[$id]['total_visits']++;
                if ($visit->status === 'completed') {
                    $consultantStats[$id]['completed']++;
                }
            }
        }

        $rankings = array_values($consultantStats);
        usort($rankings, fn($a, $b) => $b['total_visits'] <=> $a['total_visits']);

        return [
            'system_total_visits'      => $totalVisits,
            'completed_visits'         => $completedVisits,
            'in_progress_visits'       => $inProgressVisits,
            'active_consultants_count' => $activeConsultantsCount,
            'system_completion_rate'   => $systemCompletionRate,
            'on_demand_tasks_count'    => $onDemandCount,
            'consultant_rankings'      => $rankings,
            'visits_log'               => $allVisits,
        ];
    }

    /**
     * Get detailed visit information including task responses and attachments.
     */
    public function getVisitDetail(int $visitId): ?SiteVisit
    {
        return SiteVisit::with([
            'site',
            'dailyRecord.consultant',
            'taskResponses.taskDefinition.components',
            'taskResponses.responseValues',
            'taskResponses.attachments',
        ])->find($visitId);
    }

    /**
     * Get raw dataset for exporting reports.
     */
    public function getExportData(array $filters = [], ?int $consultantId = null): array
    {
        if ($consultantId) {
            $filters['consultant_id'] = $consultantId;
        }

        $query = SiteVisit::with(['dailyRecord.consultant', 'site', 'taskResponses.taskDefinition']);

        if (!empty($filters['date_from'])) {
            $query->whereHas('dailyRecord', function ($q) use ($filters) {
                $q->whereDate('work_date', '>=', $filters['date_from']);
            });
        }
        if (!empty($filters['date_to'])) {
            $query->whereHas('dailyRecord', function ($q) use ($filters) {
                $q->whereDate('work_date', '<=', $filters['date_to']);
            });
        }
        if (!empty($filters['consultant_id'])) {
            $query->whereHas('dailyRecord', function ($q) use ($filters) {
                $q->where('consultant_id', $filters['consultant_id']);
            });
        }
        if (!empty($filters['site_id'])) {
            $query->where('site_id', $filters['site_id']);
        }
        if (!empty($filters['city'])) {
            $query->whereHas('site', function ($q) use ($filters) {
                $q->where('city', $filters['city']);
            });
        }

        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    /**
     * Get sites performance aggregated report (visits, unique consultants, regular & on-demand tasks).
     */
    public function getSitesPerformanceReport(array $filters = []): array
    {
        $sitesQuery = Site::query();

        if (!empty($filters['city'])) {
            $sitesQuery->where('city', $filters['city']);
        }
        if (!empty($filters['site_id'])) {
            $sitesQuery->where('id', $filters['site_id']);
        }

        $sites = $sitesQuery->get();
        $results = [];

        foreach ($sites as $site) {
            $visitQuery = SiteVisit::where('site_id', $site->id)
                ->with(['dailyRecord.consultant', 'taskResponses.taskDefinition']);

            if (!empty($filters['date_from'])) {
                $visitQuery->whereHas('dailyRecord', function ($q) use ($filters) {
                    $q->whereDate('work_date', '>=', $filters['date_from']);
                });
            }
            if (!empty($filters['date_to'])) {
                $visitQuery->whereHas('dailyRecord', function ($q) use ($filters) {
                    $q->whereDate('work_date', '<=', $filters['date_to']);
                });
            }

            $visits = $visitQuery->get();

            $consultantsSet = [];
            $regularTasksCount = 0;
            $onDemandTasksCount = 0;

            foreach ($visits as $v) {
                if ($v->dailyRecord && $v->dailyRecord->consultant_id) {
                    $consultantsSet[$v->dailyRecord->consultant_id] = true;
                }

                foreach ($v->taskResponses as $resp) {
                    $taskDef = $resp->taskDefinition;
                    $taskTypeVal = $taskDef?->task_type;
                    $typeStr = is_object($taskTypeVal) ? $taskTypeVal->value : (string) $taskTypeVal;

                    if ($typeStr === 'on_demand') {
                        $onDemandTasksCount++;
                    } else {
                        $regularTasksCount++;
                    }
                }
            }

            $results[] = [
                'site_id'               => $site->id,
                'site_name'             => $site->name,
                'site_code'             => $site->code,
                'site_city'             => $site->city,
                'total_visits_count'    => $visits->count(),
                'consultants_count'     => count($consultantsSet),
                'regular_tasks_count'   => $regularTasksCount,
                'on_demand_tasks_count' => $onDemandTasksCount,
            ];
        }

        return $results;
    }

    /**
     * Get task breakdown report for a specific site (frequency of tasks & distinct consultants count).
     */
    public function getSpecificSiteTaskBreakdown(int $siteId, array $filters = []): array
    {
        $site = Site::find($siteId);
        if (!$site) {
            return [
                'site' => null,
                'tasks_breakdown' => [],
            ];
        }

        $visitQuery = SiteVisit::where('site_id', $siteId)
            ->with(['dailyRecord.consultant', 'taskResponses.taskDefinition']);

        if (!empty($filters['date_from'])) {
            $visitQuery->whereHas('dailyRecord', function ($q) use ($filters) {
                $q->whereDate('work_date', '>=', $filters['date_from']);
            });
        }
        if (!empty($filters['date_to'])) {
            $visitQuery->whereHas('dailyRecord', function ($q) use ($filters) {
                $q->whereDate('work_date', '<=', $filters['date_to']);
            });
        }

        $visits = $visitQuery->get();
        $tasksMap = [];

        foreach ($visits as $visit) {
            $consultant = $visit->dailyRecord->consultant ?? null;

            foreach ($visit->taskResponses as $resp) {
                $taskDefId = $resp->task_definition_id ?? ('custom_' . $resp->id);
                $taskDef = $resp->taskDefinition;
                $title = $taskDef->title ?? $resp->notes ?? 'مهمة ميدانية';
                $taskTypeVal = $taskDef?->task_type;
                $typeStr = is_object($taskTypeVal) ? $taskTypeVal->value : (string) $taskTypeVal;

                if (!isset($tasksMap[$taskDefId])) {
                    $tasksMap[$taskDefId] = [
                        'task_id'         => $taskDefId,
                        'title'           => $title,
                        'task_type'       => $typeStr === 'on_demand' ? 'إضافية (عند الحاجة)' : 'يومية قياسية',
                        'execution_count' => 0,
                        'consultants'     => [],
                    ];
                }

                $tasksMap[$taskDefId]['execution_count']++;

                if ($consultant) {
                    $tasksMap[$taskDefId]['consultants'][$consultant->id] = $consultant->full_name;
                }
            }
        }

        $breakdown = [];
        foreach ($tasksMap as $item) {
            $breakdown[] = [
                'task_id'           => $item['task_id'],
                'title'             => $item['title'],
                'task_type'         => $item['task_type'],
                'execution_count'   => $item['execution_count'],
                'consultants_count' => count($item['consultants']),
                'consultants_list'  => array_values($item['consultants']),
            ];
        }

        usort($breakdown, fn($a, $b) => $b['execution_count'] <=> $a['execution_count']);

        return [
            'site'            => [
                'id'   => $site->id,
                'name' => $site->name,
                'code' => $site->code,
                'city' => $site->city,
            ],
            'tasks_breakdown' => $breakdown,
        ];
    }
}
