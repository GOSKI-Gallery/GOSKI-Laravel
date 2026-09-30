<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
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
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/posts/post-1/like', [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson([
            'success' => true,
            'liked' => true,
        ]);
    }

    public function test_toggle_like_when_not_liked_yet_regular()
    {
        Http::fake([
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/posts/post-1/like');

        $response->assertSessionHas('success');
    }

    public function test_toggle_like_when_already_liked_ajax()
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/likes*" => function ($request) {
                if ($request->method() === 'DELETE') {
                    return Http::response([['id' => 1, 'user_id' => 'user-1', 'post_id' => 'post-1']], 200);
                }

                return Http::response([['id' => 1]], 200);
            },
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/posts/post-1/like', [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson([
            'success' => true,
            'liked' => false,
        ]);
    }

    public function test_toggle_like_when_already_liked_regular()
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/likes*" => function ($request) {
                if ($request->method() === 'DELETE') {
                    return Http::response([['id' => 1, 'user_id' => 'user-1', 'post_id' => 'post-1']], 200);
                }

                return Http::response([['id' => 1]], 200);
            },
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/posts/post-1/like');

        $response->assertSessionHas('success');
    }

    public function test_toggle_like_unlike_path_uses_only_two_http_requests()
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/likes*" => function ($request) {
                if ($request->method() === 'DELETE') {
                    return Http::response([['id' => 1, 'user_id' => 'user-1', 'post_id' => 'post-1']], 200);
                }

                return Http::response([], 200, ['Content-Range' => '0-1/2']);
            },
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/posts/post-1/like', [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson([
            'success' => true,
            'liked' => false,
            'likes_count' => 2,
        ]);
        Http::assertSentCount(2);
    }

    public function test_toggle_like_like_path_returns_true_with_fresh_count()
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/likes*" => function ($request) {
                if ($request->method() === 'DELETE') {
                    return Http::response([], 200);
                }

                if ($request->method() === 'POST') {
                    return Http::response([['id' => 9, 'user_id' => 'user-1', 'post_id' => 'post-1']], 201);
                }

                return Http::response([], 200, ['Content-Range' => '0-4/5']);
            },
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/posts/post-1/like', [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson([
            'success' => true,
            'liked' => true,
            'likes_count' => 5,
        ]);
        // Like path needs the fallback POST after the DELETE no-op, plus the
        // fresh count: 3 requests (same as baseline, no regression). Unlike
        // path above is the one optimized down to 2.
        Http::assertSentCount(3);
    }

    public function test_toggle_like_clears_user_tag_cache()
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/likes*" => function ($request) {
                if ($request->method() === 'DELETE') {
                    return Http::response([], 200);
                }

                if ($request->method() === 'POST') {
                    return Http::response([['id' => 9]], 201);
                }

                return Http::response([], 200, ['Content-Range' => '0-0/1']);
            },
        ]);

        $user = User::factory()->create();
        Cache::put("user:{$user->id}:liked_tag_ids", collect([1, 2]), 3600);

        $this->actingAs($user)
            ->post('/posts/post-1/like', [], ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();

        $this->assertTrue(Cache::missing("user:{$user->id}:liked_tag_ids"));
    }
}
