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

        $roles = config('supabase.exposed_roles', 'anon, authenticated, service_role');

        DB::unprepared("
            GRANT USAGE ON SCHEMA laravel TO {$roles};
            GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA laravel TO {$roles};
            GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA laravel TO {$roles};
            ALTER DEFAULT PRIVILEGES IN SCHEMA laravel GRANT ALL PRIVILEGES ON TABLES TO {$roles};
            ALTER DEFAULT PRIVILEGES IN SCHEMA laravel GRANT ALL PRIVILEGES ON SEQUENCES TO {$roles};
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $roles = config('supabase.exposed_roles', 'anon, authenticated, service_role');

        DB::unprepared("
            ALTER DEFAULT PRIVILEGES IN SCHEMA laravel REVOKE ALL PRIVILEGES ON SEQUENCES FROM {$roles};
            ALTER DEFAULT PRIVILEGES IN SCHEMA laravel REVOKE ALL PRIVILEGES ON TABLES FROM {$roles};
            REVOKE ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA laravel FROM {$roles};
            REVOKE ALL PRIVILEGES ON ALL TABLES IN SCHEMA laravel FROM {$roles};
            REVOKE USAGE ON SCHEMA laravel FROM {$roles};
        ");
    }
};
