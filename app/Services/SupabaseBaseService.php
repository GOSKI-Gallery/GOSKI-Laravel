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
        $this->url = rtrim(config('supabase.url'), '/');
        $this->key = config('supabase.service_role_key');
        $this->anonKey = config('supabase.anon_key');
    }

    protected function client(bool $useServiceKey = true)
    {
        $token = $useServiceKey ? $this->key : $this->anonKey;

        return Http::withHeaders([
            'apikey' => $this->anonKey,
            'Authorization' => "Bearer {$token}",
            'Accept-Profile' => 'laravel',
            'Content-Profile' => 'laravel',
        ]);
    }
}
