<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use App\Models\Setting;
use App\Models\ActivityLog;

interface GovernanceRepositoryInterface
{
    public function getAllRolesWithPermissions(): Collection;
    
    public function getAllPermissionsGrouped(): Collection;

    public function findRoleById(int $id): Role;

    public function createRole(array $data): Role;

    public function updateRole(int $id, array $data): Role;

    public function deleteRole(int $id): bool;

    public function getAllSettings(): Collection;

    public function updateOrCreateSetting(string $key, $value, string $group = 'general', ?string $description = null): Setting;

    public function getRecentActivityLogs(int $limit = 100): Collection;

    public function findActivityLogById(int $id): ?ActivityLog;

    public function logActivity(array $data): ActivityLog;
}
