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

        $hasPgNet = DB::select("SELECT 1 FROM pg_available_extensions WHERE name = 'pg_net'");
        if (empty($hasPgNet)) {
            return;
        }

        $supabaseUrl = rtrim(config('supabase.url'), '/');
        $functionUrl = $supabaseUrl.'/functions/v1/image-moderator';
        $serviceKey = config('supabase.service_role_key');

        DB::unprepared("
            DO \$do\$
            BEGIN
                DROP TRIGGER IF EXISTS on_post_created_moderation ON laravel.posts;
                DROP FUNCTION IF EXISTS public.trigger_image_moderation();

                CREATE OR REPLACE FUNCTION public.trigger_image_moderation()
                RETURNS trigger LANGUAGE plpgsql SECURITY DEFINER AS \$\$
                BEGIN
                    IF NEW.moderation_status IS NULL THEN
                        PERFORM net.http_post(
                            url := '{$functionUrl}',
                            headers := jsonb_build_object(
                                'Content-Type', 'application/json',
                                'Authorization', 'Bearer {$serviceKey}'
                            ),
                            body := jsonb_build_object('record', row_to_json(NEW))::text,
                            timeout_milliseconds := 30000
                        );
                    END IF;
                    RETURN NEW;
                END; \$\$;

                CREATE TRIGGER on_post_created_moderation
                AFTER INSERT ON laravel.posts
                FOR EACH ROW
                EXECUTE FUNCTION public.trigger_image_moderation();
            END \$do\$
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared('
            DROP TRIGGER IF EXISTS on_post_created_moderation ON laravel.posts;
            DROP FUNCTION IF EXISTS public.trigger_image_moderation();
        ');
    }
};
