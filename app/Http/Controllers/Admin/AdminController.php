<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Services\SupabaseAuthService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalPosts = Post::count();

        $pendingPosts = Post::with('users')
            ->where('moderation_status', 'POSSIBLE')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact('totalUsers', 'totalPosts', 'pendingPosts'));
    }

    public function approvePost($id)
    {
        $post = Post::findOrFail($id);
        $post->moderation_status = 'VERY_UNLIKELY';
        $post->save();

        return redirect()->route('admin.dashboard')->with('success', 'Post aprovado com sucesso.');
    }

    public function destroyPost($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Post deletado com sucesso.');
    }

    public function index()
    {
        $users = User::withCount(['posts', 'followers', 'following'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function detail($id)
    {
        $user = User::withCount(['posts', 'followers', 'following'])
            ->findOrFail($id);

        return view('admin.users.detail', compact('user'));
    }

    public function remove($id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.remove', compact('user'));
    }

    public function destroy($id, Request $request, SupabaseAuthService $authService)
    {
        $user = User::findOrFail($id);

        // Verifica se o username foi digitado corretamente
        if ($request->input('username') !== $user->username) {
            return redirect()->back()->withErrors(['username' => 'Username incorreto']);
        }

        try {
            // Deleta do Supabase Auth
            $authService->deleteUser($user->id);

            // Deleta do banco de dados
            $user->delete();

            return redirect()->route('admin.users.index')->with('success', 'Usuário deletado com sucesso');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erro ao deletar usuário: '.$e->getMessage()]);
        }
    }

    public function postsIndex()
    {
        $posts = Post::with('users')
            ->withCount('likes')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.posts.index', compact('posts'));
    }

    public function postsDetail($id)
    {
        $post = Post::with('users')
            ->withCount('likes')
            ->findOrFail($id);

        return view('admin.posts.detail', compact('post'));
    }

    public function delete(Request $request)
    {
        if ($request->has('id')) {
            return redirect()->back()->with('error', 'Erro ao deletar o usuario. Tente novamente');
        } else {
            return redirect()->route('admin.users.index')->with('success', 'Usuario deletado com sucesso');
        }
    }
}
