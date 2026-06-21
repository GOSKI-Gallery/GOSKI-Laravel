<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->error('Rode o UserSeeder primeiro!');

            return;
        }

        $images = [
            'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5',
            'https://images.unsplash.com/photo-1541963463532-d68292c34b19',
            'https://images.unsplash.com/photo-1504208434309-cb69f4fe52b0',
            'https://images.unsplash.com/photo-1524758631624-e2822e304c36',
        ];

        $this->command->info('Criando 4 posts para análise da VisionAI...');

        foreach ($images as $url) {
            $this->uploadAndCreatePost($users->random(), $url);
        }

        $this->command->info('Criando 1 post com status POSSIBLE para revisão manual...');
        $this->createPendingPost($users->random());
    }

    private function uploadAndCreatePost($user, $externalUrl)
    {
        try {
            $imageContent = Http::get($externalUrl)->body();
            $filename = 'post_'.Str::random(10).'.jpg';

            $this->command->info("Fazendo upload da imagem {$filename} para o Supabase...");

            $supabaseUrl = str_replace('.supabase.co', '', env('SUPABASE_URL'));
            $uploadUrl = env('SUPABASE_URL')."/storage/v1/object/posts/{$filename}";

            $uploadResponse = Http::withHeaders([
                'Authorization' => 'Bearer '.env('SUPABASE_SERVICE_ROLE_KEY'),
                'Content-Type' => 'image/jpeg',
            ])->send('POST', $uploadUrl, [
                'body' => $imageContent,
            ]);

            if (! $uploadResponse->successful()) {
                throw new \Exception('Falha no upload para o Storage: '.$uploadResponse->body());
            }

            $internalUrl = env('SUPABASE_URL')."/storage/v1/object/public/posts/{$filename}";

            Post::factory()->create([
                'user_id' => $user->id,
                'image_url' => $internalUrl,
                'description' => 'Post para análise da VisionAI via edge function.',
            ]);

            $this->command->info('Post criado com moderation_status=null — trigger da edge function será ativado.');

        } catch (\Exception $e) {
            $this->command->error('Erro no processo: '.$e->getMessage());
        }
    }

    private function createPendingPost($user)
    {
        $imageUrl = 'https://picsum.photos/seed/nsfw-review/800/800';

        $internalUrl = $imageUrl;

        if (env('SUPABASE_URL')) {
            try {
                $imageContent = Http::timeout(10)->get($imageUrl)->body();
                $filename = 'pending_review_'.Str::random(8).'.jpg';

                $uploadUrl = env('SUPABASE_URL')."/storage/v1/object/posts/{$filename}";

                $uploadResponse = Http::withHeaders([
                    'Authorization' => 'Bearer '.env('SUPABASE_SERVICE_ROLE_KEY'),
                    'Content-Type' => 'image/jpeg',
                ])->send('POST', $uploadUrl, [
                    'body' => $imageContent,
                ]);

                if ($uploadResponse->successful()) {
                    $internalUrl = env('SUPABASE_URL')."/storage/v1/object/public/posts/{$filename}";
                }
            } catch (\Exception $e) {
                $this->command->warn('Upload falhou, usando URL direta: '.$e->getMessage());
            }
        }

        $postId = DB::table((new Post)->getTable())->insertGetId([
            'user_id' => $user->id,
            'image_url' => $internalUrl,
            'is_nsfw' => DB::raw('true'),
            'moderation_status' => 'POSSIBLE',
            'description' => 'Post pendente de revisão manual pelo administrador.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("Post #{$postId} criado com moderation_status=POSSIBLE e is_nsfw=true.");
    }
}
