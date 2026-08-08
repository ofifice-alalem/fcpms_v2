<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Setting;
use App\Models\ActivityLog;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemGovernanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Spatie roles
        Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::create(['name' => 'hr', 'guard_name' => 'web']);

        $this->adminUser = User::factory()->create([
            'status' => 'active',
        ]);
        $this->adminUser->assignRole('hr');
    }

    public function test_can_render_governance_index_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/governance');

        $response->assertStatus(200);
    }

    public function test_can_create_new_role_with_permissions(): void
    {
        $perm = Permission::create(['name' => 'manage-sites', 'guard_name' => 'web']);

        $response = $this->actingAs($this->adminUser)->post('/admin/governance/roles', [
            'name' => 'مشرف مواضع أخصائي',
            'permissions' => ['manage-sites'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'name' => 'مشرف مواضع أخصائي',
        ]);
    }

    public function test_super_admin_role_permissions_cannot_be_revoked(): void
    {
        $superAdminRole = Role::findByName('Super Admin');

        $response = $this->actingAs($this->adminUser)->put("/admin/governance/roles/{$superAdminRole->id}", [
            'name' => 'اسم معدل محظور',
            'permissions' => [],
        ]);

        $response->assertSessionHasErrors();
        $this->assertEquals('Super Admin', $superAdminRole->fresh()->name);
    }

    public function test_all_critical_model_changes_generate_activity_log_entry(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/governance/roles', [
            'name' => 'دور مدقق جديد',
            'permissions' => [],
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'create_role',
            'entity_type' => 'Role',
        ]);
    }

    public function test_can_update_system_settings(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/governance/settings', [
            'settings' => [
                [
                    'setting_key' => 'MAX_VISITS_PER_DAY',
                    'setting_value' => '10',
                    'group' => 'general',
                    'description' => 'الحد الأقصى للزيارات اليومية',
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('settings', [
            'setting_key' => 'MAX_VISITS_PER_DAY',
            'setting_value' => '10',
        ]);
    }

    public function test_can_fetch_audit_log_details(): void
    {
        $log = ActivityLog::create([
            'user_id' => $this->adminUser->id,
            'action' => 'test_action',
            'entity_type' => 'TestModel',
            'entity_id' => 1,
            'description' => 'وصف تجريبي',
            'old_values' => ['status' => 'old'],
            'new_values' => ['status' => 'new'],
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->adminUser)->get("/admin/governance/audit-logs/{$log->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $log->id,
                'action' => 'test_action',
            ],
        ]);
    }
}
