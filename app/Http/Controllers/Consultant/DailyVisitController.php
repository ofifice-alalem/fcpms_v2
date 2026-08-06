<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consultant\OpenSiteVisitRequest;
use App\Http\Requests\Consultant\SaveTaskResponsesRequest;
use App\Http\Requests\Consultant\StartDailyRecordRequest;
use App\Models\Consultant;
use App\Models\SiteVisit;
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
        $dailyRecord = $this->visitService->getTodayRecord($consultant);

        // Recalculate daily task stats strictly for task_type = 'daily' (excluding on-demand tasks)
        $completedDailyCount = \App\Models\TaskResponse::whereHas('siteVisit', fn ($q) => $q->where('daily_record_id', $dailyRecord->id))
            ->whereHas('taskDefinition', fn ($q) => $q->where('task_type', 'daily'))
            ->where('status', 'submitted')
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

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'daily_record' => $dailyRecord,
                    'available_sites' => $availableSites,
                    'active_visit' => $activeVisit,
                    'available_on_demand_tasks' => $availableOnDemandTasks,
                ],
            ]);
        }

        return Inertia::render('Consultant/DailyVisits/Index', [
            'consultant' => $consultant,
            'dailyRecord' => $dailyRecord,
            'availableSites' => $availableSites,
            'activeVisit' => $activeVisit,
            'availableOnDemandTasks' => $availableOnDemandTasks,
        ]);
    }

    public function startDay(StartDailyRecordRequest $request): RedirectResponse|JsonResponse
    {
        $consultant = $this->getConsultant();
        $record = $this->visitService->startDay($consultant, $request->input('notes'));

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم بدء اليوم العملي بنجاح',
                'data' => $record,
            ]);
        }

        return redirect()->back()->with('success', 'تم بدء اليوم العملي بنجاح');
    }

    public function createVisit(): Response
    {
        $consultant = $this->getConsultant();
        $dailyRecord = $this->visitService->getTodayRecord($consultant);
        $availableSites = $this->visitService->getAvailableSites($dailyRecord);

        return Inertia::render('Consultant/DailyVisits/Execute', [
            'consultant' => $consultant,
            'dailyRecord' => $dailyRecord,
            'visit' => null,
            'availableSites' => $availableSites,
            'availableOnDemandTasks' => [],
        ]);
    }

    public function executeVisit(SiteVisit $visit): Response
    {
        $consultant = $this->getConsultant();
        $dailyRecord = $this->visitService->getTodayRecord($consultant);
        $visitDetails = $this->visitService->getVisitDetails($visit->id);
        $availableSites = $this->visitService->getAvailableSites($dailyRecord);
        $availableOnDemandTasks = $this->visitService->getAvailableOnDemandTasks($visit->site_id, $consultant);

        return Inertia::render('Consultant/DailyVisits/Execute', [
            'consultant' => $consultant,
            'dailyRecord' => $dailyRecord,
            'visit' => $visitDetails,
            'availableSites' => $availableSites,
            'availableOnDemandTasks' => $availableOnDemandTasks,
        ]);
    }

    public function storeVisit(OpenSiteVisitRequest $request): RedirectResponse|JsonResponse
    {
        $consultant = $this->getConsultant();
        $dailyRecord = $this->visitService->getTodayRecord($consultant);

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
