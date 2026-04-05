<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggleLike(string $postId)
    {
        $userId = Auth::id();
        $supabase = new SupabaseService();

        if ($supabase->hasLikedPost($userId, $postId)) {
            $supabase->unlikePost($userId, $postId);
            $message = 'Unliked successfully!';
        } else {
            $supabase->likePost($userId, $postId);
            $message = 'Liked successfully!';
        }

        return back()->with('success', $message);
    }
}
