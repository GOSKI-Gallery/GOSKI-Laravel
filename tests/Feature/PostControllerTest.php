<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $this->user = User::factory()->create();
    }

    public function test_feed_returns_view(): void
    {
        $response = $this->actingAs($this->user)->get(route('feed'));

        $response->assertStatus(200);
        $response->assertViewIs('feed');
    }

    public function test_feed_shows_posts(): void
    {
        Post::factory()->count(3)->create(['user_id' => $this->user]);
        Post::factory()->count(2)->create();

        $response = $this->actingAs($this->user)->get(route('feed'));

        $response->assertViewHas('posts');
        $this->assertCount(5, $response->viewData('posts'));
        $response->assertViewHas('userPosts');
        $this->assertCount(3, $response->viewData('userPosts'));
        $response->assertViewHas('suggestedUsers');
        $response->assertViewHas('followersCount');
        $response->assertViewHas('followingCount');
    }

    public function test_feed_requires_authentication(): void
    {
        $response = $this->get(route('feed'));

        $response->assertRedirect(route('login'));
    }

    public function test_store_creates_post_and_redirects(): void
    {
        $file = UploadedFile::fake()->create('test.jpg', 0, 'image/jpeg');

        $response = $this->actingAs($this->user)->post(route('posts.store'), [
            'description' => 'Test description',
            'image_url' => $file,
        ]);

        $response->assertRedirect(route('feed'));
        $response->assertSessionHas('success', 'Post criado com sucesso!');
    }

    public function test_store_requires_authentication(): void
    {
        $response = $this->post(route('posts.store'), [
            'description' => 'Test description',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_store_validates_required_image(): void
    {
        $response = $this->actingAs($this->user)->post(route('posts.store'), [
            'description' => 'Test without image',
        ]);

        $response->assertSessionHasErrors('image_url');
    }

    public function test_store_persists_location(): void
    {
        $file = UploadedFile::fake()->create('test.jpg', 0, 'image/jpeg');

        $response = $this->actingAs($this->user)->post(route('posts.store'), [
            'description' => 'Post with location',
            'image_url' => $file,
            'latitude' => -14.8871,
            'longitude' => -47.8071,
            'location_name' => 'Chapada dos Veadeiros',
        ]);

        $response->assertRedirect(route('feed'));

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/rest/v1/posts')
                && $request->method() === 'POST'
                && $request['latitude'] == -14.8871
                && $request['longitude'] == -47.8071
                && $request['location_name'] === 'Chapada dos Veadeiros';
        });
    }

    public function test_store_falls_back_to_reverse_geocode(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/*" => Http::response([], 200),
            'nominatim.openstreetmap.org/*' => Http::response([
                'display_name' => 'Alto Paraíso de Goiás, Goiás, Brazil',
            ], 200),
        ]);

        $file = UploadedFile::fake()->create('test.jpg', 0, 'image/jpeg');

        $response = $this->actingAs($this->user)->post(route('posts.store'), [
            'description' => 'Post with coordinates only',
            'image_url' => $file,
            'latitude' => -14.8871,
            'longitude' => -47.8071,
        ]);

        $response->assertRedirect(route('feed'));

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/rest/v1/posts')
                && $request->method() === 'POST'
                && $request['latitude'] == -14.8871
                && $request['location_name'] === 'Alto Paraíso de Goiás, Goiás, Brazil';
        });
    }

    public function test_store_validates_invalid_latitude(): void
    {
        $file = UploadedFile::fake()->create('test.jpg', 0, 'image/jpeg');

        $response = $this->actingAs($this->user)->post(route('posts.store'), [
            'description' => 'Invalid latitude',
            'image_url' => $file,
            'latitude' => 95,
            'longitude' => -47.8071,
        ]);

        $response->assertSessionHasErrors('latitude');
    }

    public function test_store_validates_invalid_longitude(): void
    {
        $file = UploadedFile::fake()->create('test.jpg', 0, 'image/jpeg');

        $response = $this->actingAs($this->user)->post(route('posts.store'), [
            'description' => 'Invalid longitude',
            'image_url' => $file,
            'latitude' => -14.8871,
            'longitude' => 200,
        ]);

        $response->assertSessionHasErrors('longitude');
    }
}
