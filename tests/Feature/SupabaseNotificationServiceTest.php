<?php

namespace Tests\Feature;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Services\SupabaseNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SupabaseNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private SupabaseNotificationService $service;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->service = app(SupabaseNotificationService::class);
    }

    public function test_get_notifications_returns_empty_when_no_activity(): void
    {
        $notifications = $this->service->getNotifications();

        $this->assertCount(0, $notifications);
    }

    public function test_get_notifications_includes_follow_notifications(): void
    {
        $follower = User::factory()->create();
        $this->user->followers()->attach($follower->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $notifications = $this->service->getNotifications();

        $this->assertCount(1, $notifications);
        $this->assertEquals('follow', $notifications[0]->type);
    }

    public function test_get_notifications_includes_like_notifications(): void
    {
        $liker = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        Like::factory()->create([
            'user_id' => $liker->id,
            'post_id' => $post->id,
        ]);

        $notifications = $this->service->getNotifications();

        $this->assertCount(1, $notifications);
        $this->assertEquals('like', $notifications[0]->type);
    }

    public function test_get_notifications_orders_by_created_at_desc(): void
    {
        $follower = User::factory()->create();
        $this->user->followers()->attach($follower->id, [
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $liker = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        Like::factory()->create([
            'user_id' => $liker->id,
            'post_id' => $post->id,
            'created_at' => now(),
        ]);

        $notifications = $this->service->getNotifications();

        $this->assertCount(2, $notifications);
        $this->assertEquals('like', $notifications[0]->type);
        $this->assertEquals('follow', $notifications[1]->type);
    }

    public function test_mark_as_read_caches_timestamp(): void
    {
        $this->service->markAsRead();

        $cacheKey = "user_{$this->user->id}_notifications_read_at";
        $this->assertNotNull(Cache::get($cacheKey));
    }

    public function test_mark_as_read_makes_notifications_is_read(): void
    {
        $follower = User::factory()->create();
        $this->user->followers()->attach($follower->id, [
            'created_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
        ]);

        $this->service->markAsRead();
        $notifications = $this->service->getNotifications();

        $this->assertTrue($notifications[0]->is_read);
    }

    public function test_delete_caches_deleted_id(): void
    {
        $follower = User::factory()->create();
        $this->user->followers()->attach($follower->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $notifications = $this->service->getNotifications();
        $this->assertCount(1, $notifications);

        $this->service->delete($notifications[0]->id);

        $notifications = $this->service->getNotifications();
        $this->assertCount(0, $notifications);
    }

    public function test_delete_does_not_throw_for_nonexistent_id(): void
    {
        $this->service->delete('nonexistent_id');

        $notifications = $this->service->getNotifications();
        $this->assertCount(0, $notifications);
    }
}
