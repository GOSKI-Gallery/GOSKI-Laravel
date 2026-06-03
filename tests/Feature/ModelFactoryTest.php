<?php

namespace Tests\Feature;

use App\Models\Like;
use App\Models\Post;
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
        $this->assertEquals('pending', $post->moderation_status);
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
}
