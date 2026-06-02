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
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('follower_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('followed_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['follower_id', 'followed_id']);
        });
        DB::unprepared('
            ALTER TABLE public.follows ENABLE ROW LEVEL SECURITY;
            CREATE POLICY "Follows públicos" ON public.follows FOR SELECT USING (true);
            CREATE POLICY "Dono gerencia follow" ON public.follows FOR ALL USING (auth.uid() = follower_id);
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
