<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\CreatePostRequest;
use App\Models\Post;
use App\Services\LocationService;
use App\Services\RecommendationService;
use App\Services\SupabasePostService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PostController extends Controller
{
    protected SupabasePostService $supabase;

    protected RecommendationService $recommendation;

    public function __construct(SupabasePostService $supabase, RecommendationService $recommendation)
    {
        $this->supabase = $supabase;
        $this->recommendation = $recommendation;
    }

    public function index()
    {
        $user = Auth::user();

        $paginator = $this->recommendation->getRankedFeed($user);

        $posts = $paginator->items();

        $prefix = DB::getDriverName() === 'pgsql' ? 'laravel.' : '';
        $postIds = collect($posts)->pluck('id')->all();

        $likedPostIds = DB::table($prefix.'likes')
            ->where('user_id', $user->id)
            ->whereIn('post_id', $postIds)
            ->pluck('post_id')
            ->all();

        $followedUserIds = DB::table($prefix.'follows')
            ->where('follower_id', $user->id)
            ->pluck('followed_id')
            ->all();

        foreach ($posts as $post) {
            $post->setAttribute('is_liked_by_user', in_array($post->id, $likedPostIds));
            $post->setAttribute('is_followed_by_user', ($post->is_following ?? false) || in_array($post->users['id'] ?? null, $followedUserIds));
        }

        if (request()->ajax()) {
            return view('components.feed.posts.list', ['posts' => $posts]);
        }

        $userPosts = Post::where('user_id', $user->id)->latest()->take(9)->get();

        $suggestedUsers = $this->recommendation->getSuggestedUsers($user);

        return view('feed', [
            'posts' => $posts,
            'userPosts' => $userPosts,
            'suggestedUsers' => $suggestedUsers,
            'followersCount' => DB::table($prefix.'follows')->where('followed_id', $user->id)->count(),
            'followingCount' => DB::table($prefix.'follows')->where('follower_id', $user->id)->count(),
        ]);
    }

    public function store(CreatePostRequest $request)
    {
        $data = $request->validated();
        $file = $request->file('image_url');

        try {
            $fileName = 'post_'.Str::uuid().'.'.$file->extension();

            $this->supabase->uploadImage('posts', $fileName, $file);

            $publicUrl = $this->supabase->getPublicUrl('posts', $fileName);

            $latitude = isset($data['latitude']) ? (float) $data['latitude'] : null;
            $longitude = isset($data['longitude']) ? (float) $data['longitude'] : null;

            $record = [
                'user_id' => Auth::id(),
                'description' => $data['description'],
                'image_url' => $publicUrl,
                'is_nsfw' => false,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'location_name' => app(LocationService::class)->resolveLocationName(
                    $data['location_name'] ?? null,
                    $latitude,
                    $longitude
                ),
            ];

            $this->supabase->insert('posts', $record);

            return redirect()->route('feed')->with('success', 'Post criado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar o post: '.$e->getMessage().' '.$e->getLine().' '.$e->getFile());

            return redirect()->back()->with('error', 'Desculpe, ocorreu um erro ao criar o post. Tente novamente mais tarde.');
        }
    }
}
