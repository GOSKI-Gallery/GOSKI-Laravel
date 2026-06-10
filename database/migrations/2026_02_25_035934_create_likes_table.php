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

        Schema::create($prefix.'likes', function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->foreignUuid('user_id')->constrained($prefix.'users')->onDelete('cascade');
            $table->foreignId('post_id')->constrained($prefix.'posts')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'post_id']);
        });

        if ($driver === 'pgsql') {
            DB::unprepared("
                ALTER TABLE laravel.likes ENABLE ROW LEVEL SECURITY;
                DO \$\$
                BEGIN
                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Likes públicos' AND tablename = 'likes') THEN
                        CREATE POLICY \"Likes públicos\" ON laravel.likes FOR SELECT USING (true);
                    END IF;
                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Dono gerencia like' AND tablename = 'likes') THEN
                        CREATE POLICY \"Dono gerencia like\" ON laravel.likes FOR ALL USING (auth.uid() = user_id);
                    END IF;
                END \$\$;
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        $prefix = $driver === 'pgsql' ? 'laravel.' : '';

        Schema::dropIfExists($prefix.'likes');
    }
};
