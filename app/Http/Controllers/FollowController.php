<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function follow(Request $request, string $followedId)
    {
        $followerId = Auth::id();

        if ($followerId === $followedId) {
            return back()->with('error', 'Voc\u00ea n\u00e3o pode seguir a si mesmo.');
        }

        $supabase = new SupabaseService();
        $supabase->followUser($followerId, $followedId);

        return back()->with('success', 'Followed successfully!');
    }

    public function unfollow(Request $request, string $followedId)
    {
        $followerId = Auth::id();

        $supabase = new SupabaseService();
        $supabase->unfollowUser($followerId, $followedId);

        return back()->with('success', 'Unfollowed successfully!');
    }
}
