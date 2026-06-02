<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_non_admin_is_redirected_from_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->get('/admin');
        $response->assertRedirect('/');
    }

    public function test_admin_can_access_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_admin_can_see_users_index(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        User::factory()->count(5)->create();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertStatus(200);
    }

    public function test_admin_can_approve_post(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $post = Post::factory()->create(['moderation_status' => 'POSSIBLE']);

        $response = $this->actingAs($admin)->post("/admin/posts/{$post->id}/approve");
        $response->assertRedirect(route('admin.dashboard'));

        $this->assertEquals('approved', $post->fresh()->moderation_status);
    }

    public function test_admin_can_delete_post(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $post = Post::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/posts/{$post->id}");
        $response->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_non_admin_cannot_approve_post(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $post = Post::factory()->create(['moderation_status' => 'POSSIBLE']);

        $response = $this->actingAs($user)->post("/admin/posts/{$post->id}/approve");
        $response->assertRedirect('/');
    }
}
