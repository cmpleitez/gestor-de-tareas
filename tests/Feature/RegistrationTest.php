<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_from_registration_screen(): void
    {
        $response = $this->get('/register');

        $response->assertRedirect('/login'); // Middleware auth: el registro no es público
    }

    public function test_non_admin_users_can_not_access_registration_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/register');

        $response->assertForbidden(); // Middleware role:admin
    }

    public function test_admin_users_can_access_registration_screen(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin', 'web'));

        $response = $this->actingAs($admin)->get('/register');

        $response->assertStatus(200);
    }
}
