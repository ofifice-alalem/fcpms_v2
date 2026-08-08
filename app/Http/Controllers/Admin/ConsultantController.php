<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangeConsultantStatusRequest;
use App\Http\Requests\Admin\StoreConsultantRequest;
use App\Http\Requests\Admin\UpdateConsultantRequest;
use App\Models\Consultant;
use App\Models\WorkScheduleTemplate;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use App\Services\ConsultantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Support\Facades\Gate;

class ConsultantController extends Controller
{
    public function __construct(
        protected ConsultantRepositoryInterface $consultantRepository,
        protected ConsultantService $consultantService
    ) {}

    /**
     * Display paginated list of consultants with search, filtering, and stats.
     */
    public function index(Request $request): Response|JsonResponse
    {
        Gate::authorize('view-consultants');
        $filters = $request->only(['search', 'status', 'specialization', 'sort']);

        $consultants = $this->consultantRepository->getFilteredConsultants($filters, 15);

        $stats = [
            'total' => Consultant::count(),
            'active' => Consultant::where('employment_status', 'active')->count(),
            'suspended' => Consultant::where('employment_status', 'suspended')->count(),
            'vacation' => Consultant::where('employment_status', 'vacation')->count(),
        ];

        $today = now()->toDateString();
        $activeOfficialHoliday = \App\Models\OfficialHoliday::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        $workScheduleTemplates = WorkScheduleTemplate::select('id', 'name')->get();

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'data' => $consultants,
                'stats' => $stats,
                'activeOfficialHoliday' => $activeOfficialHoliday,
            ]);
        }

        return Inertia::render('Admin/Consultants/Index', [
            'consultants' => $consultants,
            'filters' => $filters,
            'stats' => $stats,
            'workScheduleTemplates' => $workScheduleTemplates,
            'activeOfficialHoliday' => $activeOfficialHoliday,
        ]);
    }

    /**
     * Store a newly created consultant and user account.
     */
    public function store(StoreConsultantRequest $request): RedirectResponse|JsonResponse
    {
        Gate::authorize('create-consultants');

        $consultant = $this->consultantService->registerNewConsultant($request->validated());

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء حساب الاستشاري بنجاح',
                'data' => $consultant->load('user'),
            ], 201);
        }

        return redirect()->route('admin.consultants.index')
            ->with('success', 'تم إنشاء سجل الاستشاري وحساب المستخدم الموازي بنجاح.');
    }

    /**
     * Display detailed profile information for a specific consultant.
     */
    public function show(Consultant $consultant): JsonResponse
    {
        Gate::authorize('view-consultants');

        $consultant->load(['user', 'workScheduleTemplate', 'leaves']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $consultant->id,
                'user_id' => $consultant->user_id,
                'employee_number' => $consultant->employee_number,
                'full_name' => $consultant->full_name,
                'email' => $consultant->user?->email,
                'phone' => $consultant->phone,
                'hire_date' => $consultant->hire_date?->format('Y-m-d'),
                'specialization' => $consultant->specialization,
                'employment_status' => $consultant->employment_status->value,
                'work_schedule_template' => $consultant->workScheduleTemplate?->name,
                'notes' => $consultant->notes,
                'created_at' => $consultant->created_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Update the specified consultant in storage.
     */
    public function update(UpdateConsultantRequest $request, Consultant $consultant): RedirectResponse|JsonResponse
    {
        Gate::authorize('edit-consultants');

        $updated = $this->consultantService->updateConsultant($consultant->id, $request->validated());

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث بيانات الاستشاري بنجاح',
                'data' => $updated,
            ]);
        }

        return redirect()->route('admin.consultants.index')
            ->with('success', 'تم تحديث بيانات الاستشاري بنجاح.');
    }

    /**
     * Update employment status (active, suspended, vacation) and handle session revocation.
     */
    public function updateStatus(ChangeConsultantStatusRequest $request, Consultant $consultant): RedirectResponse|JsonResponse
    {
        Gate::authorize('edit-consultants');

        $updated = $this->consultantService->changeEmploymentStatus($consultant->id, $request->validated()['status']);

        if ($request->wantsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم تغيير حالة الاستشاري بنجاح مع تحديث الجلسات الحالية',
                'data' => [
                    'id' => $updated->id,
                    'employment_status' => $updated->employment_status->value,
                    'user_status' => $updated->user?->status->value,
                ],
            ]);
        }

        return redirect()->route('admin.consultants.index')
            ->with('success', 'تم تحديث حالة التوظيف وإلغاء جلسات الدخول للوضع الموقوف.');
    }

    /**
     * Soft-delete consultant and parallel user account safely.
     */
    public function destroy(Consultant $consultant): RedirectResponse|JsonResponse
    {
        Gate::authorize('delete-consultants');

        $this->consultantService->deleteConsultant($consultant->id);

        if (request()->wantsJson() && !request()->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'message' => 'تم نقل سجل الاستشاري إلى الأرشيف بنجاح وحفظ البيانات التاريخية',
            ]);
        }

        return redirect()->route('admin.consultants.index')
            ->with('success', 'تم نقل سجل الاستشاري لحاوية الأرشيف بنجاح.');
    }
}
