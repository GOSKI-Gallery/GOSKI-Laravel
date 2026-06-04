<?php

namespace Tests\Feature;

use App\Services\SupabaseUserService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SupabaseUserServiceTest extends TestCase
{
    private SupabaseUserService $service;

    private string $baseUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->baseUrl = 'https://user-svc-test.goski.local';
        config(['supabase.url' => $this->baseUrl]);
        config(['supabase.service_role_key' => 'test-svc-key']);
        config(['supabase.anon_key' => 'test-anon-key']);

        $this->service = app(SupabaseUserService::class);
    }

    public function test_get_user_by_id_returns_user(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/users*" => Http::response([
                ['id' => 'user-1', 'username' => 'testuser'],
            ], 200),
        ]);

        $user = $this->service->getUserById('user-1');

        $this->assertNotNull($user);
        $this->assertEquals('user-1', $user['id']);
    }

    public function test_get_user_by_id_returns_null_when_not_found(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/users*" => Http::response([], 200),
        ]);

        $user = $this->service->getUserById('nonexistent');

        $this->assertNull($user);
    }

    public function test_follow_user_sends_correct_payload(): void
    {
        Http::fake(["{$this->baseUrl}/*" => Http::response([], 200)]);

        $this->service->followUser('follower-1', 'followed-1');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/rest/v1/follows')
                && $request->method() === 'POST'
                && $request['follower_id'] === 'follower-1'
                && $request['followed_id'] === 'followed-1';
        });
    }

    public function test_unfollow_user_sends_correct_request(): void
    {
        Http::fake(["{$this->baseUrl}/*" => Http::response([], 200)]);

        $this->service->unfollowUser('follower-1', 'followed-1');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/rest/v1/follows')
                && $request->method() === 'DELETE'
                && str_contains($request->url(), 'follower_id=eq.follower-1')
                && str_contains($request->url(), 'followed_id=eq.followed-1');
        });
    }

    public function test_is_following_returns_true(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/follows*" => Http::response([
                ['id' => 1, 'follower_id' => 'follower-1', 'followed_id' => 'followed-1'],
            ], 200),
        ]);

        $result = $this->service->isFollowing('follower-1', 'followed-1');

        $this->assertTrue($result);
    }

    public function test_is_following_returns_false(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/follows*" => Http::response([], 200),
        ]);

        $result = $this->service->isFollowing('follower-1', 'followed-1');

        $this->assertFalse($result);
    }

    public function test_get_follow_count_returns_count(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/follows*" => Http::response([
                ['id' => 1], ['id' => 2], ['id' => 3],
            ], 200),
        ]);

        $count = $this->service->getFollowCount('user-1', 'followers');

        $this->assertEquals(3, $count);
    }

    public function test_get_follow_count_returns_zero(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/follows*" => Http::response([], 200),
        ]);

        $count = $this->service->getFollowCount('user-1', 'following');

        $this->assertEquals(0, $count);
    }

    public function test_get_follow_count_uses_followed_id_for_followers(): void
    {
        Http::fake(["{$this->baseUrl}/*" => Http::response([], 200)]);

        $this->service->getFollowCount('user-1', 'followers');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'followed_id=eq.user-1');
        });
    }

    public function test_get_follow_count_uses_follower_id_for_following(): void
    {
        Http::fake(["{$this->baseUrl}/*" => Http::response([], 200)]);

        $this->service->getFollowCount('user-1', 'following');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'follower_id=eq.user-1');
        });
    }

    public function test_has_liked_post_returns_true(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/likes*" => Http::response([
                ['id' => 1, 'user_id' => 'user-1', 'post_id' => 'post-1'],
            ], 200),
        ]);

        $result = $this->service->hasLikedPost('user-1', 'post-1');

        $this->assertTrue($result);
    }

    public function test_has_liked_post_returns_false(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/likes*" => Http::response([], 200),
        ]);

        $result = $this->service->hasLikedPost('user-1', 'post-1');

        $this->assertFalse($result);
    }
}
