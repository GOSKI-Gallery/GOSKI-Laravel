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
        } else {
            $supabase->likePost((string) $userId, $postId);
            $liked = true;
        }

        app(RecommendationService::class)->clearTagCache((string) $userId);

        $likesCount = $supabase->getLikeCount($postId);

        if (request()->expectsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $likesCount,
            ]);
        }

        return back()->with('success', $liked ? 'Like adicionado!' : 'Like removido!');
    }
}
