<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_requires_guest()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/register', [
            'username' => 'newuser',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/feed');
    }

    public function test_register_success()
    {
        Http::fake([
            "{$this->supabaseUrl}/auth/v1/signup*" => Http::response(['id' => 'new-user'], 200),
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $response = $this->post('/register', [
            'username' => 'newuser',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('landingPage'));
        $response->assertSessionHas('success', 'Conta criada!');
    }

    public function test_register_with_error()
    {
        Http::fake([
            "{$this->supabaseUrl}/auth/v1/signup*" => Http::response(
                ['error_description' => 'User already exists'],
                400
            ),
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $response = $this->post('/register', [
            'username' => 'newuser',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('supabase');
    }

    public function test_profile_requires_authentication()
    {
        $response = $this->get('/profile');

        $response->assertRedirect('/login');
    }

    public function test_profile_returns_view()
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/follows*" => Http::response([], 200),
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertViewIs('profile');
    }

    public function test_profile_shows_user_posts()
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/follows*" => Http::response([], 200),
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();
        Post::factory()->count(3)->create(['user_id' => $user->id]);
        Post::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertViewHas('userPosts');
        $this->assertCount(3, $response->viewData('userPosts'));
    }

    public function test_show_requires_authentication()
    {
        $response = $this->get('/profile/some-id');

        $response->assertRedirect('/login');
    }

    public function test_show_user_profile()
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/users?id=eq.displayed-user*" => Http::response(
                [['id' => 'displayed-user', 'username' => 'displayed']],
                200
            ),
            "{$this->supabaseUrl}/rest/v1/follows*" => Http::response([], 200),
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile/displayed-user');

        $response->assertStatus(200);
        $response->assertViewIs('profile');
    }

    public function test_show_nonexistent_user_returns_404()
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/users?id=eq.nonexistent*" => Http::response([], 200),
            "{$this->supabaseUrl}/rest/v1/follows*" => Http::response([], 200),
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile/nonexistent');

        $response->assertStatus(404);
    }

    public function test_update_requires_authentication()
    {
        $response = $this->put('/profile', [
            'username' => 'updated',
            'email' => 'updated@example.com',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_update_profile_success()
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/users*" => Http::response([], 200),
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'username' => 'updateduser',
            'email' => 'updated@example.com',
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('success', 'Profile updated successfully!');
    }

    public function test_update_profile_with_error()
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/users*" => Http::response(
                ['error' => ['message' => 'Database error']],
                400
            ),
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'username' => 'updateduser',
            'email' => 'updated@example.com',
        ]);

        $response->assertSessionHasErrors('supabase');
    }
}
