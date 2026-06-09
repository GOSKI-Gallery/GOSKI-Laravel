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

        Schema::create($prefix.'post_tag', function (Blueprint $table) use ($prefix) {
            $table->foreignId('post_id')->constrained($prefix.'posts')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained($prefix.'tags')->onDelete('cascade');
            $table->float('confidence')->nullable();
            $table->timestamps();

            $table->primary(['post_id', 'tag_id']);
        });

        if ($driver === 'pgsql') {
            DB::unprepared("
                ALTER TABLE laravel.post_tag ENABLE ROW LEVEL SECURITY;
                DO \$\$
                BEGIN
                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Leitura pública post_tag' AND tablename = 'post_tag') THEN
                        CREATE POLICY \"Leitura pública post_tag\" ON laravel.post_tag FOR SELECT USING (true);
                    END IF;
                END \$\$;
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        $prefix = $driver === 'pgsql' ? 'laravel.' : '';

        Schema::dropIfExists($prefix.'post_tag');
    }
};
