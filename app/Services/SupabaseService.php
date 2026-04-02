<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseService
{
    protected string $url;

    protected string $key;

    protected string $anonKey;

    public function __construct()
    {
        $this->url = rtrim(env('SUPABASE_URL'), '/');
        $this->key = env('SUPABASE_SERVICE_ROLE_KEY');
        $this->anonKey = env('SUPABASE_ANON_KEY');
    }

    private function client(bool $useServiceKey = true)
    {
        $token = $useServiceKey ? $this->key : $this->anonKey;

        return Http::withHeaders([
            'apikey' => $this->anonKey,
            'Authorization' => "Bearer {$token}",
        ]);
    }

    public function signUp(string $email, string $password, string $username)
    {
        return $this->client(false)->post("{$this->url}/auth/v1/signup", [
            'email' => $email,
            'password' => $password,
            'data' => ['username' => $username],
        ])->json();
    }

    public function signIn(string $email, string $password)
    {
        return $this->client(false)->post("{$this->url}/auth/v1/token?grant_type=password",
            [
                'email' => $email,
                'password' => $password,
            ])->json();
    }

    public function getUser(string $token)
    {
        return Http::withHeaders([
            'apikey' => $this->anonKey,
            'Authorization' => "Bearer {$token}",
        ])->get("{$this->url}/auth/v1/user")->json();
    }

    public function getPosts()
    {
        $posts = $this->client()->get("{$this->url}/rest/v1/posts?select=*,users(*)&order=created_at.desc")->json();

        if (is_null($posts) || (isset($posts['message']) && $posts['message'] === 'JWT expired')) {
            return [];
        }

        return array_map(function ($post) {
            if (is_string($post)) {
                $post = json_decode($post, true);
            }
            if (isset($post['users']) && is_string($post['users'])) {
                $post['users'] = json_decode($post['users'], true);
            }

            return $post;
        }, $posts);
    }

    public function insert(string $table, array $data)
    {
        return $this->client()->post("{$this->url}/rest/v1/{$table}", $data)->json();
    }

    public function uploadImage(string $bucket, string $path, $file)
    {
        $url = "{$this->url}/storage/v1/object/{$bucket}/{$path}";
        
        return Http::withHeaders([
            'apikey' => $this->anonKey,
            'Authorization' => "Bearer {$this->key}",
            'Content-Type' => $file->getMimeType(),
        ])->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
        ->post($url)
        ->throw()
        ->json();
    }

    public function getPublicUrl(string $bucket, string $path)
    {
        return "{$this->url}/storage/v1/object/public/{$bucket}/{$path}";
    }
}
