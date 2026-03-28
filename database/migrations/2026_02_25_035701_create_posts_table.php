<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('image_url');
            $table->boolean('is_nsfw')->default(false);
            $table->string('moderation_status')->default('pending'); 
            $table->timestamps();
        });

        DB::unprepared("
            -- 1. RLS Posts
            ALTER TABLE public.posts ENABLE ROW LEVEL SECURITY;
            CREATE POLICY \"Posts aprovados públicos\" ON public.posts FOR SELECT USING (moderation_status = 'approved');
            CREATE POLICY \"Dono vê posts pendentes\" ON public.posts FOR SELECT USING (auth.uid() = user_id);
            CREATE POLICY \"Usuários criam posts\" ON public.posts FOR INSERT WITH CHECK (auth.uid() = user_id);
            CREATE POLICY \"Dono ou Admin deleta\" ON public.posts FOR DELETE USING (auth.uid() = user_id OR (auth.jwt() -> 'app_metadata' ->> 'role') = 'admin');

            -- 2. Trigger Moderação (Auto-delete se rejected)
            CREATE OR REPLACE FUNCTION public.handle_rejected_post()
            RETURNS trigger AS $$
            BEGIN
                IF NEW.moderation_status = 'rejected' THEN DELETE FROM public.posts WHERE id = NEW.id; END IF;
                RETURN NEW;
            END; $$ LANGUAGE plpgsql SECURITY DEFINER;

            CREATE OR REPLACE TRIGGER on_post_moderated AFTER UPDATE OF moderation_status ON public.posts FOR EACH ROW EXECUTE PROCEDURE public.handle_rejected_post();

            -- 3. Buckets Storage Posts
            INSERT INTO storage.buckets (id, name, public) VALUES ('posts', 'posts', true) ON CONFLICT (id) DO NOTHING;
            CREATE POLICY \"Posts público\" ON storage.objects FOR SELECT USING (bucket_id = 'posts');
            CREATE POLICY \"Upload posts autenticado\" ON storage.objects FOR INSERT WITH CHECK (bucket_id = 'posts' AND auth.role() = 'authenticated');
        ");
    }

    public function down(): void {
        Schema::dropIfExists('posts');
    }
};