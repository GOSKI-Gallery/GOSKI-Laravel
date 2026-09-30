<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supabaseUrl = 'https://post-svc-test.goski.local';
        config(['supabase.url' => $this->supabaseUrl]);
        config(['supabase.service_role_key' => 'test-svc-key']);
        config(['supabase.anon_key' => 'test-anon-key']);
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson('/posts/post-1/comments');

        $response->assertUnauthorized();
    }

    public function test_index_returns_comments(): void
    {
        $commentUser = User::factory()->create([
            'id' => '550e8400-e29b-41d4-a716-446655440001',
            'username' => 'commenter',
            'profile_photo_url' => 'https://example.com/photo.jpg',
        ]);

        Http::fake([
            "{$this->supabaseUrl}/rest/v1/comments*" => Http::response([
                ['id' => 1, 'body' => 'Nice!', 'user_id' => $commentUser->id, 'post_id' => 'post-1', 'created_at' => '2026-07-13T10:00:00Z'],
            ], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/posts/post-1/comments');

        $response->assertJson([
            'success' => true,
        ]);

        $response->assertJsonCount(1, 'comments');

        $response->assertJsonPath('comments.0.users.username', 'commenter');
        $response->assertJsonPath('comments.0.users.profile_photo_url', 'https://example.com/photo.jpg');
        $response->assertJsonStructure(['comments' => [['time_ago']]]);
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->postJson('/posts/post-1/comments', [
            'body' => 'Great post!',
        ]);

        $response->assertUnauthorized();
    }

    public function test_store_creates_comment_ajax(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/comments*" => Http::response([
                ['id' => 1, 'user_id' => 'user-1', 'post_id' => 'post-1', 'body' => 'Great post!'],
            ], 201),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/posts/post-1/comments', ['body' => 'Great post!']);

        $response->assertJson([
            'success' => true,
        ]);

        $response->assertJsonPath('comment.body', 'Great post!');
    }

    public function test_store_returns_db_backed_comments_count_with_single_http_call(): void
    {
        $owner = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        // Http::fake() não persiste no sqlite: o count via DB reflete as
        // linhas pré-existentes. Em produção o POST REST commita antes da
        // leitura direta, então o count inclui o comentário novo.
        DB::table('comments')->insert([
            ['user_id' => $owner->id, 'post_id' => $post->id, 'body' => 'First!', 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $owner->id, 'post_id' => $post->id, 'body' => 'Second!', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Http::fake([
            "{$this->supabaseUrl}/rest/v1/comments*" => Http::response([
                ['id' => 99, 'user_id' => $owner->id, 'post_id' => (string) $post->id, 'body' => 'Great post!'],
            ], 201),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/posts/{$post->id}/comments", ['body' => 'Great post!']);

        $expected = DB::table('comments')->where('post_id', $post->id)->count();
        $this->assertSame(2, $expected);

        $response->assertJson([
            'success' => true,
            'comments_count' => $expected,
        ]);
        $response->assertJsonPath('comment.body', 'Great post!');

        Http::assertSentCount(1);
    }

    public function test_store_returns_500_without_count_when_insert_fails(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/comments*" => Http::response([], 500),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/posts/1/comments', ['body' => 'Great post!']);

        $response->assertStatus(500);
        $response->assertJson(['success' => false]);
        $response->assertJsonMissingPath('comments_count');

        Http::assertSentCount(1);
    }

    public function test_store_validates_body(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/posts/post-1/comments', ['body' => '']);

        $response->assertJsonValidationErrors('body');
    }

    public function test_store_creates_comment_regular(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/posts/post-1/comments', ['body' => 'Great post!']);

        $response->assertSessionHas('success');
    }

    public function test_destroy_requires_authentication(): void
    {
        $response = $this->deleteJson('/posts/comments/comment-1');

        $response->assertUnauthorized();
    }

    public function test_destroy_deletes_comment_ajax(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/comments*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson('/posts/comments/comment-1');

        $response->assertJson(['success' => true]);
    }

    public function test_destroy_deletes_comment_regular(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->delete('/posts/comments/comment-1');

        $response->assertSessionHas('success');
    }

    public function test_destroy_returns_error_on_failure(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/rest/v1/comments*" => Http::response([], 500),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson('/posts/comments/comment-1');

        $response->assertStatus(500);
    }
}
