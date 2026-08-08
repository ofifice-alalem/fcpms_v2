<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GovernanceService;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Gate;

class GovernanceController extends Controller
{
    public function __construct(
        protected GovernanceService $governanceService
    ) {}

    public function index(): Response
    {
        Gate::authorize('manage-governance');

        $data = $this->governanceService->getGovernanceDashboardData();

        return Inertia::render('Admin/Governance/Index', [
            'roles' => $data['roles'],
            'permissionsGrouped' => $data['permissions_grouped'],
            'settings' => $data['settings'],
            'activityLogs' => $data['activity_logs'],
        ]);
    }

    public function storeRole(StoreRoleRequest $request): RedirectResponse
    {
        Gate::authorize('manage-roles');

        try {
            $this->governanceService->createRole($request->validated());
            return redirect()->back()->with('success', 'تم إنشاء دور الصلاحيات وتعيين المصفوفة بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function updateRole(UpdateRoleRequest $request, int $roleId): RedirectResponse
    {
        Gate::authorize('manage-roles');

        try {
            $this->governanceService->updateRole($roleId, $request->validated());
            return redirect()->back()->with('success', 'تم تحديث دور الصلاحيات وتعديل المصفوفة بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroyRole(int $roleId): RedirectResponse
    {
        Gate::authorize('manage-roles');

        try {
            $this->governanceService->deleteRole($roleId);
            return redirect()->back()->with('success', 'تم حذف دور الصلاحيات بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function updateSettings(UpdateSettingsRequest $request): RedirectResponse
    {
        Gate::authorize('manage-settings');

        try {
            $this->governanceService->updateSettings($request->validated()['settings']);
            return redirect()->back()->with('success', 'تم تحديث الإعدادات التشغيلية ومفاتيح النظام بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function showAuditLog(int $logId): JsonResponse
    {
        Gate::authorize('view-audit-logs');

        try {
            $log = $this->governanceService->getAuditLogDetail($logId);
            return response()->json([
                'success' => true,
                'data' => $log,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
