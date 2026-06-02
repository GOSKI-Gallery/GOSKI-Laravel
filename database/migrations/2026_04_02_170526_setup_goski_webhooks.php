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
        DB::unprepared("
            CREATE EXTENSION IF NOT EXISTS pg_net SCHEMA extensions;

            CREATE OR REPLACE FUNCTION public.trigger_image_moderation()
            RETURNS TRIGGER 
            SECURITY DEFINER
            SET search_path = public
            LANGUAGE plpgsql
            AS $$
            BEGIN
                PERFORM net.http_post(
                    url := '".env('SUPABASE_URL')."/functions/v1/image-moderator',
                    headers := jsonb_build_object(
                        'Content-Type', 'application/json',
                        'Authorization', 'Bearer ".env('SUPABASE_SERVICE_ROLE_KEY')."'
                    ),
                    body := jsonb_build_object('record', row_to_json(NEW))
                );
                RETURN NEW;
            END;
            $$;

            DROP TRIGGER IF EXISTS on_post_created_moderation ON public.posts;
            CREATE TRIGGER on_post_created_moderation
            AFTER INSERT ON public.posts
            FOR EACH ROW
            EXECUTE FUNCTION public.trigger_image_moderation();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('
            DROP TRIGGER IF EXISTS on_post_created_moderation ON public.posts;
            DROP FUNCTION IF EXISTS public.trigger_image_moderation();
        ');
    }
};
