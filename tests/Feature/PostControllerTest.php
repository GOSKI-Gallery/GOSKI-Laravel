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
}
