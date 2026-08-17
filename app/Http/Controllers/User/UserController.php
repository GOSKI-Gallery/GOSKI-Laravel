<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\EditUserRequest;
use App\Http\Requests\User\RegisterUserRequest;
use App\Models\Post;
use App\Services\SupabaseAuthService;
use App\Services\SupabaseUserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function register(RegisterUserRequest $request, SupabaseAuthService $supabaseAuth)
    {
        $response = $supabaseAuth->signUp(
            $request->email,
            $request->password,
            $request->username
        );

        if (isset($response['error_description']) || isset($response['error'])) {
            return back()->withInput()->withErrors([
                'supabase' => $response['error_description'] ?? $response['error']['message'],
            ]);
        }

        return redirect()->route('landingPage')->with('success', 'Conta criada!');
    }

    public function profile()
    {
        $userId = Auth::id();
        $userPosts = Post::where('user_id', $userId)->latest()->take(9)->get();

        return view('profile', [
            'profileUser' => Auth::user(),
            'isOwnProfile' => true,
            'userPosts' => $userPosts,
            'followersCount' => $this->followCount($userId, 'followers'),
            'followingCount' => $this->followCount($userId, 'following'),
        ]);
    }

    public function show(string $userId)
    {
        $supabase = new SupabaseUserService;
        $profileUser = $supabase->getUserById($userId);

        if (! $profileUser) {
            abort(404);
        }

        $userPosts = Post::where('user_id', $userId)->latest()->take(9)->get();
        $isOwnProfile = Auth::id() === $userId;

        return view('profile', [
            'profileUser' => $profileUser,
            'isOwnProfile' => $isOwnProfile,
            'userPosts' => $userPosts,
            'followersCount' => $this->followCount($userId, 'followers'),
            'followingCount' => $this->followCount($userId, 'following'),
            'isFollowed' => $isOwnProfile ? true : $this->isFollowing(Auth::id(), $userId),
        ]);
    }

    private function followCount(string $userId, string $type = 'followers'): int
    {
        $prefix = DB::getDriverName() === 'pgsql' ? 'laravel.' : '';
        $column = $type === 'followers' ? 'followed_id' : 'follower_id';

        return DB::table($prefix.'follows')->where($column, $userId)->count();
    }

    private function isFollowing(string $followerId, string $followedId): bool
    {
        $prefix = DB::getDriverName() === 'pgsql' ? 'laravel.' : '';

        return DB::table($prefix.'follows')
            ->where('follower_id', $followerId)
            ->where('followed_id', $followedId)
            ->exists();
    }

    public function update(EditUserRequest $request, SupabaseAuthService $supabaseAuth)
    {
        $validatedData = $request->validated();
        $userId = Auth::id();

        $response = $supabaseAuth->updateUser($userId, $validatedData, $request->hasFile('profile_photo_url') ? $request->file('profile_photo_url') : null);

        if (isset($response['error'])) {
            return back()->withErrors(['supabase' => $response['error']['message']]);
        }

        $user = Auth::user();
        $user->refresh();

        return redirect()->route('profile')->with('success', 'Perfil atualizado com sucesso!');
    }
}
