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

        Schema::create($prefix.'tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        if ($driver === 'pgsql') {
            DB::unprepared("
                ALTER TABLE laravel.tags ENABLE ROW LEVEL SECURITY;
                DO \$\$
                BEGIN
                    IF NOT EXISTS (SELECT 1 FROM pg_policies WHERE policyname = 'Leitura pública tags' AND tablename = 'tags') THEN
                        CREATE POLICY \"Leitura pública tags\" ON laravel.tags FOR SELECT USING (true);
                    END IF;
                END \$\$;
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        $prefix = $driver === 'pgsql' ? 'laravel.' : '';

        Schema::dropIfExists($prefix.'tags');
    }
};
