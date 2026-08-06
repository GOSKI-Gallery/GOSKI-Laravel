<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostLocationControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_location_requires_authentication(): void
    {
        $post = Post::factory()->withLocation()->create(['user_id' => $this->user->id]);

        $response = $this->getJson(route('post.location.show', $post->id));

        $response->assertUnauthorized();
    }

    public function test_returns_post_and_nearby_posts(): void
    {
        $post = Post::factory()->withLocation()->create(['user_id' => $this->user]);
        $nearby1 = Post::factory()->withLocation()->create();
        $nearby2 = Post::factory()->withLocation()->create();
        Post::factory()->create(['user_id' => $this->user]);

        $response = $this->actingAs($this->user)->getJson(route('post.location.show', $post->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'post' => ['id', 'latitude', 'longitude', 'location_name', 'users' => ['id', 'username']],
            'nearby' => [['id', 'image_url', 'latitude', 'longitude', 'distance_km', 'users' => ['id', 'username']]],
        ]);

        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertSame((int) $post->id, $data['post']['id']);
        $this->assertSame('Chapada dos Veadeiros', $data['post']['location_name']);
        $this->assertCount(2, $data['nearby']);

        $nearbyIds = array_column($data['nearby'], 'id');
        $this->assertNotContains((int) $post->id, $nearbyIds);
        $this->assertContains((int) $nearby1->id, $nearbyIds);
        $this->assertContains((int) $nearby2->id, $nearbyIds);
    }

    public function test_nearby_posts_are_sorted_by_distance(): void
    {
        $post = Post::factory()->withLocation()->create(['user_id' => $this->user]);

        Post::factory()->create([
            'user_id' => $this->user->id,
            'latitude' => -14.8871,
            'longitude' => -47.8071,
        ]);
        Post::factory()->create([
            'user_id' => $this->user->id,
            'latitude' => -15.4,
            'longitude' => -47.1,
        ]);

        $response = $this->actingAs($this->user)->getJson(route('post.location.show', $post->id));

        $distances = array_column($response->json('nearby'), 'distance_km');
        $sorted = $distances;
        sort($sorted);

        $this->assertSame($sorted, $distances);
    }

    public function test_returns_404_when_post_has_no_location(): void
    {
        $post = Post::factory()->create(['user_id' => $this->user]);

        $response = $this->actingAs($this->user)->getJson(route('post.location.show', $post->id));

        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }

    public function test_returns_404_when_post_does_not_exist(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('post.location.show', 999999));

        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }

    public function test_returns_static_map_grid_with_width_and_height(): void
    {
        $post = Post::factory()->withLocation()->create(['user_id' => $this->user]);

        $response = $this->actingAs($this->user)->getJson(
            route('post.location.show', $post->id).'?width=512&height=512'
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'map' => [
                'zoom',
                'width',
                'height',
                'attribution',
                'center_x',
                'center_y',
                'tiles' => [['x', 'y', 'left', 'top', 'url']],
                'pins',
            ],
        ]);

        $map = $response->json('map');
        $this->assertSame(512, $map['width']);
        $this->assertSame(512, $map['height']);
        $this->assertSame('© OpenStreetMap contributors © CARTO', $map['attribution']);
        $this->assertNotEmpty($map['tiles']);

        foreach ($map['tiles'] as $tile) {
            $this->assertMatchesRegularExpression(
                '#^https://[a-d]\.basemaps\.cartocdn\.com/rastertiles/voyager/'.$map['zoom'].'/#',
                $tile['url']
            );
        }
    }

    public function test_map_ignores_invalid_or_out_of_range_dimensions(): void
    {
        $post = Post::factory()->withLocation()->create(['user_id' => $this->user]);

        $response = $this->actingAs($this->user)->getJson(
            route('post.location.show', $post->id).'?width=abc&height=-5'
        );

        $response->assertStatus(200);
        $this->assertNull($response->json('map'));

        $response = $this->actingAs($this->user)->getJson(
            route('post.location.show', $post->id).'?width=99999&height=99999'
        );

        $map = $response->json('map');
        $this->assertSame(1200, $map['width']);
        $this->assertSame(1200, $map['height']);
    }
}
