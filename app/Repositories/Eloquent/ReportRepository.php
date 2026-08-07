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

        $activeConsultantsCount = Consultant::where('employment_status', 'active')->count();

        // On-demand tasks
        $onDemandCount = 0;
        foreach ($allVisits as $visit) {
            foreach ($visit->taskResponses as $resp) {
                $taskDef = $resp->taskDefinition;
                $typeStr = $taskDef ? (is_object($taskDef->task_type) ? $taskDef->task_type->value : (string) $taskDef->task_type) : null;
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

        $activeConsultantsCount = Consultant::where('employment_status', 'active')->count();

        $completedDailyTasks = 0;
        $totalRequiredDailyTasks = 0;
        foreach ($allVisits as $visit) {
            $completedDailyTasks += $visit->daily_tasks_count;
            $reqTasks = $visit->total_daily_tasks_count > 0 
                ? $visit->total_daily_tasks_count 
                : ($visit->dailyRecord && (int)$visit->dailyRecord->required_daily_tasks > 0 
                    ? (int)$visit->dailyRecord->required_daily_tasks 
                    : max(1, $visit->daily_tasks_count));
            $totalRequiredDailyTasks += $reqTasks;
        }

        $systemCompletionRate = $totalRequiredDailyTasks > 0 ? round(($completedDailyTasks / $totalRequiredDailyTasks) * 100, 1) : 0;

        return [
            'system_total_visits'          => $totalVisits,
            'completed_visits'             => $completedVisits,
            'in_progress_visits'           => $inProgressVisits,
            'active_consultants_count'     => $activeConsultantsCount,
            'present_consultants_count'    => count($consultantStats),
            'system_completion_rate'       => $systemCompletionRate,
            'completed_daily_tasks_count'  => $completedDailyTasks,
            'total_required_daily_tasks'   => $totalRequiredDailyTasks,
            'on_demand_tasks_count'        => $onDemandCount,
            'consultant_rankings'          => $rankings,
            'visits_log'                   => $allVisits,
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
            'taskResponses.values',
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
                    if ($resp->status !== 'submitted' && is_null($resp->completed_at)) {
                        continue;
                    }
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
                // Count ONLY tasks that were actually performed & submitted/completed by consultants
                if ($resp->status !== 'submitted' && is_null($resp->completed_at)) {
                    continue;
                }
                $taskDefId = $resp->task_definition_id ?? ('custom_' . $resp->id);
                $taskDef = $resp->taskDefinition;
                $title = $taskDef->title ?? $resp->notes ?? 'مهمة ميدانية';
                $taskTypeVal = $taskDef?->task_type;
                $typeStr = is_object($taskTypeVal) ? $taskTypeVal->value : (string) $taskTypeVal;

                if (!isset($tasksMap[$taskDefId])) {
                    $tasksMap[$taskDefId] = [
                        'task_id'         => $taskDefId,
                        'title'           => $title,
                        'task_type'       => $typeStr === 'on_demand' ? 'إضافية' : 'يومية',
                        'execution_count' => 0,
                        'consultants'     => [],
                    ];
                }

                $tasksMap[$taskDefId]['execution_count']++;

                if ($consultant) {
                    if (!isset($tasksMap[$taskDefId]['consultants'][$consultant->id])) {
                        $tasksMap[$taskDefId]['consultants'][$consultant->id] = [
                            'id' => $consultant->id,
                            'name' => $consultant->full_name,
                            'count' => 0,
                        ];
                    }
                    $tasksMap[$taskDefId]['consultants'][$consultant->id]['count']++;
                }
            }
        }

        $breakdown = [];
        foreach ($tasksMap as $item) {
            $consultantsList = array_values($item['consultants']);
            usort($consultantsList, fn($a, $b) => $b['count'] <=> $a['count']);

            $breakdown[] = [
                'task_id'           => $item['task_id'],
                'title'             => $item['title'],
                'task_type'         => $item['task_type'],
                'execution_count'   => $item['execution_count'],
                'consultants_count' => count($item['consultants']),
                'consultants_list'  => $consultantsList,
            ];
        }

        usort($breakdown, fn($a, $b) => $b['execution_count'] <=> $a['execution_count']);

        $dailyTasksCount = 0;
        $dailyTasksExecutions = 0;
        $onDemandTasksCount = 0;
        $onDemandTasksExecutions = 0;

        foreach ($breakdown as $item) {
            if ($item['task_type'] === 'إضافية') {
                $onDemandTasksCount++;
                $onDemandTasksExecutions += $item['execution_count'];
            } else {
                $dailyTasksCount++;
                $dailyTasksExecutions += $item['execution_count'];
            }
        }

        $uniqueConsultantsCount = $visits
            ->pluck('dailyRecord.consultant_id')
            ->filter()
            ->unique()
            ->count();

        return [
            'site'            => [
                'id'   => $site->id,
                'name' => $site->name,
                'code' => $site->code,
                'city' => $site->city,
            ],
            'summary'         => [
                'total_visits'              => $visits->count(),
                'unique_consultants'        => $uniqueConsultantsCount,
                'daily_tasks_count'         => $dailyTasksCount,
                'daily_tasks_executions'    => $dailyTasksExecutions,
                'on_demand_tasks_count'     => $onDemandTasksCount,
                'on_demand_tasks_executions'=> $onDemandTasksExecutions,
            ],
            'tasks_breakdown' => $breakdown,
        ];
    }

    /**
     * Get consultants performance aggregated report.
     */
    public function getConsultantsPerformanceReport(array $filters = []): array
    {
        $consultantsQuery = Consultant::query();

        if (!empty($filters['consultant_id'])) {
            $consultantsQuery->where('id', $filters['consultant_id']);
        }

        $consultants = $consultantsQuery->get();
        $results = [];

        foreach ($consultants as $c) {
            $visitQuery = SiteVisit::whereHas('dailyRecord', function ($q) use ($c) {
                $q->where('consultant_id', $c->id);
            })->with(['site', 'taskResponses.taskDefinition']);

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

            $sitesSet = [];
            $dailyTasksExecutions = 0;
            $onDemandTasksExecutions = 0;

            foreach ($visits as $v) {
                if ($v->site_id) {
                    $sitesSet[$v->site_id] = true;
                }

                foreach ($v->taskResponses as $resp) {
                    if ($resp->status !== 'submitted' && is_null($resp->completed_at)) {
                        continue;
                    }
                    $taskDef = $resp->taskDefinition;
                    $taskTypeVal = $taskDef?->task_type;
                    $typeStr = is_object($taskTypeVal) ? $taskTypeVal->value : (string) $taskTypeVal;

                    if ($typeStr === 'on_demand') {
                        $onDemandTasksExecutions++;
                    } else {
                        $dailyTasksExecutions++;
                    }
                }
            }

            $results[] = [
                'consultant_id'              => $c->id,
                'consultant_name'            => $c->full_name,
                'employee_number'            => $c->employee_number,
                'specialization'             => $c->specialization ?? 'استشاري ميداني',
                'phone'                      => $c->phone,
                'visited_sites_count'        => count($sitesSet),
                'total_visits_count'         => $visits->count(),
                'daily_tasks_executions'     => $dailyTasksExecutions,
                'on_demand_tasks_executions' => $onDemandTasksExecutions,
            ];
        }

        return $results;
    }

    /**
     * Get site breakdown for a specific consultant.
     */
    public function getConsultantSitesBreakdown(int $consultantId, array $filters = []): array
    {
        $consultant = Consultant::find($consultantId);
        if (!$consultant) {
            return [
                'consultant' => null,
                'summary'    => null,
                'sites'      => [],
            ];
        }

        $visitQuery = SiteVisit::whereHas('dailyRecord', function ($q) use ($consultantId) {
            $q->where('consultant_id', $consultantId);
        })->with(['site', 'taskResponses.taskDefinition']);

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

        $sitesMap = [];
        $totalDailyTasksExecutions = 0;
        $totalOnDemandTasksExecutions = 0;

        foreach ($visits as $v) {
            $site = $v->site;
            if (!$site) continue;

            $workDate = $v->dailyRecord->work_date ?? null;
            if ($workDate) {
                $workDate = \Carbon\Carbon::parse($workDate)->format('Y-m-d');
            }

            $siteId = $site->id;
            if (!isset($sitesMap[$siteId])) {
                $sitesMap[$siteId] = [
                    'site_id'                    => $siteId,
                    'site_name'                  => $site->name,
                    'site_code'                  => $site->code,
                    'site_city'                  => $site->city,
                    'visits_count'               => 0,
                    'daily_tasks_executions'     => 0,
                    'on_demand_tasks_executions' => 0,
                    'total_tasks_count'          => 0,
                    'tasks_map'                  => [],
                ];
            }

            $sitesMap[$siteId]['visits_count']++;

            foreach ($v->taskResponses as $resp) {
                if ($resp->status !== 'submitted' && is_null($resp->completed_at)) {
                    continue;
                }

                $taskDefId = $resp->task_definition_id ?? ('custom_' . $resp->id);
                $taskDef = $resp->taskDefinition;
                $title = $taskDef->title ?? $resp->notes ?? 'مهمة ميدانية';
                $taskTypeVal = $taskDef?->task_type;
                $typeStr = is_object($taskTypeVal) ? $taskTypeVal->value : (string) $taskTypeVal;
                $isOndemand = ($typeStr === 'on_demand');

                if ($isOndemand) {
                    $sitesMap[$siteId]['on_demand_tasks_executions']++;
                    $totalOnDemandTasksExecutions++;
                } else {
                    $sitesMap[$siteId]['daily_tasks_executions']++;
                    $totalDailyTasksExecutions++;
                }
                $sitesMap[$siteId]['total_tasks_count']++;

                if (!isset($sitesMap[$siteId]['tasks_map'][$taskDefId])) {
                    $sitesMap[$siteId]['tasks_map'][$taskDefId] = [
                        'task_id'          => (string) $taskDefId,
                        'title'            => $title,
                        'task_type'        => $isOndemand ? 'إضافية' : 'يومية',
                        'execution_count'  => 0,
                        'last_executed_at' => $workDate,
                    ];
                }

                $sitesMap[$siteId]['tasks_map'][$taskDefId]['execution_count']++;
                if ($workDate && (!$sitesMap[$siteId]['tasks_map'][$taskDefId]['last_executed_at'] || $workDate > $sitesMap[$siteId]['tasks_map'][$taskDefId]['last_executed_at'])) {
                    $sitesMap[$siteId]['tasks_map'][$taskDefId]['last_executed_at'] = $workDate;
                }
            }
        }

        $sitesList = [];
        foreach ($sitesMap as $siteItem) {
            $siteItem['tasks_breakdown'] = array_values($siteItem['tasks_map']);
            unset($siteItem['tasks_map']);
            $sitesList[] = $siteItem;
        }

        return [
            'consultant' => [
                'id'              => $consultant->id,
                'full_name'       => $consultant->full_name,
                'employee_number' => $consultant->employee_number,
                'specialization'  => $consultant->specialization ?? 'استشاري ميداني',
                'phone'           => $consultant->phone,
            ],
            'summary'    => [
                'total_sites'                => count($sitesMap),
                'total_visits'               => $visits->count(),
                'daily_tasks_executions'     => $totalDailyTasksExecutions,
                'on_demand_tasks_executions' => $totalOnDemandTasksExecutions,
            ],
            'sites'      => $sitesList,
        ];
    }

    /**
     * Get task execution breakdown for a specific consultant across sites.
     */
    public function getConsultantTasksBreakdown(int $consultantId, array $filters = []): array
    {
        $consultant = Consultant::find($consultantId);
        if (!$consultant) {
            return [
                'consultant'      => null,
                'executed_tasks'  => [],
                'tasks_breakdown' => [],
            ];
        }

        $visitQuery = SiteVisit::whereHas('dailyRecord', function ($q) use ($consultantId) {
            $q->where('consultant_id', $consultantId);
        })->with(['site', 'dailyRecord', 'taskResponses.taskDefinition']);

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
        $executedTasksDropdown = [];

        foreach ($visits as $visit) {
            $site = $visit->site;
            if (!$site) continue;

            $workDate = $visit->dailyRecord->work_date ?? null;
            if ($workDate) {
                $workDate = \Carbon\Carbon::parse($workDate)->format('Y-m-d');
            }

            foreach ($visit->taskResponses as $resp) {
                if ($resp->status !== 'submitted' && is_null($resp->completed_at)) {
                    continue;
                }

                $taskDefId = $resp->task_definition_id ?? ('custom_' . $resp->id);
                $taskDef = $resp->taskDefinition;
                $title = $taskDef->title ?? $resp->notes ?? 'مهمة ميدانية';
                $taskTypeVal = $taskDef?->task_type;
                $typeStr = is_object($taskTypeVal) ? $taskTypeVal->value : (string) $taskTypeVal;
                $isOndemand = ($typeStr === 'on_demand');

                if (!isset($executedTasksDropdown[$taskDefId])) {
                    $executedTasksDropdown[$taskDefId] = [
                        'id'        => (string) $taskDefId,
                        'title'     => $title,
                        'task_type' => $isOndemand ? 'إضافية' : 'يومية',
                    ];
                }

                if (!isset($tasksMap[$taskDefId])) {
                    $tasksMap[$taskDefId] = [
                        'task_id'          => (string) $taskDefId,
                        'title'            => $title,
                        'task_type'        => $isOndemand ? 'إضافية' : 'يومية',
                        'total_executions' => 0,
                        'sites'            => [],
                    ];
                }

                $tasksMap[$taskDefId]['total_executions']++;

                $siteId = $site->id;
                if (!isset($tasksMap[$taskDefId]['sites'][$siteId])) {
                    $tasksMap[$taskDefId]['sites'][$siteId] = [
                        'site_id'         => $siteId,
                        'site_name'       => $site->name,
                        'site_code'       => $site->code,
                        'site_city'       => $site->city,
                        'execution_count' => 0,
                        'last_executed'   => $workDate,
                    ];
                }

                $tasksMap[$taskDefId]['sites'][$siteId]['execution_count']++;
                if ($workDate && (!$tasksMap[$taskDefId]['sites'][$siteId]['last_executed'] || $workDate > $tasksMap[$taskDefId]['sites'][$siteId]['last_executed'])) {
                    $tasksMap[$taskDefId]['sites'][$siteId]['last_executed'] = $workDate;
                }
            }
        }

        $breakdownList = [];
        foreach ($tasksMap as $taskItem) {
            $taskItem['sites'] = array_values($taskItem['sites']);
            $breakdownList[] = $taskItem;
        }

        return [
            'consultant'      => [
                'id'              => $consultant->id,
                'full_name'       => $consultant->full_name,
                'employee_number' => $consultant->employee_number,
                'specialization'  => $consultant->specialization ?? 'استشاري ميداني',
                'phone'           => $consultant->phone,
            ],
            'executed_tasks'  => array_values($executedTasksDropdown),
            'tasks_breakdown' => $breakdownList,
        ];
    }
}
