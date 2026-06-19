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

        Schema::create($prefix.'posts', function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->foreignUuid('user_id')->constrained($prefix.'users')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('image_url');
            $table->boolean('is_nsfw')->default(false);
            $table->string('moderation_status')->nullable();
            $table->timestamps();
        });

        if ($driver === 'pgsql') {
            DB::unprepared("
                DO \$\$
                BEGIN
                    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = 'laravel' AND table_name = 'posts') THEN
                        ALTER TABLE laravel.posts ENABLE ROW LEVEL SECURITY;

                        IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Posts aprovados públicos' AND tablename = 'posts') THEN
                            CREATE POLICY \"Posts aprovados públicos\" ON laravel.posts FOR SELECT USING (moderation_status IN ('VERY_UNLIKELY', 'UNLIKELY', 'UNKNOWN'));
                        END IF;

                        IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Dono vê posts pendentes' AND tablename = 'posts') THEN
                            CREATE POLICY \"Dono vê posts pendentes\" ON laravel.posts FOR SELECT USING (auth.uid() = user_id);
                        END IF;

                        IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Usuários criam posts' AND tablename = 'posts') THEN
                            CREATE POLICY \"Usuários criam posts\" ON laravel.posts FOR INSERT WITH CHECK (auth.uid() = user_id);
                        END IF;

                        IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Dono ou Admin deleta' AND tablename = 'posts') THEN
                            CREATE POLICY \"Dono ou Admin deleta\" ON laravel.posts FOR DELETE USING (auth.uid() = user_id OR (auth.jwt() -> 'app_metadata' ->> 'role') = 'admin');
                        END IF;
                    END IF;
                END \$\$;
            ");

            DB::unprepared("
                DO \$do\$
                BEGIN
                    CREATE OR REPLACE FUNCTION public.handle_rejected_post()
                    RETURNS trigger AS \$\$
                    BEGIN
                        IF NEW.is_nsfw THEN DELETE FROM laravel.posts WHERE id = NEW.id; END IF;
                        RETURN NEW;
                    END; \$\$ LANGUAGE plpgsql SECURITY DEFINER;

                    DROP TRIGGER IF EXISTS on_post_moderated ON laravel.posts;
                    CREATE TRIGGER on_post_moderated AFTER UPDATE OF moderation_status ON laravel.posts FOR EACH ROW EXECUTE PROCEDURE public.handle_rejected_post();
                END \$do\$;
            ");

            DB::unprepared("INSERT INTO storage.buckets (id, name, public) VALUES ('posts', 'posts', true) ON CONFLICT (id) DO NOTHING;");

            DB::unprepared("
                DO \$\$
                BEGIN
                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Posts público' AND tablename = 'objects') THEN
                        CREATE POLICY \"Posts público\" ON storage.objects FOR SELECT USING (bucket_id = 'posts');
                    END IF;

                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Upload posts autenticado' AND tablename = 'objects') THEN
                        CREATE POLICY \"Upload posts autenticado\" ON storage.objects FOR INSERT WITH CHECK (bucket_id = 'posts' AND auth.role() = 'authenticated');
                    END IF;
                END \$\$;
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        $prefix = $driver === 'pgsql' ? 'laravel.' : '';

        Schema::dropIfExists($prefix.'posts');
    }
};
