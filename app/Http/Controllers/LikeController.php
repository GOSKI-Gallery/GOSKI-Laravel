<?php

namespace App\Http\Controllers;

use App\Services\SupabasePostService;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggleLike(string $postId)
    {
        $userId = Auth::id();
        $supabase = new SupabasePostService();

        if ($supabase->hasLikedPost($userId, $postId)) {
            $supabase->unlikePost($userId, $postId);
            $liked = false;
            $message = 'Unliked successfully!';
        } else {
            $supabase->likePost($userId, $postId);
            $liked = true;
            $message = 'Liked successfully!';
        }

        if (request()->expectsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => $message, 'liked' => $liked]);
        }

        return back()->with('success', $message);
    }
}
