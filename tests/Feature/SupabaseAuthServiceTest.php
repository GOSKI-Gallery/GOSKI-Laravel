<?php

namespace Tests\Feature;

use App\Services\SupabaseAuthService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SupabaseAuthServiceTest extends TestCase
{
    protected string $supabaseUrl;

    private SupabaseAuthService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supabaseUrl = 'https://supabase-test.goski.local';
        config(['supabase.url' => $this->supabaseUrl]);
        config(['supabase.service_role_key' => 'test-service-role-key']);
        config(['supabase.anon_key' => 'test-anon-key']);

        $this->service = app(SupabaseAuthService::class);
    }

    public function test_sign_up_sends_correct_payload(): void
    {
        Http::fake(["{$this->supabaseUrl}/*" => Http::response([], 200)]);

        $this->service->signUp('user@test.com', 'secret123', 'testuser');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/auth/v1/signup')
                && $request->method() === 'POST'
                && $request['email'] === 'user@test.com'
                && $request['password'] === 'secret123'
                && $request['data']['username'] === 'testuser';
        });
    }

    public function test_sign_up_returns_response(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/auth/v1/signup" => Http::response(['id' => 'new-user-id'], 200),
        ]);

        $result = $this->service->signUp('user@test.com', 'secret123', 'testuser');

        $this->assertEquals(['id' => 'new-user-id'], $result);
    }

    public function test_sign_up_uses_anon_key(): void
    {
        Http::fake(["{$this->supabaseUrl}/*" => Http::response([], 200)]);

        $this->service->signUp('a@b.com', 'secret', 'user');

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization')
                && str_contains($request->header('Authorization')[0] ?? '', 'Bearer');
        });
    }

    public function test_sign_in_sends_correct_payload(): void
    {
        Http::fake(["{$this->supabaseUrl}/*" => Http::response([], 200)]);

        $this->service->signIn('user@test.com', 'secret123');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/auth/v1/token')
                && $request->method() === 'POST'
                && $request['email'] === 'user@test.com'
                && $request['password'] === 'secret123';
        });
    }

    public function test_sign_in_returns_response(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/auth/v1/token*" => Http::response([
                'access_token' => 'jwt-token',
                'user' => ['id' => 'user-1'],
            ], 200),
        ]);

        $result = $this->service->signIn('user@test.com', 'secret123');

        $this->assertEquals('jwt-token', $result['access_token']);
    }

    public function test_sign_in_returns_error_for_invalid_credentials(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/auth/v1/token*" => Http::response([
                'error_code' => 'invalid_credentials',
                'msg' => 'Invalid login credentials',
            ], 400),
        ]);

        $result = $this->service->signIn('wrong@test.com', 'wrong');

        $this->assertArrayHasKey('error_code', $result);
    }

    public function test_get_user_sends_correct_token(): void
    {
        Http::fake(["{$this->supabaseUrl}/*" => Http::response([], 200)]);

        $this->service->getUser('user-jwt-token');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/auth/v1/user')
                && $request->method() === 'GET'
                && str_contains($request->header('Authorization')[0] ?? '', 'user-jwt-token');
        });
    }

    public function test_get_user_returns_user_data(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/auth/v1/user" => Http::response([
                'id' => 'user-1',
                'email' => 'user@test.com',
            ], 200),
        ]);

        $result = $this->service->getUser('valid-token');

        $this->assertEquals('user-1', $result['id']);
    }

    public function test_update_user_sends_password_update(): void
    {
        Http::fake(["{$this->supabaseUrl}/*" => Http::response([], 200)]);

        $this->service->updateUser('user-1', ['password' => 'newpass'], null);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/auth/v1/admin/users/user-1')
                && $request->method() === 'PUT'
                && $request['password'] === 'newpass';
        });
    }

    public function test_update_user_returns_error_on_password_failure(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/auth/v1/admin/users/user-1" => Http::response([
                'error' => 'Password too weak',
            ], 400),
        ]);

        $result = $this->service->updateUser('user-1', ['password' => 'short'], null);

        $this->assertArrayHasKey('error', $result);
    }

    public function test_update_user_sends_username_update(): void
    {
        Http::fake(["{$this->supabaseUrl}/*" => Http::response([], 200)]);

        $this->service->updateUser('user-1', ['username' => 'newuser'], null);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/rest/v1/users?id=eq.user-1')
                && $request->method() === 'PATCH'
                && ($request['username'] ?? '') === 'newuser';
        });
    }

    public function test_delete_user_sends_request(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/auth/v1/admin/users/user-1" => Http::response([], 200),
        ]);

        $this->service->deleteUser('user-1');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/auth/v1/admin/users/user-1')
                && $request->method() === 'DELETE';
        });
    }

    public function test_delete_user_throws_on_failure(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/auth/v1/admin/users/user-1" => Http::response('Server error', 500),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao deletar usuário no Supabase Auth');

        $this->service->deleteUser('user-1');
    }

    public function test_get_public_url_returns_correct_string(): void
    {
        $url = $this->service->getPublicUrl('profiles', 'user-1/photo.jpg');

        $this->assertEquals(
            "{$this->supabaseUrl}/storage/v1/object/public/profiles/user-1/photo.jpg",
            $url
        );
    }

    public function test_upload_image_sends_request(): void
    {
        Http::fake(["{$this->supabaseUrl}/*" => Http::response([], 200)]);

        $file = UploadedFile::fake()->create('photo.jpg', 0, 'image/jpeg');

        $this->service->uploadImage('profiles', 'path/to/photo.jpg', $file);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/storage/v1/object/profiles/path/to/photo.jpg')
                && $request->method() === 'POST';
        });
    }
}
