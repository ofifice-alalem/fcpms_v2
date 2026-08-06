<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultant;
use App\Models\DailyRecord;
use App\Models\SiteVisit;
use App\Models\TaskResponse;
use App\Models\ConsultantLeave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $today = Carbon::today();

        // 1. All Active Consultants
        $allConsultants = Consultant::with(['user', 'workScheduleTemplate'])->get();
        $totalConsultantsCount = $allConsultants->count();

        // 2. Checked In Consultants Today (Daily Records with check_in_time)
        $todayDailyRecords = DailyRecord::with([
            'consultant.user', 
            'siteVisits.site', 
            'siteVisits.taskResponses.taskDefinition',
            'siteVisits.taskResponses.values'
        ])
        ->whereDate('work_date', $today)
        ->get();

        $checkedInConsultantIds = $todayDailyRecords->whereNotNull('check_in_time')->pluck('consultant_id')->toArray();
        $checkedInCount = count($checkedInConsultantIds);

        // 3. Absent Consultants & Leaves Today
        $leavesToday = ConsultantLeave::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->pluck('consultant_id')
            ->toArray();

        $absentCount = max(0, $totalConsultantsCount - $checkedInCount);

        // 4. Visited Sites Today
        $todaySiteVisits = SiteVisit::with(['site', 'dailyRecord.consultant', 'taskResponses.taskDefinition'])
            ->whereHas('dailyRecord', function ($q) use ($today) {
                $q->whereDate('work_date', $today);
            })
            ->get();

        $visitedSitesCount = $todaySiteVisits->pluck('site_id')->unique()->count();
        $totalVisitsCount = $todaySiteVisits->count();

        // 5. Total Tasks Breakdown Today (Daily vs On-Demand)
        $todayTaskResponses = TaskResponse::with(['taskDefinition'])
            ->whereHas('siteVisit.dailyRecord', function ($q) use ($today) {
                $q->whereDate('work_date', $today);
            })
            ->get();

        $dailyTasksCount = $todayTaskResponses->filter(function ($r) {
            $type = $r->taskDefinition->task_type ?? null;
            return $type === 'daily' || (is_object($type) && $type->value === 'daily');
        })->count();

        $onDemandTasksCount = $todayTaskResponses->filter(function ($r) {
            $type = $r->taskDefinition->task_type ?? null;
            return $type === 'on_demand' || (is_object($type) && $type->value === 'on_demand');
        })->count();

        $totalTasksExecutedCount = $todayTaskResponses->count();

        $completedTasksCount = $todayTaskResponses->filter(function ($r) {
            $hasValues = $r->values && $r->values->some(fn($v) => !empty($v->value));
            return $r->status === 'submitted' || ($r->completed_at && $hasValues);
        })->count();

        // Detailed Lists for Dashboard Views
        $consultantsStatusList = $allConsultants->map(function ($c) use ($checkedInConsultantIds, $todayDailyRecords, $leavesToday) {
            $isLeave = in_array($c->id, $leavesToday);
            
            // Find record for today, or fallback to latest record
            $record = $todayDailyRecords->firstWhere('consultant_id', $c->id);
            if (!$record) {
                $record = DailyRecord::where('consultant_id', $c->id)
                    ->with([
                        'siteVisits.site',
                        'siteVisits.taskResponses.taskDefinition',
                        'siteVisits.taskResponses.values'
                    ])
                    ->latest('id')
                    ->first();
            }

            $isCheckedIn = in_array($c->id, $checkedInConsultantIds) || ($record && !is_null($record->check_in_time));

            $siteVisitsList = [];
            if ($record && $record->siteVisits) {
                $siteVisitsList = $record->siteVisits->map(function ($visit) {
                    return [
                        'id' => $visit->id,
                        'site_id' => $visit->site_id,
                        'status' => $visit->status ?? ($visit->visit_finished_at ? 'completed' : 'in_progress'),
                        'visit_started_at' => $visit->visit_started_at ? $visit->visit_started_at->toIso8601String() : null,
                        'visit_finished_at' => $visit->visit_finished_at ? $visit->visit_finished_at->toIso8601String() : null,
                        'notes' => $visit->notes,
                        'site' => $visit->site ? [
                            'id' => $visit->site->id,
                            'name' => $visit->site->name,
                            'code' => $visit->site->code,
                        ] : null,
                        'task_responses' => $visit->taskResponses ? $visit->taskResponses->map(function ($r) {
                            return [
                                'id' => $r->id,
                                'status' => $r->status,
                                'completed_at' => $r->completed_at ? $r->completed_at->toIso8601String() : null,
                                'task_definition' => $r->taskDefinition ? [
                                    'id' => $r->taskDefinition->id,
                                    'title' => $r->taskDefinition->title,
                                    'task_type' => $r->taskDefinition->task_type,
                                ] : null,
                                'values' => $r->values ? $r->values->map(function ($v) {
                                    return [
                                        'id' => $v->id,
                                        'task_component_id' => $v->task_component_id,
                                        'value' => $v->value,
                                    ];
                                })->values()->all() : [],
                            ];
                        })->values()->all() : [],
                    ];
                })->values()->all();
            }

            return [
                'id' => $c->id,
                'full_name' => $c->full_name,
                'employee_number' => $c->employee_number,
                'specialization' => $c->specialization,
                'phone' => $c->phone,
                'status' => $isCheckedIn ? 'checked_in' : ($isLeave ? 'leave' : 'absent'),
                'check_in_time' => $record && $record->check_in_time ? Carbon::parse($record->check_in_time)->format('H:i') : null,
                'completed_daily_tasks' => $record ? $record->completed_daily_tasks : 0,
                'required_daily_tasks' => $record ? $record->required_daily_tasks : 0,
                'completion_percentage' => $record ? (float) $record->completion_percentage : 0,
                'site_visits' => $siteVisitsList,
            ];
        });

        $recentSiteVisitsList = $todaySiteVisits->map(function ($visit) {
            return [
                'id' => $visit->id,
                'site_name' => $visit->site ? $visit->site->name : 'موقع ميداني',
                'site_code' => $visit->site ? $visit->site->code : '',
                'consultant_name' => $visit->dailyRecord && $visit->dailyRecord->consultant ? $visit->dailyRecord->consultant->full_name : 'استشاري',
                'status' => $visit->status ?? ($visit->visit_finished_at ? 'completed' : 'in_progress'),
                'started_at' => $visit->visit_started_at ? Carbon::parse($visit->visit_started_at)->format('H:i') : '--',
                'task_count' => $visit->taskResponses ? $visit->taskResponses->count() : 0,
            ];
        })->values()->all();

        return Inertia::render('Admin/Dashboard/Index', [
            'stats' => [
                'total_consultants' => $totalConsultantsCount,
                'checked_in_consultants' => $checkedInCount,
                'absent_consultants' => $absentCount,
                'visited_sites' => $visitedSitesCount,
                'total_site_visits' => $totalVisitsCount,
                'daily_tasks_count' => $dailyTasksCount,
                'on_demand_tasks_count' => $onDemandTasksCount,
                'total_tasks_count' => $totalTasksExecutedCount,
                'completed_tasks_count' => $completedTasksCount,
            ],
            'consultants_status' => $consultantsStatusList,
            'recent_visits' => $recentSiteVisitsList,
            'today_date_formatted' => Carbon::now()->locale('ar')->translatedFormat('l, j F Y'),
        ]);
    }
}
