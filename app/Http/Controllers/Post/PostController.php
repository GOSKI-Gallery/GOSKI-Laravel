<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\CreatePostRequest;
use App\Models\Post;
use App\Services\RecommendationService;
use App\Services\SupabasePostService;
use App\Services\SupabaseUserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $supabaseUser = new SupabaseUserService;

        $paginator = $this->recommendation->getRankedFeed($user);
        $paginator->getCollection()->loadCount('likes');

        foreach ($paginator->items() as $post) {
            $post->setAttribute('is_liked_by_user', $supabaseUser->hasLikedPost($user->id, (string) $post->id));
            $post->setAttribute('is_followed_by_user', $post->is_following ?? $supabaseUser->isFollowing($user->id, $post->users['id']));
        }

        if (request()->ajax()) {
            return view('components.feed.posts.list', ['posts' => $paginator->items()]);
        }

        $userPosts = Post::where('user_id', $user->id)->latest()->take(9)->get();

        $suggestedUsers = $this->recommendation->getSuggestedUsers($user);

        return view('feed', [
            'posts' => $paginator->items(),
            'userPosts' => $userPosts,
            'suggestedUsers' => $suggestedUsers,
            'followersCount' => $supabaseUser->getFollowCount($user->id, 'followers'),
            'followingCount' => $supabaseUser->getFollowCount($user->id, 'following'),
        ]);
    }

    public function store(CreatePostRequest $request)
    {
        $data = $request->validated();
        $file = $request->file('image_url');

        try {
            $fileName = 'post_'.time().'_'.uniqid().'.'.$file->extension();

            $this->supabase->uploadImage('posts', $fileName, $file);

            $publicUrl = $this->supabase->getPublicUrl('posts', $fileName);

            $record = [
                'user_id' => Auth::id(),
                'description' => $data['description'],
                'image_url' => $publicUrl,
                'is_nsfw' => false,
            ];

            $this->supabase->insert('posts', $record);

            return redirect()->route('feed')->with('success', 'Post criado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar o post: '.$e->getMessage().' '.$e->getLine().' '.$e->getFile());

            return redirect()->back()->with('error', 'Desculpe, ocorreu um erro ao criar o post. Tente novamente mais tarde.');
        }
    }
}
