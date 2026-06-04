<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LikeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggle_like_requires_authentication()
    {
        $response = $this->post('/posts/some-post/like');

        $response->assertRedirect('/login');
    }

    public function test_toggle_like_when_not_liked_yet_ajax()
    {
        Http::fake([
            'https://fuckjwtltqvngejerkzo.supabase.co/*' => Http::response([], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/posts/post-1/like', [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson([
            'success' => true,
            'message' => 'Liked successfully!',
            'liked' => true,
        ]);
    }

    public function test_toggle_like_when_not_liked_yet_regular()
    {
        Http::fake([
            'https://fuckjwtltqvngejerkzo.supabase.co/*' => Http::response([], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/posts/post-1/like');

        $response->assertSessionHas('success', 'Liked successfully!');
    }

    public function test_toggle_like_when_already_liked_ajax()
    {
        Http::fake([
            'https://fuckjwtltqvngejerkzo.supabase.co/rest/v1/likes*' => Http::response([['id' => 1]], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/posts/post-1/like', [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson([
            'success' => true,
            'message' => 'Unliked successfully!',
            'liked' => false,
        ]);
    }

    public function test_toggle_like_when_already_liked_regular()
    {
        Http::fake([
            'https://fuckjwtltqvngejerkzo.supabase.co/rest/v1/likes*' => Http::response([['id' => 1]], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/posts/post-1/like');

        $response->assertSessionHas('success', 'Unliked successfully!');
    }
}
