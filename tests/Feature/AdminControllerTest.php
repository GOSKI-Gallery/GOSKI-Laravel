<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_dashboard_shows_counts(): void
    {
        Post::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertViewHas('totalUsers');
        $response->assertViewHas('totalPosts', 2);
    }

    public function test_admin_dashboard_shows_pending_posts(): void
    {
        Post::factory()->create(['moderation_status' => 'POSSIBLE']);
        Post::factory()->create(['moderation_status' => 'approved']);

        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertViewHas('pendingPosts');
        $this->assertCount(1, $response->viewData('pendingPosts'));
    }

    public function test_admin_users_index(): void
    {
        User::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get('/admin/users');
        $response->assertStatus(200);
    }

    public function test_admin_user_detail(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->get("/admin/users/{$user->id}");
        $response->assertStatus(200);
        $response->assertViewHas('user');
    }

    public function test_admin_user_remove_view(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->get("/admin/users/{$user->id}/remove");
        $response->assertStatus(200);
    }

    public function test_admin_posts_index(): void
    {
        Post::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get('/admin/posts');
        $response->assertStatus(200);
    }

    public function test_admin_post_detail(): void
    {
        $post = Post::factory()->create();

        $response = $this->actingAs($this->admin)->get("/admin/posts/{$post->id}");
        $response->assertStatus(200);
    }
}
