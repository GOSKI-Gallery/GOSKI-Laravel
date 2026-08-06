<?php

namespace App\Services;

/**
 * Pure Web Mercator tile math for the static map.
 *
 * The formulas here are the single source of truth shared with the mobile
 * implementation — keep them identical across stacks:
 *   worldX = (lng + 180) / 360 * 2^zoom * tileSize
 *   worldY = (1 - ln(tan(latRad) + 1/cos(latRad)) / pi) / 2 * 2^zoom * tileSize
 *   tileX  = floor(worldX / tileSize), tileY = floor(worldY / tileSize)
 *   subdomain = ['a','b','c','d'][abs(x + y) % 4]  (abs before modulo!)
 */
class StaticMapService
{
    public const TILE_SIZE = 256;

    public const ATTRIBUTION = '© OpenStreetMap contributors © CARTO';

    public function defaultZoom(): int
    {
        return (int) config('staticmap.zoom', 17);
    }

    public function worldX(float $longitude, int $zoom): float
    {
        return ($longitude + 180) / 360 * (2 ** $zoom) * self::TILE_SIZE;
    }

    public function worldY(float $latitude, int $zoom): float
    {
        $latRad = deg2rad($latitude);

        return (1 - log(tan($latRad) + 1 / cos($latRad)) / M_PI) / 2 * (2 ** $zoom) * self::TILE_SIZE;
    }

    public function tileUrl(int $zoom, int $x, int $y): string
    {
        $subdomains = config('staticmap.subdomains', ['a', 'b', 'c', 'd']);
        $baseUrl = config('staticmap.base_url', 'https://{subdomain}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}.png');

        $subdomain = $subdomains[abs($x + $y) % count($subdomains)];

        return str_replace(
            ['{subdomain}', '{z}', '{x}', '{y}'],
            [(string) $subdomain, (string) $zoom, (string) $x, (string) $y],
            $baseUrl
        );
    }

    /**
     * Compute the tile grid covering a viewport centered on (lat, lng).
     *
     * Returns tiles with pixel offsets relative to the viewport's top-left.
     */
    public function tileGrid(float $latitude, float $longitude, int $width, int $height, ?int $zoom = null): array
    {
        $zoom = $zoom ?? $this->defaultZoom();

        $centerX = $this->worldX($longitude, $zoom);
        $centerY = $this->worldY($latitude, $zoom);

        $viewLeft = $centerX - $width / 2;
        $viewTop = $centerY - $height / 2;

        $xMin = (int) floor($viewLeft / self::TILE_SIZE);
        $xMax = (int) floor(($viewLeft + $width - 1) / self::TILE_SIZE);
        $yMin = (int) floor($viewTop / self::TILE_SIZE);
        $yMax = (int) floor(($viewTop + $height - 1) / self::TILE_SIZE);

        $tiles = [];

        for ($y = $yMin; $y <= $yMax; $y++) {
            for ($x = $xMin; $x <= $xMax; $x++) {
                $tiles[] = [
                    'x' => $x,
                    'y' => $y,
                    'left' => $x * self::TILE_SIZE - $viewLeft,
                    'top' => $y * self::TILE_SIZE - $viewTop,
                    'url' => $this->tileUrl($zoom, $x, $y),
                ];
            }
        }

        return [
            'zoom' => $zoom,
            'width' => $width,
            'height' => $height,
            'attribution' => $this->attribution(),
            'center_x' => $centerX,
            'center_y' => $centerY,
            'tiles' => $tiles,
        ];
    }

    /**
     * Pixel offset of a coordinate relative to the viewport's top-left,
     * computed from the same center/zoom as tileGrid().
     */
    public function pixelOffset(
        float $latitude,
        float $longitude,
        float $centerLatitude,
        float $centerLongitude,
        int $width,
        int $height,
        ?int $zoom = null
    ): array {
        $zoom = $zoom ?? $this->defaultZoom();

        $viewLeft = $this->worldX($centerLongitude, $zoom) - $width / 2;
        $viewTop = $this->worldY($centerLatitude, $zoom) - $height / 2;

        return [
            'left' => $this->worldX($longitude, $zoom) - $viewLeft,
            'top' => $this->worldY($latitude, $zoom) - $viewTop,
        ];
    }

    public function attribution(): string
    {
        return (string) config('staticmap.attribution', self::ATTRIBUTION);
    }
}
