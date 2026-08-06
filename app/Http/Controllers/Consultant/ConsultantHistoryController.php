<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Models\Consultant;
use App\Models\DailyRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ConsultantHistoryController extends Controller
{
    protected function getConsultant(): Consultant
    {
        $user = Auth::user();
        if ($user && $user->consultant) {
            return $user->consultant;
        }

        abort(403, 'عذراً، هذا الحساب لا يملك ملف استشاري ميداني لفتح هذه البوابة.');
    }

    public function index(Request $request): Response
    {
        $consultant = $this->getConsultant();

        // Month filter format: YYYY-MM
        $selectedMonth = $request->query('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($selectedMonth . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($selectedMonth . '-01')->endOfMonth();
        $today = Carbon::today();

        // Query existing records in this month
        $existingRecords = DailyRecord::where('consultant_id', $consultant->id)
            ->whereBetween('work_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->withCount('siteVisits')
            ->with(['siteVisits.taskResponses.taskDefinition'])
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->work_date)->toDateString();
            });

        $days = [];
        $attendedCount = 0;
        $absentCount = 0;
        $weekendCount = 0;
        $totalProgressSum = 0;

        $current = $startOfMonth->copy();
        while ($current->lte($endOfMonth)) {
            $dateStr = $current->toDateString();
            $isWeekend = $current->isFriday() || $current->isSaturday();
            $isFuture = $current->gt($today);

            // Do not show future days
            if ($isFuture) {
                $current->addDay();
                continue;
            }

            $record = $existingRecords->get($dateStr);

            $status = 'absent';
            $statusLabel = 'غائب';
            $statusType = 'danger';

            if ($isWeekend) {
                $status = 'weekend';
                $statusLabel = 'عطلة أسبوعية';
                $statusType = 'weekend';
                $weekendCount++;
            } elseif ($record && ($record->check_in_time || $record->site_visits_count > 0 || $record->completed_daily_tasks > 0)) {
                if ((float)$record->completion_percentage >= 100) {
                    $status = 'attended';
                    $statusLabel = 'حاضر ومكتمل 🟢';
                    $statusType = 'success';
                } else {
                    $status = 'in_progress';
                    $statusLabel = 'قيد التوثيق 🟡';
                    $statusType = 'warning';
                }
                $attendedCount++;
                $totalProgressSum += (float)$record->completion_percentage;
            } else {
                $status = 'absent';
                $statusLabel = 'غائب 🔴';
                $statusType = 'danger';
                $absentCount++;
            }

            $onDemandTasksCount = 0;
            if ($record) {
                foreach ($record->siteVisits as $visit) {
                    foreach ($visit->taskResponses as $resp) {
                        $taskDef = $resp->taskDefinition;
                        if ($taskDef && ($taskDef->task_type === \App\Enums\TaskType::ON_DEMAND || $taskDef->task_type?->value === 'on_demand')) {
                            $onDemandTasksCount++;
                        }
                    }
                }
            }

            $days[] = [
                'date' => $dateStr,
                'day_name' => $current->locale('ar')->translatedFormat('l'),
                'formatted_date' => $current->format('d - m - Y'),
                'is_today' => $current->isToday(),
                'is_weekend' => $isWeekend,
                'is_future' => false,
                'status' => $status,
                'status_label' => $statusLabel,
                'status_type' => $statusType,
                'record' => $record ? [
                    'id' => $record->id,
                    'check_in_time' => $record->check_in_time ? Carbon::parse($record->check_in_time)->timezone('Africa/Tripoli')->locale('ar')->translatedFormat('h:i a') : null,
                    'completed_daily_tasks' => $record->completed_daily_tasks,
                    'required_daily_tasks' => $record->required_daily_tasks,
                    'completion_percentage' => (float)$record->completion_percentage,
                    'site_visits_count' => $record->site_visits_count,
                    'on_demand_tasks_count' => $onDemandTasksCount,
                ] : null,
            ];

            $current->addDay();
        }

        $totalWorkingDays = count($days) - $weekendCount;
        $avgProgress = $attendedCount > 0 ? round($totalProgressSum / $attendedCount, 1) : 0;

        return Inertia::render('Consultant/History/Index', [
            'consultant' => $consultant,
            'selectedMonth' => $selectedMonth,
            'days' => array_reverse($days), // Latest days first
            'stats' => [
                'total_working_days' => max(0, $totalWorkingDays),
                'attended_count' => $attendedCount,
                'absent_count' => $absentCount,
                'avg_progress' => $avgProgress,
            ],
        ]);
    }
}
