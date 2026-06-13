<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SupabaseNotificationService extends SupabaseBaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getNotifications()
    {
        $userId = Auth::id();
        $prefix = DB::getDriverName() === 'pgsql' ? 'laravel.' : '';

        $follows = DB::table($prefix.'follows')
            ->join($prefix.'users', 'follows.follower_id', '=', 'users.id')
            ->where('follows.followed_id', $userId)
            ->select(
                'follows.id as source_id',
                'users.id as user_id',
                'users.username',
                'users.profile_photo_url',
                DB::raw("'follow' as type"),
                DB::raw('CAST(NULL AS INTEGER) as post_id'),
                'follows.created_at'
            );

        $likes = DB::table($prefix.'likes')
            ->join($prefix.'posts', 'likes.post_id', '=', 'posts.id')
            ->join($prefix.'users', 'likes.user_id', '=', 'users.id')
            ->where('posts.user_id', $userId)
            ->where('likes.user_id', '!=', $userId)
            ->select(
                'likes.id as source_id',
                'users.id as user_id',
                'users.username',
                'users.profile_photo_url',
                DB::raw("'like' as type"),
                'posts.id as post_id',
                'likes.created_at'
            );

        $notifications = $follows->union($likes)
            ->orderBy('created_at', 'desc')
            ->get();

        $lastReadAt = Cache::get("user_{$userId}_notifications_read_at");
        $deletedIds = Cache::get("user_{$userId}_deleted_notifications", []);

        return $notifications->reject(function ($item) use ($deletedIds) {
            return in_array($item->type.'_'.$item->source_id, $deletedIds);
        })->map(function ($item) use ($lastReadAt) {
            $createdAt = Carbon::parse($item->created_at);
            $item->is_read = $lastReadAt ? $createdAt->lte($lastReadAt) : false;
            $item->created_at_for_humans = $createdAt->diffForHumans();

            $item->id = $item->type.'_'.$item->source_id;

            return $item;
        })->values();
    }

    public function markAsRead()
    {
        $userId = Auth::id();

        Cache::put("user_{$userId}_notifications_read_at", now(), now()->addDays(30));
    }

    public function delete($id)
    {
        $userId = Auth::id();

        $deletedIds = Cache::get("user_{$userId}_deleted_notifications", []);

        if (! in_array($id, $deletedIds)) {
            $deletedIds[] = $id;
            Cache::put("user_{$userId}_deleted_notifications", $deletedIds, now()->addDays(30));
        }
    }
}
