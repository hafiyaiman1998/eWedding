<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'type' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_user_login_redirects_to_user_dashboard(): void
    {
        $user = User::factory()->create([
            'type' => 'user',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/user/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_redirect_back_with_errors(): void
    {
        $user = User::factory()->create([
            'type' => 'user',
            'password' => bcrypt('password'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout_redirects_to_login_and_clears_auth(): void
    {
        $user = User::factory()->create(['type' => 'user']);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_guest_is_redirected_from_protected_user_route(): void
    {
        $response = $this->get('/user/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_guest_is_redirected_from_protected_admin_route(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_dashboard_redirect_route_routes_by_type(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $this->actingAs($admin)->get('/dashboard')->assertRedirect('/admin/dashboard');

        $user = User::factory()->create(['type' => 'user']);
        $this->actingAs($user)->get('/dashboard')->assertRedirect('/user/dashboard');
    }
}
