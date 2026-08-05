<?php

namespace Tests\Feature\Admin;

use App\Enums\ConsultantStatus;
use App\Models\Consultant;
use App\Models\ConsultantLeave;
use App\Models\OfficialHoliday;
use App\Models\User;
use App\Models\WorkScheduleTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkScheduleManagementTest extends TestCase
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

    public function test_can_render_work_schedules_index_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/work-schedules');

        $response->assertStatus(200);
    }

    public function test_can_create_template_with_working_days(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/work-schedules/templates', [
            'name' => 'دوام كامل - 8 ساعات',
            'description' => 'دوام رسمي طيلة أيام الأسبوع ما عدا الجمعة والسبت',
            'is_default' => true,
            'days' => [
                ['day_of_week' => 0, 'is_working_day' => true],
                ['day_of_week' => 1, 'is_working_day' => true],
                ['day_of_week' => 2, 'is_working_day' => true],
                ['day_of_week' => 3, 'is_working_day' => true],
                ['day_of_week' => 4, 'is_working_day' => true],
                ['day_of_week' => 5, 'is_working_day' => false],
                ['day_of_week' => 6, 'is_working_day' => false],
            ],
        ]);

        $this->assertDatabaseHas('work_schedule_templates', [
            'name' => 'دوام كامل - 8 ساعات',
            'is_default' => true,
        ]);

        $template = WorkScheduleTemplate::where('name', 'دوام كامل - 8 ساعات')->first();
        $this->assertCount(7, $template->days);
    }

    public function test_only_one_default_template_can_exist(): void
    {
        $template1 = WorkScheduleTemplate::create([
            'name' => 'القالب الأول',
            'is_default' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->post('/admin/work-schedules/templates', [
            'name' => 'القالب الثاني الافتراضي',
            'is_default' => true,
            'days' => [],
        ]);

        $template1->refresh();

        $this->assertFalse($template1->is_default); // BR-007: old default reset
        $this->assertDatabaseHas('work_schedule_templates', [
            'name' => 'القالب الثاني الافتراضي',
            'is_default' => true,
        ]);
    }

    public function test_cannot_delete_template_assigned_to_active_consultants(): void
    {
        $template = WorkScheduleTemplate::create([
            'name' => 'قالب محمي',
            'is_default' => false,
        ]);

        $consultantUser = User::factory()->create();
        $consultant = Consultant::create([
            'user_id' => $consultantUser->id,
            'employee_number' => 'EMP-7777',
            'full_name' => 'استشاري نشط',
            'work_schedule_template_id' => $template->id,
            'employment_status' => ConsultantStatus::ACTIVE,
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/admin/work-schedules/templates/{$template->id}");

        $response->assertSessionHasErrors('template'); // BR-006: Block deletion
        $this->assertDatabaseHas('work_schedule_templates', ['id' => $template->id]);
    }

    public function test_adding_leave_updates_consultant_status_to_vacation(): void
    {
        $consultantUser = User::factory()->create();
        $consultant = Consultant::create([
            'user_id' => $consultantUser->id,
            'employee_number' => 'EMP-8888',
            'full_name' => 'استشاري مجاز',
            'employment_status' => ConsultantStatus::ACTIVE,
        ]);

        $response = $this->actingAs($this->adminUser)->post('/admin/work-schedules/leaves', [
            'consultant_id' => $consultant->id,
            'start_date' => '2026-08-10',
            'end_date' => '2026-08-15',
            'reason' => 'إجازة سنوية',
        ]);

        $consultant->refresh();

        $this->assertEquals(ConsultantStatus::VACATION, $consultant->employment_status); // BR-015
        $this->assertDatabaseHas('consultant_leaves', [
            'consultant_id' => $consultant->id,
            'reason' => 'إجازة سنوية',
        ]);
    }

    public function test_can_create_update_and_delete_official_holiday_with_date_range(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/work-schedules/holidays', [
            'name' => 'عطلة عيد الفطر المبارك',
            'start_date' => '2026-04-10',
            'end_date' => '2026-04-13',
            'notes' => 'قرار مجلس الوزراء رقم 12',
        ]);

        $this->assertDatabaseHas('official_holidays', [
            'name' => 'عطلة عيد الفطر المبارك',
            'start_date' => '2026-04-10',
            'end_date' => '2026-04-13',
        ]);

        $holiday = OfficialHoliday::where('name', 'عطلة عيد الفطر المبارك')->first();

        // Update
        $this->actingAs($this->adminUser)->put("/admin/work-schedules/holidays/{$holiday->id}", [
            'name' => 'عطلة عيد الفطر (تعديل)',
            'start_date' => '2026-04-10',
            'end_date' => '2026-04-14',
        ]);

        $this->assertDatabaseHas('official_holidays', [
            'id' => $holiday->id,
            'name' => 'عطلة عيد الفطر (تعديل)',
            'end_date' => '2026-04-14',
        ]);

        // Delete
        $this->actingAs($this->adminUser)->delete("/admin/work-schedules/holidays/{$holiday->id}");
        $this->assertDatabaseMissing('official_holidays', ['id' => $holiday->id]);
    }

    public function test_deleting_leave_reverts_consultant_status_to_active(): void
    {
        $consultantUser = User::factory()->create();
        $consultant = Consultant::create([
            'user_id' => $consultantUser->id,
            'employee_number' => 'EMP-9999',
            'full_name' => 'استشاري حذف الإجازة',
            'employment_status' => ConsultantStatus::ACTIVE,
        ]);

        $leave = ConsultantLeave::create([
            'consultant_id' => $consultant->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'reason' => 'إجازة للتجربة',
        ]);

        $consultant->update(['employment_status' => ConsultantStatus::VACATION]);

        $this->actingAs($this->adminUser)->delete("/admin/work-schedules/leaves/{$leave->id}");

        $consultant->refresh();

        $this->assertEquals(ConsultantStatus::ACTIVE, $consultant->employment_status);
        $this->assertDatabaseMissing('consultant_leaves', ['id' => $leave->id]);
    }
}
