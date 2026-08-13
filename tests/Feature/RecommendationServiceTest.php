<?php

namespace Tests\Feature;

use App\Models\Like;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    private RecommendationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new RecommendationService;
    }

    public function test_ranked_feed_ranks_post_from_followed_author_first(): void
    {
        $user = User::factory()->create();
        $followedAuthor = User::factory()->create();
        $otherAuthor = User::factory()->create();

        $user->following()->attach($followedAuthor->id);

        $followedPost = Post::factory()->create([
            'user_id' => $followedAuthor->id,
            'created_at' => now()->subMinutes(5),
        ]);
        $otherPost = Post::factory()->create([
            'user_id' => $otherAuthor->id,
            'created_at' => now(),
        ]);

        $feed = $this->service->getRankedFeed($user, 10);

        $this->assertTrue(
            $feed->pluck('id')->search($followedPost->id) < $feed->pluck('id')->search($otherPost->id)
        );
    }

    public function test_ranked_feed_ranks_post_with_liked_tag_first(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $tag = Tag::factory()->create();

        $likedPost = Post::factory()->create(['user_id' => $author->id]);
        $likedPost->tags()->attach($tag->id, ['confidence' => 0.9]);
        Like::factory()->create(['user_id' => $user->id, 'post_id' => $likedPost->id]);

        $taggedPost = Post::factory()->create([
            'user_id' => $author->id,
            'created_at' => now()->subMinutes(5),
        ]);
        $taggedPost->tags()->attach($tag->id, ['confidence' => 0.9]);

        $plainPost = Post::factory()->create([
            'user_id' => $author->id,
            'created_at' => now(),
        ]);

        $feed = $this->service->getRankedFeed($user, 10);

        $this->assertTrue(
            $feed->pluck('id')->search($taggedPost->id) < $feed->pluck('id')->search($plainPost->id)
        );
    }

    public function test_get_suggested_users_suggests_and_excludes_already_following(): void
    {
        $user = User::factory()->create();
        $likedAuthor = User::factory()->create();
        $alreadyFollowing = User::factory()->create();

        $user->following()->attach($alreadyFollowing->id);

        $likedPost = Post::factory()->create(['user_id' => $likedAuthor->id]);
        Like::factory()->create(['user_id' => $user->id, 'post_id' => $likedPost->id]);

        $suggested = $this->service->getSuggestedUsers($user, 10);

        $this->assertTrue($suggested->pluck('id')->contains($likedAuthor->id));
        $this->assertFalse($suggested->pluck('id')->contains($alreadyFollowing->id));
    }
}
