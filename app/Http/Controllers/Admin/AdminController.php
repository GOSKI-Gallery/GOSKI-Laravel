<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalPosts = Post::count();

        $pendingPosts = Post::with('users')
            ->where('moderation_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact('totalUsers', 'totalPosts', 'pendingPosts'));
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

    public function remove($id){
        $user = User::findOrFail($id);
        return view('admin.users.remove', compact('user'));
    }

    public function delete(Request $request){
        if ($request->has('id')) {
            return redirect()->back()->with('error', 'Erro ao deletar o usuario. Tente novamente');
        }

        else {
            return redirect()->route('admin.users.index')->with('success', 'Usuario deletado com sucesso');
        }
    }
}