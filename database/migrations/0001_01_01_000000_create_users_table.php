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

        Schema::create($prefix.'users', function (Blueprint $table) use ($driver) {
            $table->uuid('id')->primary();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('role')->nullable()->after('email');
            $table->string('profile_photo_url')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->rememberToken();

            if ($driver === 'pgsql') {
                $table->foreign('id')->references('id')->on('auth.users')->onDelete('cascade');
            }
        });

        if ($driver === 'pgsql' && app()->environment('local', 'testing', 'development')) {
            DB::unprepared("
                DO \$\$
                BEGIN
                    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = 'auth' AND table_name = 'users') THEN
                        DELETE FROM auth.users;
                    END IF;
                END \$\$;
            ");
        }

        if ($driver === 'pgsql') {

            DB::unprepared("
                DO \$\$
                BEGIN
                    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = 'laravel' AND table_name = 'users') THEN
                        ALTER TABLE laravel.users ENABLE ROW LEVEL SECURITY;

                        IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Perfis visíveis por todos' AND tablename = 'users') THEN
                            CREATE POLICY \"Perfis visíveis por todos\" ON laravel.users FOR SELECT USING (true);
                        END IF;

                        IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Usuários editam o próprio perfil' AND tablename = 'users') THEN
                            CREATE POLICY \"Usuários editam o próprio perfil\" ON laravel.users FOR UPDATE USING (auth.uid() = id);
                        END IF;
                    END IF;
                END \$\$;

                CREATE OR REPLACE FUNCTION public.handle_new_user()
                RETURNS trigger LANGUAGE plpgsql SECURITY DEFINER SET search_path = public AS \$\$
                BEGIN
                    INSERT INTO laravel.users (id, email, username, created_at, updated_at)
                    VALUES (new.id, new.email, COALESCE(new.raw_user_meta_data->>'username', split_part(new.email, '@', 1)), now(), now())
                    ON CONFLICT (id) DO NOTHING;
                    RETURN new;
                END; \$\$;

                DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
                CREATE TRIGGER on_auth_user_created AFTER INSERT ON auth.users FOR EACH ROW EXECUTE PROCEDURE public.handle_new_user();

                INSERT INTO storage.buckets (id, name, public) VALUES ('profiles', 'profiles', true) ON CONFLICT (id) DO NOTHING;

                DO \$\$
                BEGIN
                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Storage perfis público' AND tablename = 'objects') THEN
                        CREATE POLICY \"Storage perfis público\" ON storage.objects FOR SELECT USING (bucket_id = 'profiles');
                    END IF;

                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Upload perfil autenticado' AND tablename = 'objects') THEN
                        CREATE POLICY \"Upload perfil autenticado\" ON storage.objects FOR INSERT WITH CHECK (bucket_id = 'profiles' AND auth.role() = 'authenticated');
                    END IF;
                END \$\$;
            ");
        }

        Schema::create('password_reset_tokens', function (Blueprint $table) use ($prefix) {
            $table->id();
            $table->foreignUuid('user_id');
            $table->string('token')->unique();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on($prefix.'users')->onDelete('cascade');
        });

        Schema::create('sessions', function (Blueprint $table) use ($prefix) {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

            $table->foreign('user_id')->references('id')->on($prefix.'users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        $prefix = $driver === 'pgsql' ? 'laravel.' : '';

        if ($driver === 'pgsql') {
            DB::unprepared("
                DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
                DROP FUNCTION IF EXISTS public.handle_new_user;

                DO \$\$
                BEGIN
                    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = 'laravel' AND table_name = 'users') THEN
                        DELETE FROM laravel.users;
                    END IF;
                    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = 'auth' AND table_name = 'users') THEN
                        DELETE FROM auth.users;
                    END IF;
                END \$\$;
            ");
        }

        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists($prefix.'users');
    }
};
