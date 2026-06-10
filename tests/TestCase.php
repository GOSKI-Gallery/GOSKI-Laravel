<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected string $supabaseUrl;

    public function createApplication()
    {
        $app = parent::createApplication();

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);

        $supabaseUrl = env('SUPABASE_URL', 'https://test.supabase.co');
        $supabaseAnon = env('SUPABASE_ANON_KEY', 'test_anon_key');
        $supabaseService = env('SUPABASE_SERVICE_ROLE_KEY', 'test_service_role_key');

        config([
            'supabase.url' => $supabaseUrl,
            'supabase.anon_key' => $supabaseAnon,
            'supabase.service_role_key' => $supabaseService,
        ]);

        $this->supabaseUrl = rtrim(config('supabase.url'), '/');

        // Block any real HTTP calls during tests. Tests must explicitly fake
        // the endpoints they use via Http::fake([...]). If a test makes an
        // unmocked request, a clear exception is thrown with the URL.
        Http::preventStrayRequests();
    }
}
