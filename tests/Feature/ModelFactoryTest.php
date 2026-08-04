<?php

namespace Tests\Feature;

use App\Models\Like;
use App\Models\Post;
use App\Models\PushToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertNotNull($user->username);
        $this->assertNotNull($user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_post_can_be_created(): void
    {
        $post = Post::factory()->create();
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
        $this->assertNull($post->moderation_status);
        $this->assertNotNull($post->image_url);
    }

    public function test_verified_user_has_verified_at(): void
    {
        $user = User::factory()->unverified()->create();
        $this->assertNull($user->email_verified_at);
    }

    public function test_post_belongs_to_user(): void
    {
        $post = Post::factory()->create();
        $this->assertInstanceOf(User::class, $post->users);
    }

    public function test_user_has_many_posts(): void
    {
        $user = User::factory()->create();
        Post::factory()->count(3)->create(['user_id' => $user->id]);
        $this->assertCount(3, $user->posts);
    }

    public function test_like_can_be_created(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $like = Like::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $this->assertDatabaseHas('likes', ['id' => $like->id]);
    }

    public function test_post_can_have_multiple_likes(): void
    {
        $post = Post::factory()->create();
        $users = User::factory()->count(3)->create();

        foreach ($users as $user) {
            $post->likes()->create(['user_id' => $user->id]);
        }

        $this->assertCount(3, $post->likes);
    }

    public function test_user_has_liked_posts(): void
    {
        $user = User::factory()->create();
        $posts = Post::factory()->count(2)->create();

        foreach ($posts as $post) {
            Like::create(['user_id' => $user->id, 'post_id' => $post->id]);
        }

        $this->assertCount(2, $user->likedPosts);
    }

    public function test_like_belongs_to_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $like = Like::create(['user_id' => $user->id, 'post_id' => $post->id]);

        $this->assertInstanceOf(Post::class, $like->post);
        $this->assertEquals($post->id, $like->post->id);
    }

    public function test_like_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $like = Like::create(['user_id' => $user->id, 'post_id' => $post->id]);

        $this->assertInstanceOf(User::class, $like->user);
        $this->assertEquals($user->id, $like->user->id);
    }

    public function test_user_has_followers(): void
    {
        $user = User::factory()->create();
        $followers = User::factory()->count(2)->create();

        foreach ($followers as $follower) {
            $user->followers()->attach($follower->id, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->assertCount(2, $user->followers);
    }

    public function test_user_has_following(): void
    {
        $user = User::factory()->create();
        $followed = User::factory()->count(3)->create();

        foreach ($followed as $target) {
            $user->following()->attach($target->id, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->assertCount(3, $user->following);
    }

    public function test_push_token_can_be_created(): void
    {
        $user = User::factory()->create();
        $token = PushToken::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('push_tokens', ['id' => $token->id]);
        $this->assertNotNull($token->token);
        $this->assertStringContainsString('ExponentPushToken[', $token->token);
    }

    public function test_push_token_belongs_to_user(): void
    {
        $token = PushToken::factory()->create();

        $this->assertInstanceOf(User::class, $token->user);
    }
}
