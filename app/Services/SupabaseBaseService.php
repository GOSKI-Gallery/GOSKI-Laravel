<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

abstract class SupabaseBaseService
{
    protected string $url;
    protected string $key;
    protected string $anonKey;

    public function __construct()
    {
        $this->url = rtrim(env('SUPABASE_URL'), '/');
        $this->key = env('SUPABASE_SERVICE_ROLE_KEY');
        $this->anonKey = env('SUPABASE_ANON_KEY');
    }

    protected function client(bool $useServiceKey = true)
    {
        $token = $useServiceKey ? $this->key : $this->anonKey;

        return Http::withHeaders([
            'apikey' => $this->anonKey,
            'Authorization' => "Bearer {$token}",
        ]);
    }
}