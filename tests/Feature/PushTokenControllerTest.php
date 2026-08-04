<?php

namespace Tests\Feature;

use App\Models\PushToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson(route('push.tokens.index'));

        $response->assertUnauthorized();
    }

    public function test_index_returns_user_tokens(): void
    {
        PushToken::factory()->count(2)->create(['user_id' => $this->user->id]);
        PushToken::factory()->create();

        $response = $this->actingAs($this->user)->getJson(route('push.tokens.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => [['id', 'user_id', 'token', 'platform']]]);
        $this->assertCount(2, $response->json('data'));
        $this->assertSame((string) $this->user->id, $response->json('data.0.user_id'));
    }

    public function test_destroy_removes_token(): void
    {
        $token = PushToken::factory()->create(['user_id' => $this->user->id, 'token' => 'ExponentPushToken[delete-me]']);

        $response = $this->actingAs($this->user)->delete(route('push.tokens.destroy', $token->token));

        $response->assertStatus(200);
        $this->assertDatabaseMissing((new PushToken)->getTable(), ['id' => $token->id]);
    }

    public function test_destroy_returns_422_when_token_not_found(): void
    {
        $response = $this->actingAs($this->user)->delete(route('push.tokens.destroy', 'ExponentPushToken[nonexistent]'));

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }
}
