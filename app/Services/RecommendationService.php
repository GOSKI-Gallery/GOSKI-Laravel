<?php

namespace App\Services;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /** @return Paginator<int, Post> */
    public function getRankedFeed(User $user, int $perPage = 20, int $page = 1): Paginator
    {
        $tagIds = $this->getUserLikedTagIds($user);

        $driver = DB::getDriverName();
        $prefix = $driver === 'pgsql' ? 'laravel.' : '';
        $epochExpr = match ($driver) {
            'sqlite' => "strftime('%s', posts.created_at)",
            default => 'EXTRACT(EPOCH FROM posts.created_at)',
        };

        // CTE to compute matching_tags_count per post (single scan, not per-row)
        // Use whereIn directly which handles bindings automatically
        $tagCte = DB::table($prefix.'post_tag as pt')
            ->select('pt.post_id', DB::raw('COUNT(*) as matching_tags_count'))
            ->when(! $tagIds->isEmpty(), function ($query) use ($tagIds) {
                $query->whereIn('pt.tag_id', $tagIds);
            })
            ->groupBy('pt.post_id');

        // Main query with CTE joined
        $query = Post::select('posts.*')
            ->addSelect(DB::raw('COALESCE(tag_counts.matching_tags_count, 0) as matching_tags_count'))
            ->addSelect(DB::raw('CASE WHEN f.id IS NOT NULL THEN 1 ELSE 0 END as is_following'))
            ->leftJoin($prefix.'follows as f', function ($join) use ($user) {
                $join->on('f.followed_id', 'posts.user_id')
                    ->where('f.follower_id', $user->id);
            })
            ->leftJoinSub($tagCte, 'tag_counts', function ($join) {
                $join->on('tag_counts.post_id', '=', 'posts.id');
            })
            ->orderByRaw(
                "({$epochExpr} + CASE WHEN f.id IS NOT NULL THEN 3600 ELSE 0 END + COALESCE(tag_counts.matching_tags_count, 0) * 600) DESC"
            )
            ->with('users')
            ->withCount('likes')
            ->withCount('comments');

        return $query->simplePaginate($perPage, ['*'], 'page', $page);
    }

    /** @return Collection<int, User> */
    public function getSuggestedUsers(User $user, int $limit = 5): Collection
    {
        $authId = $user->id;

        return Cache::remember("suggested_users:{$authId}", now()->addSeconds(300), function () use ($authId, $limit) {
            $prefix = DB::getDriverName() === 'pgsql' ? 'laravel.' : '';

            $alreadyFollowing = DB::table($prefix.'follows')
                ->where('follower_id', $authId)
                ->pluck('followed_id')
                ->toArray();

            $likedAuthorIds = Like::where('likes.user_id', $authId)
                ->join($prefix.'posts', 'posts.id', '=', 'likes.post_id')
                ->where('posts.user_id', '!=', $authId)
                ->distinct()
                ->pluck('posts.user_id')
                ->filter()
                ->values();

            $followingIds = DB::table($prefix.'follows')
                ->where('follower_id', $authId)
                ->pluck('followed_id');

            $mutualIds = DB::table($prefix.'follows')
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
        });
    }

    /** @return Collection<int, mixed> */
    public function getUserLikedTagIds(User $user): Collection
    {
        $prefix = DB::getDriverName() === 'pgsql' ? 'laravel.' : '';

        return Cache::remember("user:{$user->id}:liked_tag_ids", 3600, function () use ($user, $prefix) {
            return Like::where('user_id', $user->id)
                ->join($prefix.'post_tag', 'post_tag.post_id', '=', 'likes.post_id')
                ->distinct()
                ->pluck('post_tag.tag_id');
        });
    }

    public function clearTagCache(User|string $user): void
    {
        $id = $user instanceof User ? $user->id : $user;
        Cache::forget("user:{$id}:liked_tag_ids");
    }

    public function clearSuggestionCache(User|string $user): void
    {
        $id = $user instanceof User ? $user->id : $user;
        Cache::forget("suggested_users:{$id}");
    }
}
