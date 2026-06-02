<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_can_be_created(): void
    {
        $tag = Tag::factory()->create();
        $this->assertDatabaseHas('tags', ['id' => $tag->id]);
        $this->assertNotNull($tag->name);
    }

    public function test_post_can_have_tags(): void
    {
        $post = Post::factory()->create();
        $tags = Tag::factory()->count(3)->create();

        $post->tags()->attach($tags->pluck('id')->mapWithKeys(fn ($id) => [$id => ['confidence' => 0.9]]));

        $this->assertCount(3, $post->tags);
    }

    public function test_tag_can_have_posts(): void
    {
        $tag = Tag::factory()->create();
        $posts = Post::factory()->count(2)->create();

        $tag->posts()->attach($posts->pluck('id'), ['confidence' => 0.95]);

        $this->assertCount(2, $tag->posts);
    }

    public function test_tag_post_pivot_has_confidence(): void
    {
        $tag = Tag::factory()->create();
        $post = Post::factory()->create();

        $tag->posts()->attach($post->id, ['confidence' => 0.85]);

        $pivot = $tag->posts()->first()->pivot;
        $this->assertEquals(0.85, $pivot->confidence);
    }
}
