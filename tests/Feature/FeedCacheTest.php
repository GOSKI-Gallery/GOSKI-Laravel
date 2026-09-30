<?php

namespace Tests\Feature;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FeedCacheTest extends TestCase
{
    use RefreshDatabase;

    private RecommendationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        Cache::flush();
        DB::flushQueryLog();

        $this->service = new RecommendationService;
    }

    public function test_suggested_users_second_call_hits_cache(): void
    {
        $user = User::factory()->create();
        $likedAuthor = User::factory()->create();
        $likedPost = Post::factory()->create(['user_id' => $likedAuthor->id]);
        Like::factory()->create(['user_id' => $user->id, 'post_id' => $likedPost->id]);

        DB::enableQueryLog();
        $first = $this->service->getSuggestedUsers($user, 10);
        $firstCount = count(DB::getQueryLog());

        DB::flushQueryLog();
        $second = $this->service->getSuggestedUsers($user, 10);
        $secondCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertTrue($second->pluck('id')->contains($likedAuthor->id));
        $this->assertEquals($first->pluck('id')->all(), $second->pluck('id')->all());
        $this->assertTrue(Cache::has("suggested_users:{$user->id}"));
        $this->assertLessThan($firstCount, $secondCount);
        $this->assertSame(0, $secondCount);
    }

    public function test_clear_suggestion_cache_invalidates(): void
    {
        $user = User::factory()->create();
        $likedAuthor = User::factory()->create();
        $likedPost = Post::factory()->create(['user_id' => $likedAuthor->id]);
        Like::factory()->create(['user_id' => $user->id, 'post_id' => $likedPost->id]);

        $first = $this->service->getSuggestedUsers($user, 10);
        $this->assertTrue(Cache::has("suggested_users:{$user->id}"));

        $this->service->clearSuggestionCache($user);
        $this->assertTrue(Cache::missing("suggested_users:{$user->id}"));

        DB::enableQueryLog();
        DB::flushQueryLog();
        $recomputed = $this->service->getSuggestedUsers($user, 10);
        $recomputedCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertGreaterThan(0, $recomputedCount);
        $this->assertEquals($first->pluck('id')->all(), $recomputed->pluck('id')->all());
    }

    public function test_clear_suggestion_cache_accepts_string_id(): void
    {
        $user = User::factory()->create();
        $likedAuthor = User::factory()->create();
        $likedPost = Post::factory()->create(['user_id' => $likedAuthor->id]);
        Like::factory()->create(['user_id' => $user->id, 'post_id' => $likedPost->id]);

        $this->service->getSuggestedUsers($user, 10);
        $this->assertTrue(Cache::has("suggested_users:{$user->id}"));

        $this->service->clearSuggestionCache((string) $user->id);
        $this->assertTrue(Cache::missing("suggested_users:{$user->id}"));
    }

    public function test_feed_index_caches_sidebar_blocks(): void
    {
        $user = User::factory()->create();
        Post::factory()->count(3)->create(['user_id' => $user->id]);

        $first = $this->actingAs($user)->get(route('feed'));
        $first->assertOk();

        $this->assertTrue(Cache::has("feed:user_posts:{$user->id}"));
        $this->assertTrue(Cache::has("suggested_users:{$user->id}"));
        $this->assertTrue(Cache::has("follow_counts:{$user->id}:followers"));
        $this->assertTrue(Cache::has("follow_counts:{$user->id}:following"));

        $second = $this->actingAs($user)->get(route('feed'));
        $second->assertOk();

        $this->assertEquals(
            collect($first->viewData('userPosts'))->pluck('id')->all(),
            collect($second->viewData('userPosts'))->pluck('id')->all()
        );
        $this->assertEquals($first->viewData('followersCount'), $second->viewData('followersCount'));
        $this->assertEquals($first->viewData('followingCount'), $second->viewData('followingCount'));
    }

    public function test_store_invalidates_user_posts_cache(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('feed'))->assertOk();
        $this->assertTrue(Cache::has("feed:user_posts:{$user->id}"));

        $file = UploadedFile::fake()->create('test.jpg', 0, 'image/jpeg');

        $this->actingAs($user)->post(route('posts.store'), [
            'description' => 'Cache invalidation post',
            'image_url' => $file,
        ])->assertRedirect(route('feed'));

        $this->assertTrue(Cache::missing("feed:user_posts:{$user->id}"));
    }

    public function test_follow_invalidates_counts_and_suggestion_cache(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)->get(route('feed'))->assertOk();
        $this->assertTrue(Cache::has("follow_counts:{$user->id}:following"));
        $this->assertTrue(Cache::has("suggested_users:{$user->id}"));

        $this->actingAs($user)->post("/follow/{$other->id}");

        $this->assertTrue(Cache::missing("follow_counts:{$user->id}:following"));
        $this->assertTrue(Cache::missing("follow_counts:{$other->id}:followers"));
        $this->assertTrue(Cache::missing("suggested_users:{$user->id}"));
    }

    public function test_unfollow_invalidates_counts_and_suggestion_cache(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)->get(route('feed'))->assertOk();
        $this->assertTrue(Cache::has("follow_counts:{$user->id}:following"));
        $this->assertTrue(Cache::has("suggested_users:{$user->id}"));

        $this->actingAs($user)->post("/unfollow/{$other->id}");

        $this->assertTrue(Cache::missing("follow_counts:{$user->id}:following"));
        $this->assertTrue(Cache::missing("follow_counts:{$other->id}:followers"));
        $this->assertTrue(Cache::missing("suggested_users:{$user->id}"));
    }

    public function test_toggle_like_invalidates_suggestion_cache(): void
    {
        $user = User::factory()->create();
        $likedAuthor = User::factory()->create();
        $likedPost = Post::factory()->create(['user_id' => $likedAuthor->id]);
        Like::factory()->create(['user_id' => $user->id, 'post_id' => $likedPost->id]);

        $this->service->getSuggestedUsers($user, 10);
        $this->assertTrue(Cache::has("suggested_users:{$user->id}"));

        $this->actingAs($user)
            ->post('/posts/post-1/like', [], ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();

        $this->assertTrue(Cache::missing("suggested_users:{$user->id}"));
    }
}
