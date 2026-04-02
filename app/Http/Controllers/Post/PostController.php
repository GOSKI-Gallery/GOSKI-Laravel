<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\CreatePostRequest;
use App\Services\SupabaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        $posts = $this->supabase->getPosts();

        return view('feed', ['posts' => $posts]);
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
                'is_nsfw' => false
            ];
        
            $this->supabase->insert('posts', $record);
        
            return redirect()->route('feed')->with('success', 'Post criado com sucesso!');
        } 
        
        catch (\Exception $e) {
            Log::error('Erro ao criar o post: '.$e->getMessage().' '.$e->getLine().' '.$e->getFile());

            return redirect()->back()->with('error', 'Desculpe, ocorreu um erro ao criar o post. Tente novamente mais tarde.');
        }
    }
}
