<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\PushToken;
use App\Models\Tag;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function test_post_fillable_attributes(): void
    {
        $post = new Post;
        $this->assertEquals([
            'description', 'image_url', 'latitude', 'longitude', 'location_name', 'is_nsfw', 'moderation_status', 'user_id',
        ], $post->getFillable());
    }

    public function test_post_default_properties(): void
    {
        $post = new Post;
        $this->assertNull($post->likes_count);
        $this->assertNull($post->is_liked_by_user);
        $this->assertNull($post->is_followed_by_user);
    }

    public function test_user_fillable_attributes(): void
    {
        $user = new User;
        $this->assertEquals(['username', 'email', 'role'], $user->getFillable());
    }

    public function test_user_hidden_attributes(): void
    {
        $user = new User;
        $this->assertEquals(['password', 'remember_token'], $user->getHidden());
    }

    public function test_user_key_type(): void
    {
        $user = new User;
        $this->assertEquals('string', $user->getKeyType());
    }

    public function test_user_casts(): void
    {
        $user = new User;
        $casts = $user->getCasts();
        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);
    }

    public function test_tag_fillable_attributes(): void
    {
        $tag = new Tag;
        $this->assertEquals(['name'], $tag->getFillable());
    }

    public function test_push_token_fillable_attributes(): void
    {
        $token = new PushToken;
        $this->assertEquals(['user_id', 'token', 'platform'], $token->getFillable());
    }
}
