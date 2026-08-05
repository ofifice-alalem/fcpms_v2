<?php

namespace Tests\Feature\Admin;

use App\Enums\ConsultantStatus;
use App\Enums\UserStatus;
use App\Models\Consultant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ConsultantManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'status' => 'active',
        ]);
    }

    public function test_can_render_consultants_index_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/consultants');

        $response->assertStatus(200);
    }

    public function test_can_create_consultant_with_user_account(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/consultants', [
            'full_name' => 'أحمد السالم',
            'email' => 'ahmed.salem@fcpms.test',
            'phone' => '091-234-5678',
            'specialization' => 'هندسة مدنية وشبكات',
            'hire_date' => '2026-01-01',
            'employment_status' => 'active',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'أحمد السالم',
            'email' => 'ahmed.salem@fcpms.test',
            'status' => UserStatus::ACTIVE->value,
        ]);

        $user = User::where('email', 'ahmed.salem@fcpms.test')->first();

        $this->assertDatabaseHas('consultants', [
            'user_id' => $user->id,
            'full_name' => 'أحمد السالم',
            'specialization' => 'هندسة مدنية وشبكات',
        ]);

        $consultant = Consultant::where('user_id', $user->id)->first();
        $this->assertNotNull($consultant->employee_number);
        $this->assertStringStartsWith('EMP-', $consultant->employee_number);
    }

    public function test_can_update_consultant_profile(): void
    {
        $user = User::factory()->create(['email' => 'old.email@fcpms.test']);
        $consultant = Consultant::create([
            'user_id' => $user->id,
            'employee_number' => 'EMP-1025',
            'full_name' => 'سالم البوعيشي',
            'specialization' => 'صيانة',
            'employment_status' => ConsultantStatus::ACTIVE,
        ]);

        $response = $this->actingAs($this->adminUser)->put("/admin/consultants/{$consultant->id}", [
            'full_name' => 'سالم البوعيشي المحدث',
            'email' => 'new.email@fcpms.test',
            'specialization' => 'تقنية معلومات',
            'employee_number' => 'EMP-9999', // attempt to change frozen number
        ]);

        $consultant->refresh();
        $user->refresh();

        $this->assertEquals('سالم البوعيشي المحدث', $consultant->full_name);
        $this->assertEquals('new.email@fcpms.test', $user->email);
        $this->assertEquals('EMP-1025', $consultant->employee_number); // Frozen! BR-005
    }

    public function test_changing_status_to_suspended_revokes_sessions(): void
    {
        $user = User::factory()->create(['status' => UserStatus::ACTIVE]);
        $consultant = Consultant::create([
            'user_id' => $user->id,
            'employee_number' => 'EMP-1030',
            'full_name' => 'استشاري معلق',
            'employment_status' => ConsultantStatus::ACTIVE,
        ]);

        // Insert mock session
        DB::table('sessions')->insert([
            'id' => 'mock_session_id_123',
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => 'mock_payload',
            'last_activity' => time(),
        ]);

        $response = $this->actingAs($this->adminUser)->patch("/admin/consultants/{$consultant->id}/status", [
            'status' => 'suspended',
        ]);

        $consultant->refresh();
        $user->refresh();

        $this->assertEquals(ConsultantStatus::SUSPENDED, $consultant->employment_status);
        $this->assertEquals(UserStatus::INACTIVE, $user->status); // BR-015
        $this->assertDatabaseMissing('sessions', ['id' => 'mock_session_id_123']);
    }

    public function test_soft_deleting_consultant_preserves_historical_records(): void
    {
        $user = User::factory()->create();
        $consultant = Consultant::create([
            'user_id' => $user->id,
            'employee_number' => 'EMP-1040',
            'full_name' => 'استشاري محذوف',
            'employment_status' => ConsultantStatus::ACTIVE,
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/admin/consultants/{$consultant->id}");

        $this->assertSoftDeleted('consultants', ['id' => $consultant->id]);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }
}
