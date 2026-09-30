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

        $liked = $supabase->toggleLike((string) $userId, $postId);

        app(RecommendationService::class)->clearTagCache((string) $userId);
        app(RecommendationService::class)->clearSuggestionCache((string) $userId);

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
