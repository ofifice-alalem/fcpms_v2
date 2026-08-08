<?php

namespace App\Services;

use App\Repositories\Contracts\GovernanceRepositoryInterface;
use Illuminate\Support\Collection;

class GovernanceService
{
    public function __construct(
        protected GovernanceRepositoryInterface $governanceRepo
    ) {}

    public function getGovernanceDashboardData(): array
    {
        return [
            'roles' => $this->governanceRepo->getAllRolesWithPermissions(),
            'permissions_grouped' => $this->governanceRepo->getAllPermissionsGrouped(),
            'settings' => $this->governanceRepo->getAllSettings(),
            'activity_logs' => $this->governanceRepo->getRecentActivityLogs(150),
        ];
    }

    public function createRole(array $data)
    {
        $role = $this->governanceRepo->createRole($data);

        $this->governanceRepo->logActivity([
            'action' => 'create_role',
            'entity_type' => 'Role',
            'entity_id' => $role->id,
            'description' => "تم إنشاء دور صلاحيات جديد باسم: {$role->name}",
            'new_values' => [
                'name' => $role->name,
                'permissions' => $data['permissions'] ?? [],
            ],
        ]);

        return $role;
    }

    public function updateRole(int $id, array $data)
    {
        $role = $this->governanceRepo->findRoleById($id);

        if (strtolower($role->name) === 'super admin' || $role->name === 'مدير النظام القيادي') {
            // Protect Super Admin role name from being modified if attempting to change name
            if (isset($data['name']) && $data['name'] !== $role->name) {
                throw new \Exception('لا يمكن تغيير اسم دور مدير النظام القيادي (Super Admin).');
            }
        }

        $oldPermissions = $role->permissions->pluck('name')->toArray();

        $updatedRole = $this->governanceRepo->updateRole($id, $data);

        $this->governanceRepo->logActivity([
            'action' => 'update_role',
            'entity_type' => 'Role',
            'entity_id' => $updatedRole->id,
            'description' => "تم تحديث الصلاحيات المسندة للدور: {$updatedRole->name}",
            'old_values' => ['permissions' => $oldPermissions],
            'new_values' => ['permissions' => $data['permissions'] ?? []],
        ]);

        return $updatedRole;
    }

    public function deleteRole(int $id): bool
    {
        $role = $this->governanceRepo->findRoleById($id);

        if (strtolower($role->name) === 'super admin' || $role->name === 'مدير النظام القيادي') {
            throw new \Exception('محظور أمنياً: لا يمكن حذف دور مدير النظام القيادي (Super Admin).');
        }

        $roleName = $role->name;
        $deleted = $this->governanceRepo->deleteRole($id);

        if ($deleted) {
            $this->governanceRepo->logActivity([
                'action' => 'delete_role',
                'entity_type' => 'Role',
                'entity_id' => $id,
                'description' => "تم حذف دور الصلاحيات: {$roleName}",
                'old_values' => ['name' => $roleName],
            ]);
        }

        return $deleted;
    }

    public function updateSettings(array $settingsData)
    {
        $updatedSettings = [];
        $oldSettings = [];

        foreach ($settingsData as $item) {
            $key = $item['setting_key'];
            $val = $item['setting_value'] ?? '';
            $group = $item['group'] ?? 'general';
            $desc = $item['description'] ?? null;

            $updated = $this->governanceRepo->updateOrCreateSetting($key, $val, $group, $desc);
            $updatedSettings[$key] = $val;
        }

        $this->governanceRepo->logActivity([
            'action' => 'update_settings',
            'entity_type' => 'Setting',
            'description' => 'تم تحديث الإعدادات التشغيلية ومفاتيح النظام',
            'new_values' => $updatedSettings,
        ]);

        return $updatedSettings;
    }

    public function getAuditLogDetail(int $logId): array
    {
        $log = $this->governanceRepo->findActivityLogById($logId);
        if (!$log) {
            throw new \Exception('سجل التدقيق غير موجود.');
        }

        $oldValues = $log->old_values ?? [];
        $newValues = $log->new_values ?? [];
        $entityType = $log->entity_type ?? $log->model_type;
        $entityId = $log->entity_id ?? $log->model_id;

        if (($entityType === 'SiteVisit' || $log->action === 'execute_site_visit' || $log->action === 'open_site_visit') && $entityId) {
            $visit = \App\Models\SiteVisit::with(['site', 'taskResponses.taskDefinition', 'taskResponses.values.component'])->find($entityId);
            if ($visit) {
                if ($visit->site) {
                    $newValues['site_name'] = $visit->site->name;
                    if (!isset($oldValues['site_name'])) {
                        $oldValues['site_name'] = $visit->site->name;
                    }
                }

                if ($visit->taskResponses && $visit->taskResponses->isNotEmpty()) {
                    $taskSummaries = [];
                    foreach ($visit->taskResponses as $resp) {
                        $taskTitle = $resp->taskDefinition ? $resp->taskDefinition->title : "مهمة ميدانية #{$resp->task_definition_id}";
                        if ($resp->values && $resp->values->isNotEmpty()) {
                            $vals = $resp->values->map(function ($v) {
                                $lbl = $v->component ? $v->component->label : 'الإدخال';
                                $valText = $v->value ?? '—';
                                return "{$lbl}: {$valText}";
                            })->implode('، ');
                            $taskSummaries[] = "{$taskTitle}: {$vals}";
                        } else {
                            $statusStr = $resp->status === 'submitted' ? 'مكتملة ومستلمة' : ($resp->completed_at ? 'تم التنفيذ' : 'قيد الإجراء');
                            $taskSummaries[] = "{$taskTitle}: {$statusStr}";
                        }
                    }
                    if ((empty($newValues['tasks_details']) || !str_contains($newValues['tasks_details'], ':')) && !empty($taskSummaries)) {
                        $newValues['tasks_details'] = implode(' | ', $taskSummaries);
                    }
                }
            }
        }

        if (($entityType === 'WorkScheduleTemplate' || str_contains($log->action, 'schedule_template')) && $entityId) {
            $tmpl = \App\Models\WorkScheduleTemplate::with('days')->find($entityId);
            if ($tmpl && $tmpl->days) {
                if (empty($newValues['days'])) {
                    $newValues['days'] = $tmpl->days->toArray();
                }
            }
        }

        if (($entityType === 'TaskDefinition' || str_contains($log->action, 'task')) && $entityId) {
            if (!empty($newValues['site_assignments']) && is_array($newValues['site_assignments'])) {
                $newValues['site_assignments'] = collect($newValues['site_assignments'])->map(function ($item) {
                    if (is_array($item) && isset($item['site_id']) && !isset($item['site'])) {
                        $site = \App\Models\Site::find($item['site_id']);
                        if ($site) {
                            $item['site'] = ['name' => $site->name];
                        }
                    }
                    return $item;
                })->toArray();
            }
            if (!empty($oldValues['site_assignments']) && is_array($oldValues['site_assignments'])) {
                $oldValues['site_assignments'] = collect($oldValues['site_assignments'])->map(function ($item) {
                    if (is_array($item) && isset($item['site_id']) && !isset($item['site'])) {
                        $site = \App\Models\Site::find($item['site_id']);
                        if ($site) {
                            $item['site'] = ['name' => $site->name];
                        }
                    }
                    return $item;
                })->toArray();
            }
        }

        return [
            'id' => $log->id,
            'user_name' => $log->user ? $log->user->name : 'النظام الآلي',
            'user_email' => $log->user ? $log->user->email : null,
            'action' => $log->action,
            'entity_type' => $entityType ?? 'عام',
            'entity_id' => $entityId,
            'description' => $log->description,
            'old_values' => !empty($oldValues) ? $oldValues : null,
            'new_values' => !empty($newValues) ? $newValues : null,
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'created_at' => $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
