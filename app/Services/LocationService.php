<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LocationService
{
    public function resolveLocationName(?string $locationName, ?float $latitude, ?float $longitude): ?string
    {
        if (! empty($locationName)) {
            return $locationName;
        }

        if ($latitude === null || $longitude === null) {
            return null;
        }

        $cacheKey = 'location:'.round($latitude, 3).':'.round($longitude, 3);

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($latitude, $longitude) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => config('app.name', 'GOSKI').' (localizacao@goski.com)',
                ])->acceptJson()
                    ->get('https://nominatim.openstreetmap.org/reverse', [
                        'format' => 'jsonv2',
                        'lat' => $latitude,
                        'lon' => $longitude,
                        'zoom' => 14,
                    ]);

                if ($response->failed()) {
                    return;
                }

                $data = $response->json();

                if (! is_array($data) || empty($data['display_name'])) {
                    return;
                }

                return mb_substr((string) $data['display_name'], 0, 255);
            } catch (\Throwable) {
                return;
            }
        });
    }
}
