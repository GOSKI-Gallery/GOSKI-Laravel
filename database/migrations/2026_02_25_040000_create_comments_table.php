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

        Schema::create($prefix.'comments', function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->foreignUuid('user_id')->constrained($prefix.'users')->onDelete('cascade');
            $table->foreignId('post_id')->constrained($prefix.'posts')->onDelete('cascade');
            $table->text('body');
            $table->timestamps();
        });

        if ($driver === 'pgsql') {
            DB::unprepared("
                ALTER TABLE laravel.comments ENABLE ROW LEVEL SECURITY;
                DO \$\$
                BEGIN
                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Comments públicos' AND tablename = 'comments') THEN
                        CREATE POLICY \"Comments públicos\" ON laravel.comments FOR SELECT USING (true);
                    END IF;
                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Dono gerencia comment' AND tablename = 'comments') THEN
                        CREATE POLICY \"Dono gerencia comment\" ON laravel.comments FOR ALL USING (auth.uid() = user_id);
                    END IF;
                END \$\$;
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        $prefix = $driver === 'pgsql' ? 'laravel.' : '';

        Schema::dropIfExists($prefix.'comments');
    }
};
