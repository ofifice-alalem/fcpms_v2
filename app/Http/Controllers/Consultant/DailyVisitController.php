<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consultant\OpenSiteVisitRequest;
use App\Http\Requests\Consultant\SaveTaskResponsesRequest;
use App\Http\Requests\Consultant\StartDailyRecordRequest;
use App\Models\Consultant;
use App\Models\SiteVisit;
use App\Models\TaskResponse;
use App\Services\ConsultantVisitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DailyVisitController extends Controller
{
    public function __construct(
        protected ConsultantVisitService $visitService
    ) {}

    protected function getConsultant(): Consultant
    {
        $user = Auth::user();
        if ($user && $user->consultant) {
            return $user->consultant;
        }

        // Fallback or preview for admin / first consultant
        $consultant = Consultant::first();
        if (!$consultant) {
            $consultant = Consultant::create([
                'user_id' => $user?->id,
                'employee_number' => 'CONS-001',
                'full_name' => $user?->name ?? 'الاستشاري الميداني',
                'phone' => '0910000000',
                'hire_date' => now(),
                'specialization' => 'مفتش ميداني عام',
            ]);
        }

        return $consultant;
    }

    public function index(Request $request): Response|JsonResponse
    {
        $consultant = $this->getConsultant();
        $targetDate = $request->query('date');
        $dailyRecord = $targetDate 
            ? $this->visitService->getRecordForDate($consultant, $targetDate) 
            : $this->visitService->getTodayRecord($consultant);

        // Sanitize any existing task responses marked as submitted without having any valid filled values
        \App\Models\TaskResponse::whereHas('siteVisit', fn ($q) => $q->where('daily_record_id', $dailyRecord->id))
            ->where('status', 'submitted')
            ->whereDoesntHave('values', function ($vq) {
                $vq->whereNotNull('value')->whereRaw("TRIM(value) != '' AND value != '[]' AND value != 'null'");
            })
            ->update([
                'status' => 'draft',
                'submitted_at' => null,
                'completed_at' => null,
            ]);

        // Recalculate daily task stats strictly for filled task_type = 'daily' (excluding on-demand tasks)
        $completedDailyCount = \App\Models\TaskResponse::whereHas('siteVisit', fn ($q) => $q->where('daily_record_id', $dailyRecord->id))
            ->whereHas('taskDefinition', fn ($q) => $q->where('task_type', 'daily'))
            ->where('status', 'submitted')
            ->whereHas('values', function ($vq) {
                $vq->whereNotNull('value')->whereRaw("TRIM(value) != '' AND value != '[]' AND value != 'null'");
            })
            ->count();

        $requiredCount = max(1, $dailyRecord->required_daily_tasks);
        $calcPercentage = min(100, round(($completedDailyCount / $requiredCount) * 100, 2));

        if ($dailyRecord->completed_daily_tasks !== $completedDailyCount || (float)$dailyRecord->completion_percentage !== (float)$calcPercentage) {
            $dailyRecord->update([
                'completed_daily_tasks' => $completedDailyCount,
                'completion_percentage' => $calcPercentage,
            ]);
        }

        $availableSites = $this->visitService->getAvailableSites($dailyRecord);
        $activeVisit = $this->visitService->getActiveVisit($dailyRecord);

        $availableOnDemandTasks = [];
        if ($activeVisit) {
            $availableOnDemandTasks = $this->visitService->getAvailableOnDemandTasks($activeVisit->site_id, $consultant);
        }

        $dailyRecord->load([
            'siteVisits.site',
            'siteVisits.taskResponses.taskDefinition.components.options',
            'siteVisits.taskResponses.values.component',
            'siteVisits.taskResponses.attachments',
        ]);

        $recordDate = \Carbon\Carbon::parse($dailyRecord->work_date)->toDateString();
        $todayDate = \Carbon\Carbon::today()->toDateString();
        $isHistorical = $recordDate !== $todayDate;

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'daily_record' => $dailyRecord,
                    'available_sites' => $availableSites,
                    'active_visit' => $activeVisit,
                    'available_on_demand_tasks' => $availableOnDemandTasks,
                    'is_historical' => $isHistorical,
                    'selected_date' => $recordDate,
                ],
            ]);
        }

        return Inertia::render('Consultant/DailyVisits/Index', [
            'consultant' => $consultant,
            'dailyRecord' => $dailyRecord,
            'availableSites' => $availableSites,
            'activeVisit' => $activeVisit,
            'availableOnDemandTasks' => $availableOnDemandTasks,
            'isHistorical' => $isHistorical,
            'selectedDate' => $recordDate,
        ]);
    }

    public function startDay(StartDailyRecordRequest $request): RedirectResponse|JsonResponse
    {
        $consultant = $this->getConsultant();
        $targetDate = $request->input('date');
        $record = $targetDate ? $this->visitService->getRecordForDate($consultant, $targetDate) : $this->visitService->getTodayRecord($consultant);

        if (!$record->check_in_time) {
            $record->update([
                'check_in_time' => \Carbon\Carbon::now(),
                'notes' => $request->input('notes') ?: $record->notes,
            ]);
        }

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم بدء اليوم العملي بنجاح',
                'data' => $record,
            ]);
        }

        return redirect()->back()->with('success', 'تم بدء اليوم العملي بنجاح');
    }

    public function createVisit(Request $request): Response
    {
        $consultant = $this->getConsultant();
        $targetDate = $request->query('date');
        $dailyRecord = $targetDate ? $this->visitService->getRecordForDate($consultant, $targetDate) : $this->visitService->getTodayRecord($consultant);
        $availableSites = $this->visitService->getAvailableSites($dailyRecord);

        $recordDate = \Carbon\Carbon::parse($dailyRecord->work_date)->toDateString();
        $todayDate = \Carbon\Carbon::today()->toDateString();
        $isHistorical = $recordDate !== $todayDate;

        return Inertia::render('Consultant/DailyVisits/Execute', [
            'consultant' => $consultant,
            'dailyRecord' => $dailyRecord,
            'visit' => null,
            'availableSites' => $availableSites,
            'availableOnDemandTasks' => [],
            'isHistorical' => $isHistorical,
            'selectedDate' => $recordDate,
        ]);
    }

    public function executeVisit(SiteVisit $visit): Response
    {
        $consultant = $this->getConsultant();
        $dailyRecord = $visit->dailyRecord;
        $visitDetails = $this->visitService->getVisitDetails($visit->id);
        $availableSites = $this->visitService->getAvailableSites($dailyRecord);
        $availableOnDemandTasks = $this->visitService->getAvailableOnDemandTasks($visit->site_id, $consultant);

        $recordDate = \Carbon\Carbon::parse($dailyRecord->work_date)->toDateString();
        $todayDate = \Carbon\Carbon::today()->toDateString();
        $isHistorical = $recordDate !== $todayDate;

        return Inertia::render('Consultant/DailyVisits/Execute', [
            'consultant' => $consultant,
            'dailyRecord' => $dailyRecord,
            'visit' => $visitDetails,
            'availableSites' => $availableSites,
            'availableOnDemandTasks' => $availableOnDemandTasks,
            'isHistorical' => $isHistorical,
            'selectedDate' => $recordDate,
        ]);
    }

    public function storeVisit(OpenSiteVisitRequest $request): RedirectResponse|JsonResponse
    {
        $consultant = $this->getConsultant();
        $targetDate = $request->input('date');
        $dailyRecord = $targetDate ? $this->visitService->getRecordForDate($consultant, $targetDate) : $this->visitService->getTodayRecord($consultant);

        $visit = $this->visitService->openSiteVisit(
            $dailyRecord,
            $request->input('site_id'),
            $request->input('notes')
        );

        $availableOnDemandTasks = $this->visitService->getAvailableOnDemandTasks($visit->site_id, $consultant);

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم فتح زيارة الموقع وجلب المهام اليومية الدورية بنجاح',
                'data' => [
                    'site_visit_id' => $visit->id,
                    'site_name' => $visit->site?->name,
                    'status' => $visit->status,
                    'active_visit' => $visit,
                    'available_on_demand_tasks' => $availableOnDemandTasks,
                ],
            ], 201);
        }

        $recordDate = \Carbon\Carbon::parse($dailyRecord->work_date)->toDateString();
        $todayDate = \Carbon\Carbon::today()->toDateString();

        if ($recordDate !== $todayDate || $targetDate) {
            return redirect()->route('consultant.site-visits.execute', ['visit' => $visit->id, 'date' => $recordDate])->with('success', 'تم فتح زيارة الموقع بنجاح');
        }

        return redirect()->route('consultant.site-visits.execute', $visit->id)->with('success', 'تم فتح زيارة الموقع بنجاح');
    }

    public function triggerOnDemand(Request $request, SiteVisit $visit): JsonResponse|RedirectResponse
    {
        $request->validate([
            'task_definition_id' => ['required', 'integer', 'exists:task_definitions,id'],
        ]);

        $taskResponse = $this->visitService->triggerOnDemandTask($visit, $request->input('task_definition_id'));
        $updatedVisit = $this->visitService->getVisitDetails($visit->id);

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم تفعيل وتضمين المهمة عند الحاجة لنموذج الزيارة الحالي',
                'data' => [
                    'task_response' => $taskResponse,
                    'active_visit' => $updatedVisit,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'تم تفعيل المهمة عند الحاجة بنجاح');
    }

    public function removeOnDemandTask(Request $request, SiteVisit $visit, TaskResponse $response): JsonResponse|RedirectResponse
    {
        $this->visitService->removeOnDemandTask($visit, $response->id);

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المهمة عند الحاجة بنجاح',
            ]);
        }

        return redirect()->back()->with('success', 'تم حذف المهمة عند الحاجة بنجاح');
    }

    public function saveResponses(SaveTaskResponsesRequest $request, SiteVisit $visit): JsonResponse|RedirectResponse
    {
        $responses = $request->input('responses', []);
        if ($request->input('complete_visit')) {
            $responses['_complete_visit'] = true;
        }

        $attachments = $request->file('attachments', []);

        $updatedVisit = $this->visitService->saveTaskResponses($visit, $responses, $attachments);

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ وتحديث الإجابات وإثباتات الصور بنجاح',
                'data' => [
                    'site_visit_id' => $updatedVisit->id,
                    'status' => $updatedVisit->status,
                    'submitted_values_count' => count($responses),
                ],
            ]);
        }

        $recordDate = \Carbon\Carbon::parse($visit->dailyRecord->work_date)->toDateString();
        $todayDate = \Carbon\Carbon::today()->toDateString();

        if ($request->input('complete_visit') && $recordDate !== $todayDate) {
            return redirect()->route('consultant.visits.index', ['date' => $recordDate])->with('success', 'تم حفظ إجابات وتفاصيل الزيارة بنجاح');
        }

        return redirect()->back()->with('success', 'تم حفظ إجابات وتفاصيل الزيارة بنجاح');
    }

    public function show(SiteVisit $visit): JsonResponse
    {
        $details = $this->visitService->getVisitDetails($visit->id);

        return response()->json([
            'success' => true,
            'data' => $details,
        ]);
    }

    public function destroy(SiteVisit $visit): JsonResponse|RedirectResponse
    {
        $this->visitService->cancelVisit($visit);

        if (request()->wantsJson() && !request()->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء ومسح الزيارة بنجاح',
            ]);
        }

        return redirect()->route('consultant.visits.index')->with('success', 'تم إلغاء ومسح الزيارة بنجاح');
    }
}
