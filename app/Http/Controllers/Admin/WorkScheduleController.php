<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreConsultantLeaveRequest;
use App\Http\Requests\Admin\StoreOfficialHolidayRequest;
use App\Http\Requests\Admin\StoreScheduleTemplateRequest;
use App\Http\Requests\Admin\UpdateConsultantLeaveRequest;
use App\Http\Requests\Admin\UpdateOfficialHolidayRequest;
use App\Http\Requests\Admin\UpdateScheduleTemplateRequest;
use App\Models\ConsultantLeave;
use App\Models\OfficialHoliday;
use App\Models\WorkScheduleTemplate;
use App\Services\WorkScheduleService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WorkScheduleController extends Controller
{
    public function __construct(
        protected WorkScheduleService $scheduleService
    ) {}

    /**
     * Display work schedules, official holidays, and consultant leaves index page.
     */
    public function index(): Response
    {
        $data = $this->scheduleService->getWorkSchedulesPageData();

        return Inertia::render('Admin/WorkSchedules/Index', $data);
    }

    /**
     * Store a new work schedule template.
     */
    public function storeTemplate(StoreScheduleTemplateRequest $request): RedirectResponse
    {
        $this->scheduleService->createScheduleTemplate($request->validated());

        return redirect()->back()->with('success', 'تم إنشاء قالب الدوام وتعيين أيام العمل بنجاح.');
    }

    /**
     * Update an existing work schedule template.
     */
    public function updateTemplate(UpdateScheduleTemplateRequest $request, WorkScheduleTemplate $template): RedirectResponse
    {
        $this->scheduleService->updateScheduleTemplate($template, $request->validated());

        return redirect()->back()->with('success', 'تم تحديث بيانات قالب الدوام وأيام العمل بنجاح.');
    }

    /**
     * Delete an unassigned work schedule template (BR-006).
     */
    public function destroyTemplate(WorkScheduleTemplate $template): RedirectResponse
    {
        $this->scheduleService->deleteScheduleTemplate($template);

        return redirect()->back()->with('success', 'تم حذف قالب الدوام بنجاح.');
    }

    /**
     * Record an official holiday.
     */
    public function storeHoliday(StoreOfficialHolidayRequest $request): RedirectResponse
    {
        $this->scheduleService->addOfficialHoliday($request->validated());

        return redirect()->back()->with('success', 'تم تسجيل العطلة الرسمية العامة بنجاح.');
    }

    /**
     * Update an official holiday.
     */
    public function updateHoliday(UpdateOfficialHolidayRequest $request, OfficialHoliday $holiday): RedirectResponse
    {
        $this->scheduleService->updateOfficialHoliday($holiday, $request->validated());

        return redirect()->back()->with('success', 'تم تحديث بيانات العطلة الرسمية بنجاح.');
    }

    /**
     * Delete an official holiday.
     */
    public function destroyHoliday(OfficialHoliday $holiday): RedirectResponse
    {
        $this->scheduleService->deleteOfficialHoliday($holiday);

        return redirect()->back()->with('success', 'تم حذف العطلة الرسمية بنجاح.');
    }

    /**
     * Record a consultant leave and update status to 'vacation' (BR-015).
     */
    public function storeLeave(StoreConsultantLeaveRequest $request): RedirectResponse
    {
        $this->scheduleService->recordConsultantLeave($request->validated());

        return redirect()->back()->with('success', 'تم تسجيل إجازة الاستشاري وتحديث حالته التشغيلية إلى "في إجازة".');
    }

    /**
     * Update a consultant leave.
     */
    public function updateLeave(UpdateConsultantLeaveRequest $request, ConsultantLeave $leave): RedirectResponse
    {
        $this->scheduleService->updateConsultantLeave($leave, $request->validated());

        return redirect()->back()->with('success', 'تم تحديث بيانات إجازة الاستشاري بنجاح.');
    }

    /**
     * Delete a consultant leave.
     */
    public function destroyLeave(ConsultantLeave $leave): RedirectResponse
    {
        $this->scheduleService->deleteConsultantLeave($leave);

        return redirect()->back()->with('success', 'تم حذف سجل الإجازة بنجاح.');
    }
}
