<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared('
            DO $do$
            BEGIN
                DROP POLICY IF EXISTS "Dono gerencia like" ON laravel.likes;
                CREATE POLICY "Dono gerencia like" ON laravel.likes
                    FOR ALL
                    USING (auth.uid() = user_id)
                    WITH CHECK (auth.uid() = user_id);

                DROP POLICY IF EXISTS "Dono gerencia follow" ON laravel.follows;
                CREATE POLICY "Dono gerencia follow" ON laravel.follows
                    FOR ALL
                    USING (auth.uid() = follower_id)
                    WITH CHECK (auth.uid() = follower_id);
            END $do$;
        ');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared('
            DO $do$
            BEGIN
                DROP POLICY IF EXISTS "Dono gerencia like" ON laravel.likes;
                CREATE POLICY "Dono gerencia like" ON laravel.likes
                    FOR ALL
                    USING (auth.uid() = user_id);

                DROP POLICY IF EXISTS "Dono gerencia follow" ON laravel.follows;
                CREATE POLICY "Dono gerencia follow" ON laravel.follows
                    FOR ALL
                    USING (auth.uid() = follower_id);
            END $do$;
        ');
    }
};
