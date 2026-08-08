<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\GovernanceRepositoryInterface;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Support\Collection;

class GovernanceRepository implements GovernanceRepositoryInterface
{
    public function getAllRolesWithPermissions(): Collection
    {
        return Role::with(['permissions'])
            ->withCount('users')
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'guard_name' => $role->guard_name,
                    'users_count' => $role->users_count,
                    'permissions_count' => $role->permissions->count(),
                    'permissions' => $role->permissions->pluck('name')->toArray(),
                    'created_at' => $role->created_at ? $role->created_at->toIso8601String() : null,
                ];
            })
            ->values();
    }

    public function getAllPermissionsGrouped(): Collection
    {
        if (Permission::count() === 0) {
            $defaultPermissions = [
                // Sites
                'view-sites', 'create-sites', 'edit-sites', 'delete-sites',
                // Consultants
                'view-consultants', 'create-consultants', 'edit-consultants', 'delete-consultants',
                // Schedules
                'view-schedules', 'create-schedules', 'edit-schedules', 'delete-schedules',
                // Tasks
                'view-tasks', 'create-tasks', 'edit-tasks', 'delete-tasks', 'assign-tasks',
                // Visits
                'execute-visits', 'view-visit-history',
                // Reports
                'view-reports', 'export-reports', 'retroactive-audit',
                // Governance
                'manage-governance', 'manage-roles', 'manage-settings', 'view-audit-logs',
                // Backups
                'manage-backups', 'create-backups', 'restore-backups',
            ];

            foreach ($defaultPermissions as $permName) {
                Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            }
        }

        $permissions = Permission::all();

        return $permissions->groupBy(function ($perm) {
            $parts = explode('-', $perm->name);
            return count($parts) > 1 ? $parts[count($parts) - 1] : 'general';
        })->map(function ($group, $key) {
            return [
                'group' => $key,
                'items' => $group->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                ])->values(),
            ];
        })->values();
    }

    public function findRoleById(int $id): Role
    {
        return Role::with(['permissions'])->withCount('users')->findOrFail($id);
    }

    public function createRole(array $data): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    public function updateRole(int $id, array $data): Role
    {
        $role = $this->findRoleById($id);

        if (isset($data['name'])) {
            $role->name = $data['name'];
            $role->save();
        }

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    public function deleteRole(int $id): bool
    {
        $role = $this->findRoleById($id);

        if ($role->users_count > 0) {
            throw new \Exception('لا يمكن حذف هذا الدور لأنه مخصّص حالياً لمستخدمين نشطين.');
        }

        return (bool) $role->delete();
    }

    public function getAllSettings(): Collection
    {
        return Setting::all()->map(function ($setting) {
            return [
                'id' => $setting->id,
                'setting_key' => $setting->setting_key,
                'setting_value' => $setting->setting_value,
                'group' => $setting->group ?? 'general',
                'description' => $setting->description,
                'updated_at' => $setting->updated_at ? $setting->updated_at->toIso8601String() : null,
            ];
        })->values();
    }

    public function updateOrCreateSetting(string $key, $value, string $group = 'general', ?string $description = null): Setting
    {
        return Setting::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => is_array($value) ? json_encode($value) : (string) $value,
                'group' => $group,
                'description' => $description,
            ]
        );
    }

    public function getRecentActivityLogs(int $limit = 100): Collection
    {
        return ActivityLog::with('user')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user_id' => $log->user_id,
                    'user_name' => $log->user ? $log->user->name : 'النظام الآلي',
                    'user_email' => $log->user ? $log->user->email : null,
                    'action' => $log->action,
                    'entity_type' => $log->entity_type ?? $log->model_type ?? 'عام',
                    'entity_id' => $log->entity_id ?? $log->model_id,
                    'description' => $log->description,
                    'old_values' => $log->old_values,
                    'new_values' => $log->new_values,
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'created_at' => $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : null,
                ];
            })
            ->values();
    }

    public function findActivityLogById(int $id): ?ActivityLog
    {
        return ActivityLog::with('user')->find($id);
    }

    public function logActivity(array $data): ActivityLog
    {
        return \App\Helpers\ActivityLogger::log(
            action: $data['action'],
            entityType: $data['entity_type'] ?? $data['model_type'] ?? 'General',
            entityId: $data['entity_id'] ?? $data['model_id'] ?? null,
            description: $data['description'] ?? null,
            oldValues: $data['old_values'] ?? null,
            newValues: $data['new_values'] ?? null,
            userId: $data['user_id'] ?? auth()->id()
        );
    }
}
