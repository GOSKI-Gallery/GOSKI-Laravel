<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'image_url' => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5',
            'description' => 'Processando imagem...',
            'moderation_status' => 'pending',
        ];
    }
}