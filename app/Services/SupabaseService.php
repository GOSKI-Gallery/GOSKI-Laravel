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

    public function followUser(string $followerId, string $followedId)
    {
        return $this->client()->post(
            "{$this->url}/rest/v1/follows",
            [
                'follower_id' => $followerId,
                'followed_id' => $followedId,
            ]
        )->json();
    }

    public function unfollowUser(string $followerId, string $followedId)
    {
        return $this->client()
            ->delete("{$this->url}/rest/v1/follows?follower_id=eq.{$followerId}&followed_id=eq.{$followedId}")
            ->json();
    }

    public function isFollowing(string $followerId, string $followedId): bool
    {
        $response = $this->client()
            ->get("{$this->url}/rest/v1/follows?follower_id=eq.{$followerId}&followed_id=eq.{$followedId}")
            ->json();

        return is_array($response) && count($response) > 0;
    }

    public function getFollowCount(string $userId, string $type = 'followers'): int
    {
        $column = $type === 'followers' ? 'followed_id' : 'follower_id';
        $response = $this->client()
            ->get("{$this->url}/rest/v1/follows?{$column}=eq.{$userId}")
            ->json();

        return is_array($response) ? count($response) : 0;
    }

    public function likePost(string $userId, string $postId)
    {
        return $this->client()->post(
            "{$this->url}/rest/v1/likes",
            [
                'user_id' => $userId,
                'post_id' => $postId,
            ]
        )->json();
    }

    public function unlikePost(string $userId, string $postId)
    {
        return $this->client()
            ->delete("{$this->url}/rest/v1/likes?user_id=eq.{$userId}&post_id=eq.{$postId}")
            ->json();
    }

    public function hasLikedPost(string $userId, string $postId): bool
    {
        $response = $this->client()
            ->get("{$this->url}/rest/v1/likes?user_id=eq.{$userId}&post_id=eq.{$postId}")
            ->json();

        return is_array($response) && count($response) > 0;
    }

    public function getLikeCount(string $postId): int
    {
        $response = $this->client()
            ->get("{$this->url}/rest/v1/likes?post_id=eq.{$postId}")
            ->json();

        return is_array($response) ? count($response) : 0;
    }
}
