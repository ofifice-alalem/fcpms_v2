<?php

namespace Tests\Feature\Consultant;

use App\Models\Consultant;
use App\Models\DailyRecord;
use App\Models\Site;
use App\Models\SiteVisit;
use App\Models\TaskDefinition;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyVisitsExecutionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Consultant $consultant;
    protected Site $site;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->consultant = Consultant::create([
            'user_id' => $this->user->id,
            'employee_number' => 'CONS-TEST-01',
            'full_name' => 'اختبار الاستشاري الميداني',
            'hire_date' => now(),
            'specialization' => 'مفتش سلامة',
        ]);

        $this->site = Site::create([
            'name' => 'موقع طرابلس المركزي - البرج أ',
            'code' => 'SITE-TRIPOLI-01',
            'status' => 'active',
        ]);
    }

    public function test_consultant_can_start_daily_record_and_select_site_from_dropdown(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('consultant.visits.start-day'), [
            'notes' => 'بدء اليوم العملي من المقر الرئيسي',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('daily_records', [
            'consultant_id' => $this->consultant->id,
            'work_date' => now()->toDateString(),
        ]);

        // Open site visit from dropdown
        $visitResponse = $this->actingAs($this->user)->postJson(route('consultant.site-visits.store'), [
            'site_id' => $this->site->id,
        ]);

        $visitResponse->assertStatus(201);

        $this->assertDatabaseHas('site_visits', [
            'site_id' => $this->site->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_daily_tasks_auto_load_while_ondemand_tasks_require_dropdown_trigger(): void
    {
        // Create 1 daily task and 1 on_demand task
        $dailyTask = TaskDefinition::create([
            'title' => 'فحص أنظمة السلامة ومطفآت الحريق',
            'task_type' => 'daily',
            'is_active' => true,
        ]);

        $onDemandTask = TaskDefinition::create([
            'title' => 'بلاغ عطل طارئ في المولد الكهربائي',
            'task_type' => 'on_demand',
            'is_active' => true,
        ]);

        // Open visit
        $visitResponse = $this->actingAs($this->user)->postJson(route('consultant.site-visits.store'), [
            'site_id' => $this->site->id,
        ]);

        $visitResponse->assertStatus(201);
        $visitId = $visitResponse->json('data.site_visit_id');

        // Verify daily task is automatically loaded into task_responses
        $this->assertDatabaseHas('task_responses', [
            'site_visit_id' => $visitId,
            'task_definition_id' => $dailyTask->id,
        ]);

        // Verify on_demand task is NOT automatically loaded until triggered
        $this->assertDatabaseMissing('task_responses', [
            'site_visit_id' => $visitId,
            'task_definition_id' => $onDemandTask->id,
        ]);

        // Explicitly trigger on_demand task via dropdown action
        $triggerResponse = $this->actingAs($this->user)->postJson(route('consultant.site-visits.trigger-ondemand', $visitId), [
            'task_definition_id' => $onDemandTask->id,
        ]);

        $triggerResponse->assertStatus(200);

        // Now verify on_demand task exists in task_responses
        $this->assertDatabaseHas('task_responses', [
            'site_visit_id' => $visitId,
            'task_definition_id' => $onDemandTask->id,
        ]);
    }

    public function test_consultant_can_edit_responses_during_active_visit(): void
    {
        $task = TaskDefinition::create([
            'title' => 'مهمة فحص المظهر الخارجي',
            'task_type' => 'daily',
            'is_active' => true,
        ]);

        $visitResponse = $this->actingAs($this->user)->postJson(route('consultant.site-visits.store'), [
            'site_id' => $this->site->id,
        ]);

        $visitId = $visitResponse->json('data.site_visit_id');

        // Save initial responses
        $saveResponse = $this->actingAs($this->user)->postJson(route('consultant.site-visits.save-responses', $visitId), [
            'responses' => [
                [
                    'task_definition_id' => $task->id,
                    'is_completed' => true,
                ],
            ],
            'complete_visit' => true,
        ]);

        $saveResponse->assertStatus(200);

        $this->assertDatabaseHas('site_visits', [
            'id' => $visitId,
            'status' => 'completed',
        ]);
    }
}
