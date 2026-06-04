<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $supabaseUrl = 'https://fuckjwtltqvngejerkzo.supabase.co';

    public function test_login_returns_landing_view(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('landing');
    }

    public function test_authenticate_with_valid_credentials_logs_in_admin_user(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $accessToken = 'admin-access-token';

        Http::fake([
            "{$this->supabaseUrl}/auth/v1/token?grant_type=password" => Http::response([
                'access_token' => $accessToken,
                'token_type' => 'bearer',
            ]),
            "{$this->supabaseUrl}/auth/v1/user" => Http::response([
                'id' => $user->id,
            ]),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticate_with_valid_credentials_logs_in_regular_user(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $accessToken = 'regular-access-token';

        Http::fake([
            "{$this->supabaseUrl}/auth/v1/token?grant_type=password" => Http::response([
                'access_token' => $accessToken,
                'token_type' => 'bearer',
            ]),
            "{$this->supabaseUrl}/auth/v1/user" => Http::response([
                'id' => $user->id,
            ]),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/feed');
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticate_with_invalid_credentials_returns_error(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/auth/v1/token?grant_type=password" => Http::response([
                'error_code' => 'invalid_credentials',
            ], 401),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email' => 'Credenciais inválidas.']);
        $this->assertGuest();
    }

    public function test_authenticate_with_email_not_confirmed(): void
    {
        Http::fake([
            "{$this->supabaseUrl}/auth/v1/token?grant_type=password" => Http::response([
                'error_code' => 'email_not_confirmed',
            ], 401),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'unconfirmed@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email' => 'Por favor, confirme seu e-mail antes de logar.']);
        $this->assertGuest();
    }

    public function test_authenticate_when_user_not_found_locally(): void
    {
        $accessToken = 'valid-access-token';

Http::fake([
            "{$this->supabaseUrl}/auth/v1/token?grant_type=password" => Http::response([
                'access_token' => $accessToken,
                'token_type' => 'bearer',
            ]),
            "{$this->supabaseUrl}/auth/v1/user" => Http::response([
                'id' => $user->id,
            ]),
            "{$this->supabaseUrl}/*" => Http::response([], 200),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'unknown@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email' => 'Email not found.',
            'password' => 'Password is incorrect.',
        ]);
        $this->assertGuest();
    }

    public function test_logout_logs_out_and_redirects(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('landingPage'));
        $this->assertGuest();
        $this->assertFalse(session()->has('password_hash_web'));
    }
}
