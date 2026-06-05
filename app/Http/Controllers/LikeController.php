<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use App\Services\SupabasePostService;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggleLike(string $postId)
    {
        $userId = Auth::id();

        if ($userId === null) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $supabase = new SupabasePostService;

        if ($supabase->hasLikedPost((string) $userId, $postId)) {
            $supabase->unlikePost((string) $userId, $postId);
            $liked = false;
            $message = 'Unliked successfully!';
        } else {
            $supabase->likePost((string) $userId, $postId);
            $liked = true;
            $message = 'Liked successfully!';
        }

        app(RecommendationService::class)->clearTagCache((string) $userId);

        if (request()->expectsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => true, 'message' => $message, 'liked' => $liked]);
        }

        return back()->with('success', $message);
    }
}
