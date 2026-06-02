<?php

namespace App\Http\Controllers;

use App\Services\SupabasePostService;
use App\Services\SupabaseUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $supabase = new SupabasePostService;
        $supabaseUser = new SupabaseUserService;

        $supabaseUser->followUser($followerId, $followedId);

        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => 'Followed successfully!', 'following' => true]);
        }

        return back()->with('success', 'Followed successfully!');
    }

    public function unfollow(Request $request, string $followedId)
    {
        $followerId = Auth::id();

        $supabase = new SupabasePostService;
        $supabaseUser = new SupabaseUserService;

        $supabaseUser->unfollowUser($followerId, $followedId);

        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => 'Unfollowed successfully!', 'following' => false]);
        }

        return back()->with('success', 'Unfollowed successfully!');
    }
}
