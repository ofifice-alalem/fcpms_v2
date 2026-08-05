<?php

namespace Tests\Feature\Admin;

use App\Models\Consultant;
use App\Models\DailyRecord;
use App\Models\Site;
use App\Models\SiteVisit;
use App\Models\TaskDefinition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskBuilderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create([
            'username' => 'hr_admin',
            'status' => 'active',
        ]);
    }

    public function test_can_render_task_builder_index_page(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.tasks.index'));

        $response->assertStatus(200);
    }

    public function test_can_render_task_builder_create_page(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.tasks.create'));

        $response->assertStatus(200);
    }

    public function test_can_render_task_builder_edit_page(): void
    {
        $task = TaskDefinition::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.tasks.edit', $task->id));

        $response->assertStatus(200);
    }

    public function test_can_create_task_with_dynamic_components_and_conditional_rules(): void
    {
        $payload = [
            'title' => 'تفقد أنظمة السلامة ومطافئ الحريق',
            'description' => 'استبيان فحص دوري لمعدات الحريق في الموقع',
            'task_type' => 'daily',
            'is_active' => true,
            'components' => [
                [
                    'temp_id' => 'comp_1',
                    'label' => 'هل طفايات الحريق صالحة للاستخدام؟',
                    'component_type' => 'select',
                    'placeholder' => 'اختر الحالة',
                    'is_required' => true,
                    'display_order' => 1,
                    'options' => [
                        ['label' => 'نعم مطابقة', 'value' => 'yes'],
                        ['label' => 'لا تحتاج صيانة', 'value' => 'no'],
                    ],
                ],
                [
                    'temp_id' => 'comp_2',
                    'label' => 'قم برفع صورة إثبات طفاية الحريق',
                    'component_type' => 'image_upload',
                    'placeholder' => 'ارفع صورة واضحة',
                    'is_required' => true,
                    'display_order' => 2,
                    'conditional_parent_temp_id' => 'comp_1',
                    'conditional_value' => 'no',
                ],
            ],
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.tasks.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('task_definitions', [
            'title' => 'تفقد أنظمة السلامة ومطافئ الحريق',
            'task_type' => 'daily',
            'is_active' => true,
        ]);

        $task = TaskDefinition::where('title', 'تفقد أنظمة السلامة ومطافئ الحريق')->first();
        $this->assertNotNull($task);
        $this->assertCount(2, $task->components);

        $parentComp = $task->components()->where('component_type', 'select')->first();
        $childComp = $task->components()->where('component_type', 'image_upload')->first();

        $this->assertEquals('no', $childComp->conditional_value);
        $this->assertEquals($parentComp->id, $childComp->conditional_parent_id);
    }

    public function test_task_assignments_to_sites_and_consultants(): void
    {
        $site = Site::create([
            'name' => 'موقع طرابلس الرئيسي',
            'code' => 'TR-S-01',
            'city' => 'طرابلس',
            'address' => 'شارع الجرابة',
            'status' => 'active',
        ]);

        $consultant = Consultant::create([
            'user_id' => User::factory()->create()->id,
            'full_name' => 'أحمد السالم',
            'employee_number' => 'EMP-1001',
            'national_id' => '119900000001',
            'phone' => '0910000001',
            'operational_status' => 'active',
        ]);

        $payload = [
            'title' => 'مهمة تفتيش جودة تخصصية',
            'task_type' => 'on_demand',
            'is_active' => true,
            'components' => [
                [
                    'label' => 'ملاحظات المفتش',
                    'component_type' => 'text',
                    'is_required' => false,
                ],
            ],
            'site_ids' => [$site->id],
            'consultant_ids' => [$consultant->id],
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.tasks.store'), $payload);

        $response->assertRedirect();

        $task = TaskDefinition::where('title', 'مهمة تفتيش جودة تخصصية')->first();
        $this->assertNotNull($task);

        $this->assertDatabaseHas('task_site_assignments', [
            'task_definition_id' => $task->id,
            'site_id' => $site->id,
        ]);

        $this->assertDatabaseHas('task_consultant_assignments', [
            'task_definition_id' => $task->id,
            'consultant_id' => $consultant->id,
        ]);
    }

    public function test_cannot_hard_delete_task_with_existing_responses(): void
    {
        $site = Site::create([
            'name' => 'موقع بنغازي 1',
            'code' => 'BEN-S-01',
            'city' => 'بنغازي',
            'address' => 'شارع دبي',
            'status' => 'active',
        ]);

        $consultant = Consultant::create([
            'user_id' => User::factory()->create()->id,
            'full_name' => 'محمود الفيتوري',
            'employee_number' => 'EMP-1002',
            'phone' => '0910000002',
            'operational_status' => 'active',
        ]);

        $dailyRecord = DailyRecord::create([
            'consultant_id' => $consultant->id,
            'work_date' => now()->toDateString(),
        ]);

        $siteVisit = SiteVisit::create([
            'daily_record_id' => $dailyRecord->id,
            'site_id' => $site->id,
            'visit_started_at' => now(),
        ]);

        $task = TaskDefinition::factory()->create([
            'title' => 'مهمة فحص ذات سجلات سابقة',
            'task_type' => 'daily',
            'is_active' => true,
        ]);

        // Simulate an existing response attached to siteVisit
        $task->responses()->create([
            'site_visit_id' => $siteVisit->id,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.tasks.destroy', $task->id));

        $response->assertRedirect();

        $this->assertSoftDeleted('task_definitions', [
            'id' => $task->id,
        ]);

        $this->assertDatabaseHas('task_definitions', [
            'id' => $task->id,
            'is_active' => false,
        ]);
    }

    public function test_can_toggle_task_active_status(): void
    {
        $task = TaskDefinition::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.tasks.toggle-active', $task->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('task_definitions', [
            'id' => $task->id,
            'is_active' => false,
        ]);
    }
}
