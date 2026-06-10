<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\DB;

trait HasSchemaPrefix
{
    public function getTable(): string
    {
        $table = parent::getTable();

        if (DB::getDriverName() !== 'pgsql') {
            return $table;
        }

        if (str_contains($table, '.') || str_starts_with($table, 'laravel_reserved_')) {
            return $table;
        }

        return 'laravel.'.$table;
    }

    public static function qualifyTable(string $table): string
    {
        if (DB::getDriverName() === 'pgsql') {
            return 'laravel.'.$table;
        }

        return $table;
    }
}
