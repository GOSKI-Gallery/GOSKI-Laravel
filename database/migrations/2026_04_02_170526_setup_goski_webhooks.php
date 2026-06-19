<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            $hasPgNet = DB::select("SELECT 1 FROM pg_available_extensions WHERE name = 'pg_net'");
            if (empty($hasPgNet)) {
                return;
            }

            DB::unprepared("
                DO \$do\$
                BEGIN
                    CREATE OR REPLACE FUNCTION public.trigger_image_moderation()
                    RETURNS trigger LANGUAGE plpgsql SECURITY DEFINER AS \$\$
                    BEGIN
                        IF NEW.moderation_status IS NULL THEN
                            PERFORM pg_notify('post_moderation', json_build_object('post_id', NEW.id, 'image_url', NEW.image_url)::text);
                        END IF;
                        RETURN NEW;
                    END; \$\$;

                    DROP TRIGGER IF EXISTS on_post_created_moderation ON laravel.posts;
                    CREATE TRIGGER on_post_created_moderation
                    AFTER INSERT ON laravel.posts
                    FOR EACH ROW
                    EXECUTE FUNCTION public.trigger_image_moderation();
                END \$do\$;
            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::unprepared('
                DROP TRIGGER IF EXISTS on_post_created_moderation ON laravel.posts;
                DROP FUNCTION IF EXISTS public.trigger_image_moderation();
            ');
        }
    }
};
