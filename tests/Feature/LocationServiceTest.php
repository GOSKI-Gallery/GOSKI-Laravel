<?php

namespace Tests\Feature;

use App\Services\LocationService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LocationServiceTest extends TestCase
{
    private LocationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(LocationService::class);
    }

    public function test_returns_provided_location_name(): void
    {
        $name = $this->service->resolveLocationName('Chapada dos Veadeiros', -14.8871, -47.8071);

        $this->assertSame('Chapada dos Veadeiros', $name);
    }

    public function test_returns_null_when_no_coordinates(): void
    {
        $name = $this->service->resolveLocationName(null, null, null);

        $this->assertNull($name);
    }

    public function test_reverse_geocodes_via_nominatim(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                'display_name' => 'Alto Paraíso de Goiás, Goiás, Brazil',
            ], 200),
        ]);

        $name = $this->service->resolveLocationName(null, -14.8871, -47.8071);

        $this->assertSame('Alto Paraíso de Goiás, Goiás, Brazil', $name);
    }

    public function test_returns_null_when_nominatim_fails(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([], 500),
        ]);

        $name = $this->service->resolveLocationName(null, -14.8871, -47.8071);

        $this->assertNull($name);
    }

    public function test_returns_null_when_display_name_missing(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([], 200),
        ]);

        $name = $this->service->resolveLocationName(null, -14.8871, -47.8071);

        $this->assertNull($name);
    }
}
