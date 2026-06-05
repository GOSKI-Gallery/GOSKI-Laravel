<?php

namespace App\Services;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /** @return LengthAwarePaginator<int, Post> */
    public function getRankedFeed(User $user, int $perPage = 20): LengthAwarePaginator
    {
        $tagIds = $this->getUserLikedTagIds($user);

        $tagSubquery = $tagIds->isEmpty()
            ? 'NULL'
            : $tagIds->implode(',');

        $driver = DB::getDriverName();
        $epochExpr = $driver === 'sqlite'
            ? "strftime('%%s', posts.created_at)"
            : 'EXTRACT(EPOCH FROM posts.created_at)';

        return Post::select('posts.*')
            ->addSelect(DB::raw(
                'CASE WHEN f.id IS NOT NULL THEN 1 ELSE 0 END AS is_following'
            ))
            ->addSelect(DB::raw(
                "(SELECT COUNT(*) FROM post_tag pt WHERE pt.post_id = posts.id AND pt.tag_id IN ({$tagSubquery})) AS matching_tags_count"
            ))
            ->leftJoin('follows as f', function ($join) use ($user) {
                $join->on('f.followed_id', 'posts.user_id')
                    ->where('f.follower_id', $user->id);
            })
            ->orderByRaw(
                "({$epochExpr} + CASE WHEN f.id IS NOT NULL THEN 3600 ELSE 0 END + (SELECT COUNT(*) FROM post_tag pt WHERE pt.post_id = posts.id AND pt.tag_id IN ({$tagSubquery})) * 600) DESC")
            ->with('users')
            ->paginate($perPage);
    }

    /** @return Collection<int, User> */
    public function getSuggestedUsers(User $user, int $limit = 5): Collection
    {
        $authId = $user->id;

        $alreadyFollowing = DB::table('follows')
            ->where('follower_id', $authId)
            ->pluck('followed_id')
            ->toArray();

        $likedAuthorIds = Like::where('likes.user_id', $authId)
            ->join('posts', 'posts.id', '=', 'likes.post_id')
            ->where('posts.user_id', '!=', $authId)
            ->distinct()
            ->pluck('posts.user_id')
            ->filter()
            ->values();

        $followingIds = DB::table('follows')
            ->where('follower_id', $authId)
            ->pluck('followed_id');

        $mutualIds = DB::table('follows')
            ->whereIn('follower_id', $followingIds)
            ->where('followed_id', '!=', $authId)
            ->selectRaw('followed_id, COUNT(*) as cnt')
            ->groupBy('followed_id')
            ->pluck('cnt', 'followed_id');

        $scores = [];
        foreach ($likedAuthorIds as $id) {
            $scores[$id] = ($scores[$id] ?? 0) + 2;
        }
        foreach ($mutualIds as $id => $cnt) {
            $scores[$id] = ($scores[$id] ?? 0) + 1 + $cnt;
        }
        foreach ($alreadyFollowing as $id) {
            unset($scores[$id]);
        }

        arsort($scores);
        $topIds = array_slice(array_keys($scores), 0, $limit);

        if (empty($topIds)) {
            return collect();
        }

        $users = User::whereIn('id', $topIds)->get()->keyBy('id');

        return collect($topIds)
            ->map(fn ($id) => $users[$id] ?? null)
            ->filter()
            ->values();
    }

    /** @return Collection<int, mixed> */
    public function getUserLikedTagIds(User $user): Collection
    {
        return Cache::remember("user:{$user->id}:liked_tag_ids", 3600, function () use ($user) {
            return Like::where('user_id', $user->id)
                ->join('post_tag', 'post_tag.post_id', '=', 'likes.post_id')
                ->distinct()
                ->pluck('post_tag.tag_id');
        });
    }

    public function clearTagCache(User|string $user): void
    {
        $id = $user instanceof User ? $user->id : $user;
        Cache::forget("user:{$id}:liked_tag_ids");
    }
}
