<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        $prefix = $driver === 'pgsql' ? 'laravel.' : '';

        Schema::table($prefix.'comments', function (Blueprint $table) {
            $table->index('post_id', 'comments_post_id_index');
        });

        Schema::table($prefix.'likes', function (Blueprint $table) {
            $table->index('post_id', 'likes_post_id_index');
        });

        Schema::table($prefix.'follows', function (Blueprint $table) {
            $table->index('followed_id', 'follows_followed_id_index');
        });

        Schema::table($prefix.'posts', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'posts_user_id_created_at_index');
            $table->index('created_at', 'posts_created_at_index');
        });
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        $prefix = $driver === 'pgsql' ? 'laravel.' : '';

        Schema::table($prefix.'posts', function (Blueprint $table) {
            $table->dropIndex('posts_created_at_index');
            $table->dropIndex('posts_user_id_created_at_index');
        });

        Schema::table($prefix.'follows', function (Blueprint $table) {
            $table->dropIndex('follows_followed_id_index');
        });

        Schema::table($prefix.'likes', function (Blueprint $table) {
            $table->dropIndex('likes_post_id_index');
        });

        Schema::table($prefix.'comments', function (Blueprint $table) {
            $table->dropIndex('comments_post_id_index');
        });
    }
};
