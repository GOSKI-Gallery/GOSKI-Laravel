<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Services\SupabasePostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostNearbyServiceTest extends TestCase
{
    use RefreshDatabase;

    private SupabasePostService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(SupabasePostService::class);
    }

    public function test_get_nearby_posts_returns_posts_with_distance(): void
    {
        $center = Post::factory()->withLocation()->create();

        Post::factory()->create([
            'latitude' => -14.8871,
            'longitude' => -47.8071,
        ]);
        Post::factory()->create([
            'latitude' => -14.9,
            'longitude' => -47.8,
        ]);
        Post::factory()->create();

        $nearby = $this->service->getNearbyPosts((string) $center->id, -14.8871, -47.8071);

        $this->assertCount(2, $nearby);

        foreach ($nearby as $post) {
            $this->assertArrayHasKey('distance_km', $post);
            $this->assertArrayHasKey('image_url', $post);
            $this->assertArrayHasKey('users', $post);
            $this->assertArrayHasKey('username', $post['users']);
        }
    }

    public function test_get_nearby_posts_excludes_the_post_itself(): void
    {
        $center = Post::factory()->withLocation()->create();

        Post::factory()->create([
            'latitude' => -14.8871,
            'longitude' => -47.8071,
        ]);

        $nearby = $this->service->getNearbyPosts((string) $center->id, -14.8871, -47.8071);

        $ids = array_column($nearby, 'id');

        $this->assertNotContains((int) $center->id, $ids);
    }

    public function test_get_nearby_posts_sorts_by_distance(): void
    {
        $center = Post::factory()->withLocation()->create();

        Post::factory()->create([
            'latitude' => -15.0,
            'longitude' => -47.7,
        ]);
        Post::factory()->create([
            'latitude' => -14.8871,
            'longitude' => -47.8071,
        ]);

        $nearby = $this->service->getNearbyPosts((string) $center->id, -14.8871, -47.8071);

        $distances = array_column($nearby, 'distance_km');
        $sorted = $distances;
        sort($sorted);

        $this->assertSame($sorted, $distances);
        $this->assertSame($sorted[0], $distances[0]);
    }

    public function test_get_nearby_posts_returns_empty_when_none_in_radius(): void
    {
        $center = Post::factory()->withLocation()->create();

        Post::factory()->create([
            'latitude' => -29.9,
            'longitude' => -51.2,
        ]);

        $nearby = $this->service->getNearbyPosts((string) $center->id, -14.8871, -47.8071, 25);

        $this->assertSame([], $nearby);
    }
}
