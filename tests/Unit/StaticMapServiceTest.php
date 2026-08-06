<?php

namespace Tests\Unit;

use App\Services\StaticMapService;
use Tests\TestCase;

class StaticMapServiceTest extends TestCase
{
    private StaticMapService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new StaticMapService;
    }

    public function test_world_x_origin_and_positive_longitude(): void
    {
        $this->assertSame(128.0, $this->service->worldX(0.0, 0));
        $this->assertSame(256.0, $this->service->worldX(180.0, 0));
        $this->assertSame(256.0, $this->service->worldX(0.0, 1));
    }

    public function test_world_y_origin_and_max_latitude(): void
    {
        $this->assertSame(128.0, $this->service->worldY(0.0, 0));
        $this->assertSame(256.0, $this->service->worldY(0.0, 1));
        $this->assertSame(0.0, round($this->service->worldY(85.05112878, 0), 5));
    }

    public function test_world_x_scales_with_zoom(): void
    {
        $this->assertSame(512.0, $this->service->worldX(0.0, 2));
        $this->assertSame(1024.0, $this->service->worldX(0.0, 3));
    }

    public function test_tile_url_uses_deterministic_subdomain(): void
    {
        $url = $this->service->tileUrl(17, 1, 2);

        $this->assertStringStartsWith('https://d.basemaps.cartocdn.com/rastertiles/voyager/17/1/2.png', $url);
        $this->assertSame($url, $this->service->tileUrl(17, 1, 2));
    }

    public function test_tile_url_handles_negative_coordinates(): void
    {
        $url = $this->service->tileUrl(17, -1, 2);

        $this->assertStringStartsWith('https://b.basemaps.cartocdn.com/rastertiles/voyager/17/-1/2.png', $url);
    }

    public function test_tile_url_subdomain_is_always_in_range(): void
    {
        foreach ([-3, -2, -1, 0, 1, 2, 3] as $x) {
            foreach ([-3, -2, -1, 0, 1, 2, 3] as $y) {
                $url = $this->service->tileUrl(15, $x, $y);
                $this->assertMatchesRegularExpression('#^https://[a-d]\.basemaps\.cartocdn\.com/#', $url);
            }
        }
    }

    public function test_tile_grid_covers_the_viewport(): void
    {
        $grid = $this->service->tileGrid(0.0, 0.0, 512, 512, 0);

        $this->assertSame(9, count($grid['tiles']));
        $this->assertSame(0, $grid['zoom']);
        $this->assertSame(512, $grid['width']);
        $this->assertSame(512, $grid['height']);

        $lefts = array_column($grid['tiles'], 'left');
        $tops = array_column($grid['tiles'], 'top');

        $this->assertLessThanOrEqual(0, min($lefts));
        $this->assertGreaterThanOrEqual(512, max($lefts) + 256);
        $this->assertLessThanOrEqual(0, min($tops));
        $this->assertGreaterThanOrEqual(512, max($tops) + 256);

        foreach ($grid['tiles'] as $tile) {
            $this->assertArrayHasKey('x', $tile);
            $this->assertArrayHasKey('y', $tile);
            $this->assertArrayHasKey('left', $tile);
            $this->assertArrayHasKey('top', $tile);
            $this->assertStringContainsString('/rastertiles/voyager/0/', $tile['url']);
        }
    }

    public function test_tile_grid_uses_default_zoom_from_config(): void
    {
        config(['staticmap.zoom' => 17]);

        $grid = $this->service->tileGrid(-14.8871, -47.8071, 300, 300);

        $this->assertSame(17, $grid['zoom']);
    }

    public function test_attribution_constant_matches_contract(): void
    {
        $this->assertSame('© OpenStreetMap contributors © CARTO', StaticMapService::ATTRIBUTION);
    }

    public function test_pixel_offset_places_center_at_middle(): void
    {
        $offset = $this->service->pixelOffset(0.0, 0.0, 0.0, 0.0, 512, 512, 0);

        $this->assertSame(256.0, $offset['left']);
        $this->assertSame(256.0, $offset['top']);
    }

    public function test_pixel_offset_moves_east(): void
    {
        $offset = $this->service->pixelOffset(0.0, 1.0, 0.0, 0.0, 512, 512, 2);

        $this->assertGreaterThan(256.0, $offset['left']);
        $this->assertSame(256.0, $offset['top']);
    }
}
