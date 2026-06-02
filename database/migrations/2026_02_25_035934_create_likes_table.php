<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'post_id']);
        });

        DB::unprepared('
            ALTER TABLE public.likes ENABLE ROW LEVEL SECURITY;
            CREATE POLICY "Likes públicos" ON public.likes FOR SELECT USING (true);
            CREATE POLICY "Dono gerencia like" ON public.likes FOR ALL USING (auth.uid() = user_id);
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
