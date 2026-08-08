<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultant;
use App\Models\DailyRecord;
use App\Models\SiteVisit;
use App\Models\TaskResponse;
use App\Models\ConsultantLeave;
use App\Models\OfficialHoliday;
use App\Models\WorkScheduleTemplate;
use App\Rules\BusinessRuleEvaluator;
use App\Enums\AttendanceStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $today = Carbon::today();

        // 1. All Active Consultants with their Work Schedule Templates
        $allConsultants = Consultant::with(['user', 'workScheduleTemplate.days'])->get();
        $totalConsultantsCount = $allConsultants->count();

        // Default Work Schedule Template (Fallback if consultant doesn't have a template)
        $defaultTemplate = WorkScheduleTemplate::where('is_default', true)->with('days')->first();

        // Check if today is an official holiday (BR-014)
        $officialHolidayToday = OfficialHoliday::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();
        $isOfficialHoliday = !is_null($officialHolidayToday);

        // Day of week (0=Sunday ... 6=Saturday)
        $todayDayOfWeek = $today->dayOfWeek;

        // 2. Checked In Consultants Today (Daily Records with check_in_time)
        $todayDailyRecords = DailyRecord::with([
            'consultant.user', 
            'siteVisits.site', 
            'siteVisits.taskResponses.taskDefinition',
            'siteVisits.taskResponses.values.component'
        ])
        ->whereDate('work_date', $today)
        ->get();

        $checkedInConsultantIds = $todayDailyRecords->whereNotNull('check_in_time')->pluck('consultant_id')->toArray();

        // 3. Leaves Today
        $leavesToday = ConsultantLeave::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->pluck('consultant_id')
            ->toArray();

        // 4. Visited Sites Today
        $todaySiteVisits = SiteVisit::with([
            'site', 
            'dailyRecord.consultant', 
            'taskResponses.taskDefinition',
            'taskResponses.values.component'
        ])
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

        // Detailed Lists for Dashboard Views using BusinessRuleEvaluator (BR-018)
        $consultantsStatusList = $allConsultants->map(function ($c) use (
            $todayDayOfWeek,
            $isOfficialHoliday,
            $officialHolidayToday,
            $defaultTemplate,
            $checkedInConsultantIds,
            $todayDailyRecords,
            $leavesToday
        ) {
            $isOnLeave = in_array($c->id, $leavesToday);
            
            // Find record for today, or fallback to latest record for site visits history
            $todayRecord = $todayDailyRecords->firstWhere('consultant_id', $c->id);
            $record = $todayRecord;
            if (!$record) {
                $record = DailyRecord::where('consultant_id', $c->id)
                    ->with([
                        'siteVisits.site',
                        'siteVisits.taskResponses.taskDefinition',
                        'siteVisits.taskResponses.values.component'
                    ])
                    ->latest('id')
                    ->first();
            }

            $isCheckedIn = in_array($c->id, $checkedInConsultantIds) || ($todayRecord && !is_null($todayRecord->check_in_time));

            // Check if today is a working day for this consultant based on their template
            $template = $c->workScheduleTemplate ?? $defaultTemplate;
            $dayConfig = $template?->days?->firstWhere('day_of_week', $todayDayOfWeek);
            if ($dayConfig) {
                $isWorkingDay = (bool) $dayConfig->is_working_day;
            } else {
                // Default: Sunday(0) to Thursday(4) are working days, Friday(5)/Saturday(6) are weekend
                $isWorkingDay = !in_array($todayDayOfWeek, [5, 6]);
            }

            // Strict BR-018 evaluation: Official Holiday -> Work Schedule -> Leave -> Activity -> Absence
            $evalStatus = BusinessRuleEvaluator::evaluateAttendanceStatus(
                $isOfficialHoliday,
                $isWorkingDay,
                $isOnLeave,
                $isCheckedIn
            );

            $statusString = match($evalStatus) {
                AttendanceStatus::HOLIDAY => 'holiday',
                AttendanceStatus::NON_WORKING_DAY => 'off_day',
                AttendanceStatus::LEAVE => 'leave',
                AttendanceStatus::PRESENT => 'checked_in',
                AttendanceStatus::ABSENT => 'absent',
            };

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
                            $typeRaw = $r->taskDefinition->task_type ?? null;
                            $taskTypeStr = is_object($typeRaw) ? ($typeRaw->value ?? (string)$typeRaw) : (string)$typeRaw;

                            return [
                                'id' => $r->id,
                                'status' => $r->status,
                                'completed_at' => $r->completed_at ? $r->completed_at->toIso8601String() : null,
                                'task_definition' => $r->taskDefinition ? [
                                    'id' => $r->taskDefinition->id,
                                    'title' => $r->taskDefinition->title,
                                    'task_type' => $taskTypeStr,
                                ] : null,
                                'values' => $r->values ? $r->values->map(function ($v) {
                                    return [
                                        'id' => $v->id,
                                        'task_component_id' => $v->task_component_id,
                                        'value' => $v->value,
                                        'component' => $v->component ? [
                                            'id' => $v->component->id,
                                            'label' => $v->component->label,
                                            'component_type' => $v->component->component_type,
                                        ] : null,
                                        'task_component' => $v->component ? [
                                            'id' => $v->component->id,
                                            'label' => $v->component->label,
                                            'component_type' => $v->component->component_type,
                                        ] : null,
                                    ];
                                })->values()->all() : [],
                            ];
                        })->values()->all() : [],
                    ];
                })->values()->all();
            }

            $onDemandTasksCount = 0;
            if ($record && $record->siteVisits) {
                foreach ($record->siteVisits as $v) {
                    if ($v->taskResponses) {
                        foreach ($v->taskResponses as $r) {
                            $t = $r->taskDefinition->task_type ?? null;
                            $tStr = is_object($t) ? ($t->value ?? (string)$t) : (string)$t;
                            if ($tStr === 'on_demand') {
                                $onDemandTasksCount++;
                            }
                        }
                    }
                }
            }

            return [
                'id' => $c->id,
                'full_name' => $c->full_name,
                'employee_number' => $c->employee_number,
                'specialization' => $c->specialization,
                'phone' => $c->phone,
                'status' => $statusString,
                'is_working_day' => $isWorkingDay,
                'holiday_name' => $isOfficialHoliday && $officialHolidayToday ? $officialHolidayToday->name : null,
                'check_in_time' => $todayRecord && $todayRecord->check_in_time ? Carbon::parse($todayRecord->check_in_time)->format('H:i') : null,
                'completed_daily_tasks' => $record ? $record->completed_daily_tasks : 0,
                'required_daily_tasks' => $record ? $record->required_daily_tasks : 0,
                'completion_percentage' => $record ? (float) $record->completion_percentage : 0,
                'visited_sites_count' => count($siteVisitsList),
                'on_demand_tasks_count' => $onDemandTasksCount,
                'site_visits' => $siteVisitsList,
            ];
        });

        // Group visited sites by site_id for Section 2
        $recentSiteVisitsList = $todaySiteVisits->groupBy('site_id')->map(function ($visits, $siteId) {
            $firstVisit = $visits->first();
            $site = $firstVisit ? $firstVisit->site : null;

            $consultants = $visits->map(function ($visit) use ($site) {
                $record = $visit->dailyRecord;
                $c = $record ? $record->consultant : null;

                $taskResponses = $visit->taskResponses;

                $dailyResponses = $taskResponses ? $taskResponses->filter(function ($r) {
                    $type = $r->taskDefinition->task_type ?? null;
                    return $type === 'daily' || (is_object($type) && $type->value === 'daily');
                }) : collect();

                $totalDailyTasks = $dailyResponses->count();

                $completedDailyTasks = $dailyResponses->filter(function ($r) {
                    $hasValues = $r->values && $r->values->some(fn($v) => !empty($v->value));
                    return $r->status === 'submitted' || ($r->completed_at && $hasValues);
                })->count();

                $pct = $totalDailyTasks > 0 ? (int) round(($completedDailyTasks / $totalDailyTasks) * 100) : 0;

                $onDemandTasks = $taskResponses ? $taskResponses->filter(function ($r) {
                    $type = $r->taskDefinition->task_type ?? null;
                    return $type === 'on_demand' || (is_object($type) && $type->value === 'on_demand');
                })->count() : 0;

                return [
                    'visit_id' => $visit->id,
                    'consultant_id' => $c ? $c->id : null,
                    'full_name' => $c ? $c->full_name : 'استشاري',
                    'employee_number' => $c ? $c->employee_number : '',
                    'specialization' => $c ? $c->specialization : 'استشاري ميداني',
                    'started_at' => $visit->visit_started_at ? Carbon::parse($visit->visit_started_at)->format('H:i') : '--',
                    'completed_tasks' => $completedDailyTasks,
                    'total_tasks' => $totalDailyTasks,
                    'on_demand_tasks' => $onDemandTasks,
                    'completion_percentage' => $pct,
                    'status' => $visit->status ?? ($visit->visit_finished_at ? 'completed' : 'in_progress'),
                    'raw_visit' => [
                        'id' => $visit->id,
                        'site_id' => $visit->site_id,
                        'status' => $visit->status ?? ($visit->visit_finished_at ? 'completed' : 'in_progress'),
                        'visit_started_at' => $visit->visit_started_at ? $visit->visit_started_at->toIso8601String() : null,
                        'visit_finished_at' => $visit->visit_finished_at ? $visit->visit_finished_at->toIso8601String() : null,
                        'site' => $site ? ['id' => $site->id, 'name' => $site->name, 'code' => $site->code] : null,
                        'task_responses' => $taskResponses ? $taskResponses->map(function ($r) {
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
                                        'component' => $v->component ? [
                                            'id' => $v->component->id,
                                            'label' => $v->component->label,
                                            'component_type' => $v->component->component_type,
                                        ] : null,
                                        'task_component' => $v->component ? [
                                            'id' => $v->component->id,
                                            'label' => $v->component->label,
                                            'component_type' => $v->component->component_type,
                                        ] : null,
                                    ];
                                })->values()->all() : [],
                            ];
                        })->values()->all() : [],
                    ],
                ];
            })->values()->all();

            $allCompleted = $visits->every(fn($v) => $v->status === 'completed' || !is_null($v->visit_finished_at));

            return [
                'id' => $siteId,
                'site_name' => $site ? $site->name : 'موقع ميداني',
                'site_code' => $site ? $site->code : '',
                'consultants_count' => count($consultants),
                'consultants' => $consultants,
                'started_at' => $firstVisit && $firstVisit->visit_started_at ? Carbon::parse($firstVisit->visit_started_at)->format('H:i') : '--',
                'total_tasks' => array_sum(array_column($consultants, 'total_tasks')),
                'completed_tasks' => array_sum(array_column($consultants, 'completed_tasks')),
                'on_demand_tasks' => array_sum(array_column($consultants, 'on_demand_tasks')),
                'status' => $allCompleted ? 'completed' : 'in_progress',
            ];
        })->values()->all();

        $checkedInCount = $consultantsStatusList->where('status', 'checked_in')->count();
        $absentCount = $consultantsStatusList->where('status', 'absent')->count();
        $leaveCount = $consultantsStatusList->where('status', 'leave')->count();
        $offDayCount = $consultantsStatusList->whereIn('status', ['off_day', 'holiday'])->count();

        return Inertia::render('Admin/Dashboard/Index', [
            'stats' => [
                'total_consultants' => $totalConsultantsCount,
                'checked_in_consultants' => $checkedInCount,
                'absent_consultants' => $absentCount,
                'leave_consultants' => $leaveCount,
                'off_day_consultants' => $offDayCount,
                'is_official_holiday' => $isOfficialHoliday,
                'holiday_name' => $officialHolidayToday ? $officialHolidayToday->name : null,
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
