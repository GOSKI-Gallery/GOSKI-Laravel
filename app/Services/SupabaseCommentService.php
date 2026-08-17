<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class SupabaseCommentService extends SupabaseBaseService
{
    public function getComments(string $postId): array
    {
        $response = $this->client()->get("{$this->url}/rest/v1/comments", [
            'post_id' => 'eq.'.$postId,
            'order' => 'created_at.asc',
        ]);

        if ($response->failed()) {
            return [];
        }

        $comments = $response->json() ?? [];

        $userIds = array_unique(array_column($comments, 'user_id'));

        if (! empty($userIds)) {
            $users = User::whereIn('id', $userIds)->get()->keyBy('id');

            foreach ($comments as &$comment) {
                $user = $users->get($comment['user_id']);

                if ($user) {
                    $comment['users'] = [
                        'id' => $user->id,
                        'username' => $user->username,
                        'profile_photo_url' => $user->profile_photo_url,
                    ];
                }
            }
        }

        foreach ($comments as &$comment) {
            $comment['time_ago'] = isset($comment['created_at'])
                ? Carbon::parse($comment['created_at'])->diffForHumans()
                : '';
        }

        return $comments;
    }

    public function addComment(string $userId, string $postId, string $body): ?array
    {
        $response = $this->client()
            ->withHeaders(['Prefer' => 'return=representation'])
            ->post("{$this->url}/rest/v1/comments", [
                'user_id' => $userId,
                'post_id' => $postId,
                'body' => $body,
            ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        return is_array($data) ? $data[0] ?? $data : null;
    }

    public function deleteComment(string $commentId): bool
    {
        $response = $this->client()
            ->delete("{$this->url}/rest/v1/comments?id=eq.{$commentId}");

        return ! $response->failed();
    }

    public function getCommentCount(string $postId): int
    {
        $response = $this->client()->get("{$this->url}/rest/v1/comments", [
            'post_id' => 'eq.'.$postId,
            'select' => 'id',
        ]);

        if ($response->failed()) {
            return 0;
        }

        $data = $response->json();

        return is_array($data) ? count($data) : 0;
    }
}
