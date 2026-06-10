<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\DB;

trait HasSchemaPrefix
{
    public function getTable(): string
    {
        $table = parent::getTable();

        if (DB::getDriverName() === 'pgsql' && ! str_starts_with($table, 'laravel.')) {
            return 'laravel.'.$table;
        }

        return $table;
    }

    public static function qualifyTable(string $table): string
    {
        if (DB::getDriverName() === 'pgsql') {
            return 'laravel.'.$table;
        }

        return $table;
    }
}
