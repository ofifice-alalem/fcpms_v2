<?php

namespace Tests\Feature\Auth;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_email(): void
    {
        $user = User::factory()->create([
            'email' => 'consultant@example.com',
            'username' => 'consultant1',
            'password' => bcrypt('password123'),
            'status' => UserStatus::ACTIVE,
        ]);

        $response = $this->post('/login', [
            'login' => 'consultant@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_users_can_authenticate_using_username(): void
    {
        $user = User::factory()->create([
            'email' => 'consultant2@example.com',
            'username' => 'consultant2',
            'password' => bcrypt('password123'),
            'status' => UserStatus::ACTIVE,
        ]);

        $response = $this->post('/login', [
            'login' => 'consultant2',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'status' => UserStatus::ACTIVE,
        ]);

        $this->post('/login', [
            'login' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login_enforces_br002(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'username' => 'inactive_user',
            'password' => bcrypt('password123'),
            'status' => UserStatus::INACTIVE,
        ]);

        $response = $this->post('/login', [
            'login' => 'inactive_user',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    public function test_last_login_timestamp_is_updated_on_successful_login_br002(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'status' => UserStatus::ACTIVE,
            'last_login_at' => null,
        ]);

        $this->post('/login', [
            'login' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create([
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
