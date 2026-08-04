<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
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
            'created_at' => now()->toIso8601String(),
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

    public function getCommentCount(string $postId): int
    {
        $response = $this->client()
            ->get("{$this->url}/rest/v1/comments?post_id=eq.{$postId}")
            ->json();

        return is_array($response) ? count($response) : 0;
    }

    public function getNearbyPosts(string $postId, float $latitude, float $longitude, int $radiusKm = 25, int $limit = 50): array
    {
        $prefix = DB::getDriverName() === 'pgsql' ? 'laravel.' : '';

        $dLat = $radiusKm / 111.32;
        $dLng = $radiusKm / (111.32 * cos(deg2rad($latitude)));

        $rows = DB::table($prefix.'posts')
            ->join($prefix.'users', 'posts.user_id', '=', 'users.id')
            ->where('posts.id', '!=', $postId)
            ->whereNotNull('posts.latitude')
            ->whereNotNull('posts.longitude')
            ->whereBetween('posts.latitude', [$latitude - $dLat, $latitude + $dLat])
            ->whereBetween('posts.longitude', [$longitude - $dLng, $longitude + $dLng])
            ->select(
                'posts.id',
                'posts.user_id',
                'posts.image_url',
                'posts.description',
                'posts.latitude',
                'posts.longitude',
                'posts.location_name',
                'posts.created_at',
                'users.username',
                'users.profile_photo_url',
            )
            ->orderBy('posts.created_at', 'desc')
            ->limit($limit)
            ->get();

        return collect($rows)
            ->map(function ($row) use ($latitude, $longitude) {
                return [
                    'id' => (int) $row->id,
                    'user_id' => $row->user_id,
                    'image_url' => $row->image_url,
                    'description' => $row->description,
                    'latitude' => (float) $row->latitude,
                    'longitude' => (float) $row->longitude,
                    'location_name' => $row->location_name,
                    'created_at' => $row->created_at,
                    'distance_km' => round($this->haversineKm($latitude, $longitude, (float) $row->latitude, (float) $row->longitude), 2),
                    'users' => [
                        'id' => $row->user_id,
                        'username' => $row->username,
                        'profile_photo_url' => $row->profile_photo_url,
                    ],
                ];
            })
            ->sortBy('distance_km')
            ->values()
            ->all();
    }

    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadiusKm * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
