<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterUserRequest;
use App\Models\Post;
use App\Services\SupabaseService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(RegisterUserRequest $request, SupabaseService $supabase)
    {
        $response = $supabase->signUp(
            $request->email,
            $request->password,
            $request->username
        );

        if (isset($response['error_description']) || isset($response['error'])) {
            return back()->withInput()->withErrors([
                'supabase' => $response['error_description'] ?? $response['error']['message']
            ]);
        }

        return redirect()->route('landingPage')->with('success', 'Conta criada!');
    }

    public function profile()
    {
        $supabase = new SupabaseService();
        $userId = Auth::id();
        $userPosts = Post::where('user_id', $userId)->latest()->take(9)->get();

        return view('profile', [
            'profileUser' => Auth::user(),
            'isOwnProfile' => true,
            'userPosts' => $userPosts,
            'followersCount' => $supabase->getFollowCount($userId, 'followers'),
            'followingCount' => $supabase->getFollowCount($userId, 'following'),
        ]);
    }

    public function show(string $userId)
    {
        $supabase = new SupabaseService();
        $profileUser = $supabase->getUserById($userId);

        if (!$profileUser) {
            abort(404);
        }

        $userPosts = Post::where('user_id', $userId)->latest()->take(9)->get();
        $isOwnProfile = Auth::id() === $userId;

        return view('profile', [
            'profileUser' => $profileUser,
            'isOwnProfile' => $isOwnProfile,
            'userPosts' => $userPosts,
            'followersCount' => $supabase->getFollowCount($userId, 'followers'),
            'followingCount' => $supabase->getFollowCount($userId, 'following'),
            'isFollowed' => $isOwnProfile ? true : $supabase->isFollowing(Auth::id(), $userId),
        ]);
    }
}
