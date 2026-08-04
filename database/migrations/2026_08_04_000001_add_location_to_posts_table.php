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

        Schema::table($prefix.'posts', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('image_url');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('location_name', 255)->nullable()->after('longitude');

            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        $prefix = $driver === 'pgsql' ? 'laravel.' : '';

        Schema::table($prefix.'posts', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropColumn(['latitude', 'longitude', 'location_name']);
        });
    }
};
