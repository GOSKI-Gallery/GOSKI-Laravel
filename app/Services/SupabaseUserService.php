<?php

namespace App\Services;

class SupabaseUserService extends SupabaseBaseService
{
    public function getUserById(string $userId): ?array
    {
        $response = $this->client()
            ->get("{$this->url}/rest/v1/users?id=eq.{$userId}")
            ->json();

        return (is_array($response) && count($response) > 0) ? $response[0] : null;
    }

    public function followUser(string $followerId, string $followedId)
    {
        return $this->client()->post("{$this->url}/rest/v1/follows", [
            'follower_id' => $followerId,
            'followed_id' => $followedId,
            'created_at' => now()->toIso8601String(),
        ])->json();
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

    public function hasLikedPost(string $userId, string $postId): bool
    {
        $response = $this->client()
            ->get("{$this->url}/rest/v1/likes?user_id=eq.{$userId}&post_id=eq.{$postId}")
            ->json();

        return is_array($response) && count($response) > 0;
    }
}
