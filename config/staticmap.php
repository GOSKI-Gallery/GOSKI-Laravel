<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Static Map
    |--------------------------------------------------------------------------
    |
    | The location modal composes a static map from CARTO Voyager raster
    | tiles (256x256 PNGs) — no map library, no custom headers.
    | CARTO now requires an API key (free up to 5M tiles/month at
    | carto.com/basemaps/apikey), appended as ?key=YOUR_KEY.
    | Zoom is fixed in this single place for web and mobile parity.
    |
    */

    'zoom' => env('STATIC_MAP_ZOOM', 17),

    'tile_size' => 256,

    'base_url' => 'https://{subdomain}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}.png',

    'api_key' => env('CARTO_API_KEY', ''),

    'subdomains' => ['a', 'b', 'c', 'd'],

    'attribution' => '© OpenStreetMap contributors © CARTO',

];
