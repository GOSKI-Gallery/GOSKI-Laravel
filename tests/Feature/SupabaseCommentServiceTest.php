<?php

namespace Tests\Feature;

use App\Services\SupabaseCommentService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SupabaseCommentServiceTest extends TestCase
{
    private SupabaseCommentService $service;

    private string $baseUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->baseUrl = 'https://post-svc-test.goski.local';
        config(['supabase.url' => $this->baseUrl]);
        config(['supabase.service_role_key' => 'test-svc-key']);
        config(['supabase.anon_key' => 'test-anon-key']);

        $this->service = app(SupabaseCommentService::class);
    }

    public function test_get_comments_returns_array(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([
                ['id' => 1, 'body' => 'Great post!', 'user_id' => 'user-1', 'post_id' => 'post-1'],
                ['id' => 2, 'body' => 'Thanks!', 'user_id' => 'user-2', 'post_id' => 'post-1'],
            ], 200),
        ]);

        $comments = $this->service->getComments('post-1');

        $this->assertCount(2, $comments);
        $this->assertEquals('Great post!', $comments[0]['body']);
    }

    public function test_get_comments_returns_empty_array_when_null(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response(null, 200),
        ]);

        $comments = $this->service->getComments('post-1');

        $this->assertEquals([], $comments);
    }

    public function test_get_comments_handles_failure(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([], 500),
        ]);

        $comments = $this->service->getComments('post-1');

        $this->assertEquals([], $comments);
    }

    public function test_add_comment_sends_correct_payload(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([
                ['id' => 1, 'user_id' => 'user-1', 'post_id' => 'post-1', 'body' => 'Nice!'],
            ], 201),
        ]);

        $result = $this->service->addComment('user-1', 'post-1', 'Nice!');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/rest/v1/comments')
                && $request->method() === 'POST'
                && $request['user_id'] === 'user-1'
                && $request['post_id'] === 'post-1'
                && $request['body'] === 'Nice!';
        });

        $this->assertIsArray($result);
        $this->assertEquals('Nice!', $result['body']);
    }

    public function test_add_comment_returns_null_on_failure(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([], 500),
        ]);

        $result = $this->service->addComment('user-1', 'post-1', 'Nice!');

        $this->assertNull($result);
    }

    public function test_delete_comment_sends_correct_request(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([], 200),
        ]);

        $result = $this->service->deleteComment('comment-1');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/rest/v1/comments')
                && $request->method() === 'DELETE'
                && str_contains($request->url(), 'id=eq.comment-1');
        });

        $this->assertTrue($result);
    }

    public function test_delete_comment_returns_false_on_failure(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([], 500),
        ]);

        $result = $this->service->deleteComment('comment-1');

        $this->assertFalse($result);
    }

    public function test_get_comment_count_returns_count(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([
                ['id' => 1], ['id' => 2], ['id' => 3],
            ], 200),
        ]);

        $count = $this->service->getCommentCount('post-1');

        $this->assertEquals(3, $count);
    }

    public function test_get_comment_count_returns_zero(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([], 200),
        ]);

        $count = $this->service->getCommentCount('post-1');

        $this->assertEquals(0, $count);
    }

    public function test_get_comment_count_returns_zero_on_failure(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/comments*" => Http::response([], 500),
        ]);

        $count = $this->service->getCommentCount('post-1');

        $this->assertEquals(0, $count);
    }
}
