<?php

namespace Tests\Feature;

use App\Services\SupabasePostService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SupabasePostServiceTest extends TestCase
{
    private SupabasePostService $service;

    private string $baseUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->baseUrl = 'https://post-svc-test.goski.local';
        config(['supabase.url' => $this->baseUrl]);
        config(['supabase.service_role_key' => 'test-svc-key']);
        config(['supabase.anon_key' => 'test-anon-key']);

        $this->service = app(SupabasePostService::class);
    }

    public function test_get_posts_returns_array(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/posts*" => Http::response([
                ['id' => 1, 'description' => 'Post 1', 'users' => ['id' => 'user-1']],
                ['id' => 2, 'description' => 'Post 2', 'users' => ['id' => 'user-2']],
            ], 200),
        ]);

        $posts = $this->service->getPosts();

        $this->assertCount(2, $posts);
        $this->assertEquals('Post 1', $posts[0]['description']);
    }

    public function test_get_posts_returns_empty_array_when_null(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/posts*" => Http::response(null, 200),
        ]);

        $posts = $this->service->getPosts();

        $this->assertEquals([], $posts);
    }

    public function test_get_posts_handles_jwt_expired(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/posts*" => Http::response(['message' => 'JWT expired'], 401),
        ]);

        $posts = $this->service->getPosts();

        $this->assertEquals([], $posts);
    }

    public function test_insert_sends_correct_data(): void
    {
        Http::fake(["{$this->baseUrl}/*" => Http::response([], 200)]);

        $this->service->insert('posts', [
            'user_id' => 'user-1',
            'description' => 'New post',
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/rest/v1/posts')
                && $request->method() === 'POST'
                && $request['user_id'] === 'user-1'
                && $request['description'] === 'New post';
        });
    }

    public function test_like_post_sends_correct_payload(): void
    {
        Http::fake(["{$this->baseUrl}/*" => Http::response([], 200)]);

        $this->service->likePost('user-1', 'post-1');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/rest/v1/likes')
                && $request->method() === 'POST'
                && $request['user_id'] === 'user-1'
                && $request['post_id'] === 'post-1';
        });
    }

    public function test_unlike_post_sends_correct_request(): void
    {
        Http::fake(["{$this->baseUrl}/*" => Http::response([], 200)]);

        $this->service->unlikePost('user-1', 'post-1');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/rest/v1/likes')
                && $request->method() === 'DELETE'
                && str_contains($request->url(), 'user_id=eq.user-1')
                && str_contains($request->url(), 'post_id=eq.post-1');
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

    public function test_get_like_count_returns_count(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/likes*" => Http::response([
                ['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4],
            ], 200),
        ]);

        $count = $this->service->getLikeCount('post-1');

        $this->assertEquals(4, $count);
    }

    public function test_get_like_count_returns_zero(): void
    {
        Http::fake([
            "{$this->baseUrl}/rest/v1/likes*" => Http::response([], 200),
        ]);

        $count = $this->service->getLikeCount('post-1');

        $this->assertEquals(0, $count);
    }

    public function test_get_public_url_returns_correct_string(): void
    {
        $url = $this->service->getPublicUrl('posts', 'post_123.jpg');

        $this->assertEquals(
            "{$this->baseUrl}/storage/v1/object/public/posts/post_123.jpg",
            $url
        );
    }

    public function test_upload_image_sends_request(): void
    {
        Http::fake(["{$this->baseUrl}/*" => Http::response([], 200)]);

        $file = UploadedFile::fake()->create('photo.jpg', 0, 'image/jpeg');

        $this->service->uploadImage('posts', 'post_123.jpg', $file);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/storage/v1/object/posts/post_123.jpg')
                && $request->method() === 'POST';
        });
    }
}
