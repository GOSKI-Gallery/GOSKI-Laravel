<?php

namespace Tests\Feature;

use App\Models\PushToken;
use App\Models\User;
use App\Services\SupabasePushTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushTokenServiceTest extends TestCase
{
    use RefreshDatabase;

    private SupabasePushTokenService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config(['supabase.url' => $this->supabaseUrl]);
        config(['supabase.service_role_key' => 'test-svc-key']);
        config(['supabase.anon_key' => 'test-anon-key']);

        $this->service = app(SupabasePushTokenService::class);
    }

    public function test_get_tokens_by_user_returns_tokens(): void
    {
        $user = User::factory()->create();
        PushToken::factory()->count(2)->create(['user_id' => $user->id]);

        $tokens = $this->service->getTokensByUserId((string) $user->id);

        $this->assertCount(2, $tokens);
        $this->assertArrayHasKey('token', $tokens[0]);
    }

    public function test_get_tokens_by_user_returns_empty_when_none(): void
    {
        $user = User::factory()->create();

        $tokens = $this->service->getTokensByUserId((string) $user->id);

        $this->assertSame([], $tokens);
    }

    public function test_upsert_token_creates_and_updates_via_db(): void
    {
        $user = User::factory()->create();

        $created = $this->service->upsertToken($user->id, 'ExponentPushToken[abcdef]', 'android');
        $this->assertTrue($created);
        $this->assertDatabaseHas('push_tokens', [
            'user_id' => $user->id,
            'token' => 'ExponentPushToken[abcdef]',
            'platform' => 'android',
        ]);

        $updated = $this->service->upsertToken($user->id, 'ExponentPushToken[newtoken]', 'ios');
        $this->assertTrue($updated);
        $this->assertDatabaseHas('push_tokens', [
            'user_id' => $user->id,
            'token' => 'ExponentPushToken[newtoken]',
            'platform' => 'ios',
        ]);
        $this->assertDatabaseMissing('push_tokens', [
            'user_id' => $user->id,
            'token' => 'ExponentPushToken[abcdef]',
        ]);
    }

    public function test_delete_token_removes_from_db(): void
    {
        $token = PushToken::factory()->create([
            'token' => 'ExponentPushToken[delete-me]',
        ]);

        $deleted = $this->service->deleteToken('ExponentPushToken[delete-me]');

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('push_tokens', ['id' => $token->id]);
    }

    public function test_delete_token_returns_false_when_token_not_found(): void
    {
        $deleted = $this->service->deleteToken('ExponentPushToken[nonexistent]');

        $this->assertFalse($deleted);
    }
}
