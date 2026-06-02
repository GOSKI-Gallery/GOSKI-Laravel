<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabasePostService extends SupabaseBaseService
{
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

    public function likePost(string $userId, string $postId)
    {
        return $this->client()->post("{$this->url}/rest/v1/likes", [
            'user_id' => $userId,
            'post_id' => $postId,
        ])->json();
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
