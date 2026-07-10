<?php

namespace App\Http\Controllers;

use App\Services\SupabaseCommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(string $postId)
    {
        $supabase = new SupabaseCommentService;

        $comments = $supabase->getComments($postId);

        return response()->json([
            'success' => true,
            'comments' => $comments,
        ]);
    }

    public function store(Request $request, string $postId)
    {
        $userId = Auth::id();

        if ($userId === null) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $supabase = new SupabaseCommentService;

        $comment = $supabase->addComment((string) $userId, $postId, $data['body']);

        if ($comment === null) {
            return response()->json(['success' => false, 'message' => 'Failed to create comment.'], 500);
        }

        $commentCount = $supabase->getCommentCount($postId);

        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'comment' => $comment,
                'comments_count' => $commentCount,
            ]);
        }

        return back()->with('success', 'Comentário adicionado!');
    }

    public function destroy(string $commentId)
    {
        $userId = Auth::id();

        if ($userId === null) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $supabase = new SupabaseCommentService;

        $deleted = $supabase->deleteComment($commentId);

        if (! $deleted) {
            return response()->json(['success' => false, 'message' => 'Failed to delete comment.'], 500);
        }

        if (request()->expectsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
            ]);
        }

        return back()->with('success', 'Comentário removido!');
    }
}
