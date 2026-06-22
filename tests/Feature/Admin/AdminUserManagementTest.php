<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['type' => 'admin']);
    }

    public function test_admin_can_create_a_client_user(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New Client',
            'email' => 'client@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'client@example.com',
            'type' => 'user',
        ]);
    }

    public function test_admin_can_update_a_client_user(): void
    {
        $admin = $this->admin();
        $client = User::factory()->create(['type' => 'user']);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $client), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $fresh = $client->fresh();
        $this->assertSame('Updated Name', $fresh->name);
        $this->assertSame('updated@example.com', $fresh->email);
    }

    public function test_admin_can_update_client_password(): void
    {
        $admin = $this->admin();
        $client = User::factory()->create(['type' => 'user']);

        $this->actingAs($admin)->put(route('admin.users.update', $client), [
            'name' => $client->name,
            'email' => $client->email,
            'password' => 'brand-new-password',
            'password_confirmation' => 'brand-new-password',
        ]);

        $this->assertTrue(Hash::check('brand-new-password', $client->fresh()->password));
    }

    public function test_admin_can_delete_a_client_user(): void
    {
        $admin = $this->admin();
        $client = User::factory()->create(['type' => 'user']);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $client));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $client->id]);
    }

    public function test_updating_a_non_client_target_aborts_404(): void
    {
        $admin = $this->admin();
        $otherAdmin = User::factory()->create(['type' => 'admin']);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $otherAdmin), [
            'name' => 'Hacked',
            'email' => 'hacked@example.com',
        ]);

        $response->assertNotFound();
        $this->assertNotSame('Hacked', $otherAdmin->fresh()->name);
    }

    public function test_deleting_a_non_client_target_aborts_404(): void
    {
        $admin = $this->admin();
        $otherAdmin = User::factory()->create(['type' => 'admin']);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $otherAdmin));

        $response->assertNotFound();
        $this->assertDatabaseHas('users', ['id' => $otherAdmin->id]);
    }
}
