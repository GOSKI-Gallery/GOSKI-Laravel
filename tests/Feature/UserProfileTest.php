<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_profile(): void
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);
    }

    public function test_profile_update_requires_username(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_profile_update_requires_valid_email(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'username' => 'newusername',
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_profile_update_requires_confirmed_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'username' => 'newusername',
            'email' => 'test@example.com',
            'password' => 'newpassword',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
