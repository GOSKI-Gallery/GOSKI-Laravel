<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FollowControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_follow_requires_authentication()
    {
        $response = $this->post('/follow/some-id');

        $response->assertRedirect('/login');
    }

    public function test_follow_self_returns_error_for_ajax()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post("/follow/{$user->id}", [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Você não pode seguir a si mesmo.',
        ]);
    }

    public function test_follow_self_returns_error_for_regular()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post("/follow/{$user->id}");

        $response->assertSessionHas('error', 'Você não pode seguir a si mesmo.');
    }

    public function test_follow_another_user_success_ajax()
    {
        Http::fake([
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();
        $other = User::factory()->create();

        $response = $this->actingAs($user)
            ->post("/follow/{$other->id}", [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson([
            'success' => true,
            'message' => 'Followed successfully!',
            'following' => true,
        ]);
    }

    public function test_follow_another_user_success_regular()
    {
        Http::fake([
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();
        $other = User::factory()->create();

        $response = $this->actingAs($user)->post("/follow/{$other->id}");

        $response->assertSessionHas('success', 'Followed successfully!');
    }

    public function test_unfollow_requires_authentication()
    {
        $response = $this->post('/unfollow/some-id');

        $response->assertRedirect('/login');
    }

    public function test_unfollow_success_ajax()
    {
        Http::fake([
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();
        $other = User::factory()->create();

        $response = $this->actingAs($user)
            ->post("/unfollow/{$other->id}", [], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertJson([
            'success' => true,
            'message' => 'Unfollowed successfully!',
            'following' => false,
        ]);
    }

    public function test_unfollow_success_regular()
    {
        Http::fake([
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $user = User::factory()->create();
        $other = User::factory()->create();

        $response = $this->actingAs($user)->post("/unfollow/{$other->id}");

        $response->assertSessionHas('success', 'Unfollowed successfully!');
    }
}
