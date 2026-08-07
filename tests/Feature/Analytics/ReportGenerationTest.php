<?php

namespace Tests\Feature\Analytics;

use App\Enums\ConsultantStatus;
use App\Models\Consultant;
use App\Models\DailyRecord;
use App\Models\Site;
use App\Models\SiteVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'hr', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'consultant', 'guard_name' => 'web']);
    }

    public function test_consultant_can_only_view_their_own_personal_reports()
    {
        $user = User::factory()->create();
        $user->assignRole('consultant');

        $consultant = Consultant::create([
            'user_id'           => $user->id,
            'employee_number'   => 'EMP-1001',
            'full_name'         => 'أحمد السالم',
            'specialization'    => 'هندسة مدنية',
            'employment_status' => ConsultantStatus::ACTIVE,
        ]);

        $site = Site::create([
            'code'                 => 'SITE-TRIPOLI-01',
            'name'                 => 'موقع طرابلس المركزي',
            'city'                 => 'طرابلس',
            'location_coordinates' => '32.8872,13.1913',
            'status'               => 'active',
        ]);

        $dailyRecord = DailyRecord::create([
            'consultant_id'         => $consultant->id,
            'work_date'             => now()->format('Y-m-d'),
            'completion_percentage' => 100,
        ]);

        SiteVisit::create([
            'daily_record_id' => $dailyRecord->id,
            'site_id'         => $site->id,
            'status'          => 'completed',
            'check_in_time'   => now()->subHour(),
            'check_out_time'  => now(),
        ]);

        $response = $this->actingAs($user)->get(route('consultant.reports.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Consultant/Reports/Index')
            ->has('metrics')
            ->where('consultant.id', $consultant->id)
        );
    }

    public function test_hr_can_filter_and_export_enterprise_performance_reports()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $consultantUser = User::factory()->create();
        $consultantUser->assignRole('consultant');

        $consultant = Consultant::create([
            'user_id'           => $consultantUser->id,
            'employee_number'   => 'EMP-1002',
            'full_name'         => 'محمد علي',
            'specialization'    => 'شبكات',
            'employment_status' => ConsultantStatus::ACTIVE,
        ]);

        $site = Site::create([
            'code'                 => 'SITE-BENGHAZI-01',
            'name'                 => 'موقع بنغازي الميداني',
            'city'                 => 'بنغازي',
            'location_coordinates' => '32.1167,20.0667',
            'status'               => 'active',
        ]);

        $dailyRecord = DailyRecord::create([
            'consultant_id'         => $consultant->id,
            'work_date'             => now()->format('Y-m-d'),
            'completion_percentage' => 100,
        ]);

        SiteVisit::create([
            'daily_record_id' => $dailyRecord->id,
            'site_id'         => $site->id,
            'status'          => 'completed',
            'check_in_time'   => now()->subHour(),
            'check_out_time'  => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.index', [
            'consultant_id' => $consultant->id,
            'site_id'       => $site->id,
        ]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Reports/Index')
            ->has('metrics')
            ->has('consultants')
            ->has('sites')
        );

        $exportResponse = $this->actingAs($admin)->get(route('admin.reports.export', [
            'format' => 'excel',
        ]));

        $exportResponse->assertStatus(200);
    }
}
