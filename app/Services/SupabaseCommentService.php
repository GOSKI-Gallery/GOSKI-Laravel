<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    /**
     * Count comments via direct DB read (0 HTTP round-trips). Reads are
     * the direct-DB pattern per AGENTS.md §6; used by CommentController::store
     * so the request costs a single HTTP (the insert POST).
     */
    public function getCommentCountLocal(string $postId): int
    {
        $prefix = DB::getDriverName() === 'pgsql' ? 'laravel.' : '';

        return DB::table($prefix.'comments')->where('post_id', $postId)->count();
    }

    public function getCommentCount(string $postId): int
    {
        try {
            $response = $this->client()
                ->withHeaders(['Prefer' => 'count=exact'])
                ->get("{$this->url}/rest/v1/comments", [
                    'post_id' => 'eq.'.$postId,
                    'select' => 'id',
                ]);

            if ($response->failed()) {
                return 0;
            }

            if (preg_match('#/(\d+)\s*$#', $response->header('Content-Range'), $matches)) {
                return (int) $matches[1];
            }

            return 0;
        } catch (\Throwable) {
            return 0;
        }
    }
}
