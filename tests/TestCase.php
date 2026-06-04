<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    // Use the configured SUPABASE_URL when available, fallback to a safe test host
    protected string $supabaseUrl;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure the supabase config is set during tests so services build absolute URLs
        $supabaseUrl = env('SUPABASE_URL', 'https://test.supabase.co');
        $supabaseAnon = env('SUPABASE_ANON_KEY', 'test_anon_key');
        $supabaseService = env('SUPABASE_SERVICE_ROLE_KEY', 'test_service_role_key');

        config([
            'supabase.url' => $supabaseUrl,
            'supabase.anon_key' => $supabaseAnon,
            'supabase.service_role_key' => $supabaseService,
        ]);

        $this->supabaseUrl = rtrim(config('supabase.url'), '/');

        // Do not register a global Supabase Http::fake here because many tests
        // register specific fakes for various Supabase endpoints. A global
        // catch-all fake would prevent those test-level fakes from being used.
    }
}
