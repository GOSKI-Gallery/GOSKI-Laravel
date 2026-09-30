<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PerformanceIndexesMigrationTest extends TestCase
{
    use RefreshDatabase;

    private function migrationFile(): ?string
    {
        $matches = glob(database_path('migrations/*_add_performance_indexes.php'));

        return $matches === false || $matches === [] ? null : $matches[0];
    }

    private function resolveMigration(): Migration
    {
        $file = $this->migrationFile();
        $this->assertNotNull($file, 'Performance indexes migration file not found.');

        $migration = require $file;
        $this->assertInstanceOf(Migration::class, $migration);

        return $migration;
    }

    /** @return array<int, string> */
    private function indexNames(string $table): array
    {
        return collect(Schema::getIndexes($table))->pluck('name')->all();
    }

    public function test_performance_indexes_exist(): void
    {
        $this->assertContains('comments_post_id_index', $this->indexNames('comments'));
        $this->assertContains('likes_post_id_index', $this->indexNames('likes'));
        $this->assertContains('follows_followed_id_index', $this->indexNames('follows'));
        $this->assertContains('posts_user_id_created_at_index', $this->indexNames('posts'));
        $this->assertContains('posts_created_at_index', $this->indexNames('posts'));
    }

    public function test_tables_remain_usable_after_down_up_cycle(): void
    {
        $migration = $this->resolveMigration();

        $migration->down();

        $this->assertNotContains('posts_created_at_index', $this->indexNames('posts'));

        $migration->up();

        $this->assertContains('posts_created_at_index', $this->indexNames('posts'));

        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        Comment::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'body' => 'Great spot!',
        ]);

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
        $this->assertDatabaseHas('comments', ['post_id' => $post->id, 'body' => 'Great spot!']);

        $latest = DB::table('posts')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $this->assertNotNull($latest);
        $this->assertSame($post->id, $latest->id);
    }
}
