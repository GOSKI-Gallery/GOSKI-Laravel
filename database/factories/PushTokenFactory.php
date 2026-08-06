<?php

namespace Database\Factories;

use App\Models\PushToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PushTokenFactory extends Factory
{
    protected $model = PushToken::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'token' => 'ExponentPushToken['.fake()->bothify(str_repeat('?', 22)).']',
            'platform' => fake()->randomElement(['ios', 'android']),
        ];
    }
}
