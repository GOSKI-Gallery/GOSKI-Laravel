<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $managementToken = config('supabase.management_token');

        if (! $managementToken) {
            $this->warnMigration('SUPABASE_MANAGEMENT_TOKEN not set. Add the "laravel" schema manually in Supabase Dashboard > Project Settings > API > Exposed schemas.');

            return;
        }

        $projectRef = $this->extractProjectRef(config('supabase.url'));

        if (! $projectRef) {
            $this->warnMigration('Could not extract project ref from SUPABASE_URL.');

            return;
        }

        $response = Http::withToken($managementToken)
            ->patch("https://api.supabase.com/v1/projects/{$projectRef}/postgrest/config", [
                'db_schemas' => 'public,graphql_public,laravel',
            ]);

        if ($response->successful()) {
            $this->infoMigration('Schema "laravel" added to PostgREST exposed schemas.');
        } else {
            $this->warnMigration('Failed to update PostgREST config: '.$response->body());
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $managementToken = config('supabase.management_token');

        if (! $managementToken) {
            return;
        }

        $projectRef = $this->extractProjectRef(config('supabase.url'));

        if (! $projectRef) {
            return;
        }

        $response = Http::withToken($managementToken)
            ->patch("https://api.supabase.com/v1/projects/{$projectRef}/postgrest/config", [
                'db_schemas' => 'public,graphql_public',
            ]);

        if ($response->successful()) {
            $this->infoMigration('Schema "laravel" removed from PostgREST exposed schemas.');
        }
    }

    private function extractProjectRef(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        preg_match('/https:\/\/([^.]+)\.supabase\.co/', $url, $matches);

        return $matches[1] ?? null;
    }

    private function warnMigration(string $message): void
    {
        echo "\n  \e[33m⚠ {$message}\e[0m\n\n";
    }

    private function infoMigration(string $message): void
    {
        echo "\n  \e[32m✓ {$message}\e[0m\n\n";
    }
};
