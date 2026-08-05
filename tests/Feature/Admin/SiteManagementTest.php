<?php

namespace Tests\Feature\Admin;

use App\Enums\SiteStatus;
use App\Models\Site;
use App\Models\SiteVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteManagementTest extends TestCase
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

    public function test_can_render_sites_index_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/sites');

        $response->assertStatus(200);
    }

    public function test_can_create_site_with_unique_code_br020(): void
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/sites', [
            'code' => 'SITE-TRIPOLI-01',
            'name' => 'مجمع الخدمات النقل البحري',
            'city' => 'طرابلس',
            'address' => 'طريق الشط',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('sites', [
            'code' => 'SITE-TRIPOLI-01',
            'name' => 'مجمع الخدمات النقل البحري',
        ]);

        $response->assertRedirect('/admin/sites');
    }

    public function test_cannot_create_site_with_duplicate_code_br020(): void
    {
        Site::factory()->create(['code' => 'DUP-CODE']);

        $response = $this->actingAs($this->adminUser)->post('/admin/sites', [
            'code' => 'DUP-CODE',
            'name' => 'موقع مكرر',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_can_update_site_details(): void
    {
        $site = Site::factory()->create(['code' => 'OLD-CODE']);

        $response = $this->actingAs($this->adminUser)->put("/admin/sites/{$site->id}", [
            'code' => 'NEW-CODE',
            'name' => 'اسم معدل للموقع',
            'city' => 'بنغازي',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'code' => 'NEW-CODE',
            'name' => 'اسم معدل للموقع',
        ]);

        $response->assertRedirect('/admin/sites');
    }

    public function test_can_toggle_site_status_br021(): void
    {
        $site = Site::factory()->create(['status' => SiteStatus::ACTIVE]);

        $response = $this->actingAs($this->adminUser)->patch("/admin/sites/{$site->id}/toggle-status");

        $this->assertEquals(SiteStatus::INACTIVE, $site->fresh()->status);
        $response->assertRedirect('/admin/sites');
    }

    public function test_cannot_delete_site_with_pending_visits_br022(): void
    {
        $site = Site::factory()->create();
        
        // Create pending visit
        SiteVisit::create([
            'site_id' => $site->id,
            'consultant_id' => 1,
            'visit_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->adminUser)->delete("/admin/sites/{$site->id}");

        $this->assertDatabaseHas('sites', ['id' => $site->id]);
        $response->assertSessionHasErrors('site');
    }

    public function test_can_soft_delete_site_without_pending_visits(): void
    {
        $site = Site::factory()->create();

        $response = $this->actingAs($this->adminUser)->delete("/admin/sites/{$site->id}");

        $this->assertSoftDeleted('sites', ['id' => $site->id]);
        $response->assertRedirect('/admin/sites');
    }
}
