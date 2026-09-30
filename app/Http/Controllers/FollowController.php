<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use App\Services\SupabaseUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FollowController extends Controller
{
    public function follow(Request $request, string $followedId)
    {
        $followerId = Auth::id();

        if ($followerId === $followedId) {
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => 'Você não pode seguir a si mesmo.'], 400);
            }

            return back()->with('error', 'Você não pode seguir a si mesmo.');
        }

        $supabaseUser = new SupabaseUserService;

        $supabaseUser->followUser($followerId, $followedId);

        $this->clearFollowCaches($followerId, $followedId);

        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => 'Followed successfully!', 'following' => true]);
        }

        return back()->with('success', 'Followed successfully!');
    }

    public function unfollow(Request $request, string $followedId)
    {
        $followerId = Auth::id();

        $supabaseUser = new SupabaseUserService;

        $supabaseUser->unfollowUser($followerId, $followedId);

        $this->clearFollowCaches($followerId, $followedId);

        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => 'Unfollowed successfully!', 'following' => false]);
        }

        return back()->with('success', 'Unfollowed successfully!');
    }

    private function clearFollowCaches(string $followerId, string $followedId): void
    {
        foreach ([$followerId, $followedId] as $id) {
            Cache::forget("follow_counts:{$id}:followers");
            Cache::forget("follow_counts:{$id}:following");
            app(RecommendationService::class)->clearSuggestionCache($id);
        }
    }
}
