<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_landing_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_guest_can_access_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_guest_can_access_register_page(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_is_redirected_from_landing(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/');
        $response->assertRedirect('/feed');
    }

    public function test_authenticated_user_is_redirected_from_login(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect('/feed');
    }

    public function test_authenticated_user_is_redirected_from_register(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/register');
        $response->assertRedirect('/feed');
    }

    public function test_guest_is_redirected_from_authenticated_routes(): void
    {
        $response = $this->get('/feed');
        $response->assertRedirect('/login');
    }
}
