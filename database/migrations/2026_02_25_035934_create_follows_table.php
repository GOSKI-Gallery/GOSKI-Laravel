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

        Schema::create($prefix.'follows', function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->foreignUuid('follower_id')->constrained($prefix.'users')->onDelete('cascade');
            $table->foreignUuid('followed_id')->constrained($prefix.'users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['follower_id', 'followed_id']);
        });

        if ($driver === 'pgsql') {
            DB::unprepared("
                ALTER TABLE laravel.follows ENABLE ROW LEVEL SECURITY;
                DO \$\$
                BEGIN
                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Follows públicos' AND tablename = 'follows') THEN
                        CREATE POLICY \"Follows públicos\" ON laravel.follows FOR SELECT USING (true);
                    END IF;
                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Dono gerencia follow' AND tablename = 'follows') THEN
                        CREATE POLICY \"Dono gerencia follow\" ON laravel.follows FOR ALL USING (auth.uid() = follower_id);
                    END IF;
                END \$\$;
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        $prefix = $driver === 'pgsql' ? 'laravel.' : '';

        Schema::dropIfExists($prefix.'follows');
    }
};
