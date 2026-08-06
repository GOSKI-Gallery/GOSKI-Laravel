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

        Schema::create($prefix.'push_tokens', function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->foreignUuid('user_id')->constrained($prefix.'users')->onDelete('cascade');
            $table->string('token');
            $table->string('platform')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'token']);
            $table->index('user_id');
        });

        if ($driver === 'pgsql') {
            DB::unprepared("
                ALTER TABLE laravel.push_tokens ENABLE ROW LEVEL SECURITY;
                DO \$\$
                BEGIN
                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Usuario insere seu token' AND tablename = 'push_tokens') THEN
                        CREATE POLICY \"Usuario insere seu token\" ON laravel.push_tokens FOR INSERT WITH CHECK (auth.uid() = user_id);
                    END IF;
                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Usuario atualiza seu token' AND tablename = 'push_tokens') THEN
                        CREATE POLICY \"Usuario atualiza seu token\" ON laravel.push_tokens FOR UPDATE USING (auth.uid() = user_id) WITH CHECK (auth.uid() = user_id);
                    END IF;
                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Usuario le seus tokens' AND tablename = 'push_tokens') THEN
                        CREATE POLICY \"Usuario le seus tokens\" ON laravel.push_tokens FOR SELECT USING (auth.uid() = user_id);
                    END IF;
                END \$\$;
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        $prefix = $driver === 'pgsql' ? 'laravel.' : '';

        Schema::dropIfExists($prefix.'push_tokens');
    }
};
