<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->error("Rode o UserSeeder primeiro!");
            return;
        }

        $images = [
            'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5',
            'https://images.unsplash.com/photo-1541963463532-d68292c34b19',
        ];

        foreach ($images as $url) {
            $this->uploadAndCreatePost($users->random(), $url);
        }
    }

    private function uploadAndCreatePost($user, $externalUrl)
    {
        try {
            $imageContent = Http::get($externalUrl)->body();
            $filename = 'post_' . Str::random(10) . '.jpg';

            $this->command->info("Fazendo upload da imagem {$filename} para o Supabase...");

            $supabaseUrl = str_replace('.supabase.co', '', env('SUPABASE_URL'));
            $uploadUrl = env('SUPABASE_URL') . "/storage/v1/object/posts/{$filename}";

            $uploadResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
                'Content-Type' => 'image/jpeg'
            ])->send('POST', $uploadUrl, [
                'body' => $imageContent
            ]);

            if (!$uploadResponse->successful()) {
                throw new \Exception("Falha no upload para o Storage: " . $uploadResponse->body());
            }

            $internalUrl = env('SUPABASE_URL') . "/storage/v1/object/public/posts/{$filename}";
            
            $post = Post::factory()->create([
                'user_id' => $user->id,
                'image_url' => $internalUrl,
            ]);

            Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY')
            ])->post(env('SUPABASE_FUNCTION_URL'), [
                'record' => $post
            ]);

            $this->command->info("Post #{$post->id} criado e imagem enviada ao Storage!");

        } catch (\Exception $e) {
            $this->command->error("Erro no processo: " . $e->getMessage());
        }
    }
}