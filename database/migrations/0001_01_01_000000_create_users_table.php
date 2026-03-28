<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('profile_photo_url')->nullable();
            $table->timestamps();
            $table->rememberToken();
            
            $table->foreign('id')->references('id')->on('auth.users')->onDelete('cascade');
        });

        DB::unprepared("
            -- 1. Ativar RLS
            ALTER TABLE public.users ENABLE ROW LEVEL SECURITY;
            CREATE POLICY \"Perfis visíveis por todos\" ON public.users FOR SELECT USING (true);
            CREATE POLICY \"Usuários editam o próprio perfil\" ON public.users FOR UPDATE USING (auth.uid() = id);

            -- 2. Trigger de Sincronização (Auth -> Public.Users)
            CREATE OR REPLACE FUNCTION public.handle_new_user()
            RETURNS trigger LANGUAGE plpgsql SECURITY DEFINER SET search_path = public AS $$
            BEGIN
                INSERT INTO public.users (id, email, username, created_at, updated_at)
                VALUES (
                    new.id, 
                    new.email, 
                    COALESCE(new.raw_user_meta_data->>'username', split_part(new.email, '@', 1)),
                    now(), 
                    now()
                );
                RETURN new;
            END; $$;

            CREATE OR REPLACE TRIGGER on_auth_user_created
            AFTER INSERT ON auth.users FOR EACH ROW EXECUTE PROCEDURE public.handle_new_user();

            -- 3. Buckets de Storage
            INSERT INTO storage.buckets (id, name, public) VALUES ('profiles', 'profiles', true) ON CONFLICT (id) DO NOTHING;
            CREATE POLICY \"Storage perfis público\" ON storage.objects FOR SELECT USING (bucket_id = 'profiles');
            CREATE POLICY \"Upload perfil autenticado\" ON storage.objects FOR INSERT WITH CHECK (bucket_id = 'profiles' AND auth.role() = 'authenticated');
        ");
    }

    public function down(): void {
        DB::unprepared('DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users; DROP FUNCTION IF EXISTS public.handle_new_user;');
        Schema::dropIfExists('users');
    }
};