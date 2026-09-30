<?php

namespace Tests\Unit;

use App\Services\SupabaseCommentService;
use App\Services\SupabasePostService;
use App\Services\SupabaseUserService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SupabaseCountExactTest extends TestCase
{
    private SupabasePostService $postService;

    private SupabaseCommentService $commentService;

    private SupabaseUserService $userService;

    private string $baseUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->baseUrl = 'https://count-exact-test.goski.local';
        config(['supabase.url' => $this->baseUrl]);
        config(['supabase.service_role_key' => 'test-svc-key']);
        config(['supabase.anon_key' => 'test-anon-key']);

        $this->postService = app(SupabasePostService::class);
        $this->commentService = app(SupabaseCommentService::class);
        $this->userService = app(SupabaseUserService::class);
    }

    public function test_post_like_count_returns_total_from_content_range(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/likes*" => Http::response([], 200, ['Content-Range' => '0-0/42']),
        ]);

        $count = $this->postService->getLikeCount('post-1');

        $this->assertSame(42, $count);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/rest/v1/likes')
                && str_contains($request->url(), 'post_id=eq.post-1')
                && $request->hasHeader('Prefer', 'count=exact')
                && $request->hasHeader('Accept-Profile', 'laravel')
                && $request->hasHeader('Content-Profile', 'laravel');
        });
    }

    public function test_post_comment_count_returns_total_from_content_range(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([], 200, ['Content-Range' => '0-0/7']),
        ]);

        $count = $this->postService->getCommentCount('post-1');

        $this->assertSame(7, $count);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/rest/v1/comments')
                && str_contains($request->url(), 'post_id=eq.post-1')
                && $request->hasHeader('Prefer', 'count=exact')
                && $request->hasHeader('Accept-Profile', 'laravel')
                && $request->hasHeader('Content-Profile', 'laravel');
        });
    }

    public function test_comment_service_count_returns_total_from_content_range(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([], 200, ['Content-Range' => '0-0/15']),
        ]);

        $count = $this->commentService->getCommentCount('post-1');

        $this->assertSame(15, $count);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/rest/v1/comments')
                && $request->hasHeader('Prefer', 'count=exact')
                && $request->hasHeader('Accept-Profile', 'laravel')
                && $request->hasHeader('Content-Profile', 'laravel');
        });
    }

    public function test_user_follow_count_followers_returns_total_from_content_range(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/follows*" => Http::response([], 200, ['Content-Range' => '0-0/9']),
        ]);

        $count = $this->userService->getFollowCount('user-1', 'followers');

        $this->assertSame(9, $count);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/rest/v1/follows')
                && str_contains($request->url(), 'followed_id=eq.user-1')
                && $request->hasHeader('Prefer', 'count=exact')
                && $request->hasHeader('Accept-Profile', 'laravel')
                && $request->hasHeader('Content-Profile', 'laravel');
        });
    }

    public function test_user_follow_count_following_uses_follower_column_with_count_exact(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/follows*" => Http::response([], 200, ['Content-Range' => '*/3']),
        ]);

        $count = $this->userService->getFollowCount('user-1', 'following');

        $this->assertSame(3, $count);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'follower_id=eq.user-1')
                && $request->hasHeader('Prefer', 'count=exact');
        });
    }

    public function test_post_like_count_returns_zero_without_content_range(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/likes*" => Http::response([['id' => 1], ['id' => 2]], 200),
        ]);

        $count = $this->postService->getLikeCount('post-1');

        $this->assertSame(0, $count);
        Http::assertSent(fn ($request) => $request->hasHeader('Prefer', 'count=exact'));
    }

    public function test_post_comment_count_returns_zero_without_content_range(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([['id' => 1]], 200),
        ]);

        $count = $this->postService->getCommentCount('post-1');

        $this->assertSame(0, $count);
        Http::assertSent(fn ($request) => $request->hasHeader('Prefer', 'count=exact'));
    }

    public function test_comment_service_count_returns_zero_without_content_range(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([['id' => 1], ['id' => 2]], 200),
        ]);

        $count = $this->commentService->getCommentCount('post-1');

        $this->assertSame(0, $count);
        Http::assertSent(fn ($request) => $request->hasHeader('Prefer', 'count=exact'));
    }

    public function test_user_follow_count_returns_zero_without_content_range(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/follows*" => Http::response([['id' => 1]], 200),
        ]);

        $count = $this->userService->getFollowCount('user-1', 'followers');

        $this->assertSame(0, $count);
        Http::assertSent(fn ($request) => $request->hasHeader('Prefer', 'count=exact'));
    }

    public function test_post_like_count_returns_zero_on_server_error(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/likes*" => Http::response([], 500),
        ]);

        $count = $this->postService->getLikeCount('post-1');

        $this->assertSame(0, $count);
        Http::assertSent(fn ($request) => $request->hasHeader('Prefer', 'count=exact'));
    }

    public function test_post_comment_count_returns_zero_on_server_error(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([], 500),
        ]);

        $count = $this->postService->getCommentCount('post-1');

        $this->assertSame(0, $count);
        Http::assertSent(fn ($request) => $request->hasHeader('Prefer', 'count=exact'));
    }

    public function test_comment_service_count_returns_zero_on_server_error(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([], 500),
        ]);

        $count = $this->commentService->getCommentCount('post-1');

        $this->assertSame(0, $count);
        Http::assertSent(fn ($request) => $request->hasHeader('Prefer', 'count=exact'));
    }

    public function test_user_follow_count_returns_zero_on_server_error(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/follows*" => Http::response([], 500),
        ]);

        $count = $this->userService->getFollowCount('user-1', 'followers');

        $this->assertSame(0, $count);
        Http::assertSent(fn ($request) => $request->hasHeader('Prefer', 'count=exact'));
    }
}
