<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseService
{
    protected string $url;
    protected string $key;

    public function __construct()
    {
        $this->url = rtrim(env('SUPABASE_URL'), '/');
        $this->key = env('SUPABASE_SERVICE_ROLE_KEY');
    }

    /**
     * Helper para headers padrão
     */
    private function client()
    {
        return Http::withHeaders([
            'apikey' => $this->key,
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$this->key}",
        ]);
    }
    
    /**
     * Registra um novo usuário no Auth do Supabase
     */
    public function signUp(string $email, string $password, string $username)
    {
        return $this->client()->post("{$this->url}/auth/v1/signup", [
            'email' => $email,
            'password' => $password,
            'data' => [
                'username' => $username
            ]
        ])->json();
    }

    /**
     * Autentica um usuário no Auth do Supabase
    */
    public function signIn(string $email, string $password)
    {
        return $this->client()->post("{$this->url}/auth/v1/token?grant_type=password", 
        [
            'email' => $email,
            'password' => $password,
        ])->json();
    }

    /**
     * Retorna o Usuario
    */
    public function getUser(string $token) 
    {
        return Http::withHeaders([
            'apikey' => $this->key,
            'Authorization' => "Bearer {$token}",
        ])->get("{$this->url}/auth/v1/user")->json();
    }

    public function getPosts()
    {
        return $this->client()->get("{$this->url}/rest/v1/posts?select=*,users(*)&order=created_at.desc")->json();
    }

    /**
     * Busca no Banco de Dados (PostgREST)
     */
    public function from(string $table)
    {
        return Http::withHeaders([
            'apikey' => $this->key,
            'Authorization' => "Bearer {$this->key}",
        ])->get("{$this->url}/rest/v1/{$table}");
    }
}   