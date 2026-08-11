# Location System

## Overview

Posts can optionally carry a geographic location. Location is **opt-in**: both the mobile app and the web composer only send coordinates when the user explicitly enables it.

## Architecture

### Database

- **Table**: `posts` (in `laravel` schema on PostgreSQL, plain on others)
- **Migration**: columns foldados em `database/migrations/2026_02_25_035701_create_posts_table.php`
- Added columns: `latitude` decimal(10,7) nullable, `longitude` decimal(10,7) nullable, `location_name` varchar(255) nullable
- Composite index on `(latitude, longitude)` for range (bounding box) queries
- Existing RLS policies remain valid — INSERT already checks `auth.uid() = user_id`

### Storage/query strategy

- Plain numeric lat/lng (no PostGIS). "Nearby" queries use a bounding-box range filter + Haversine distance computed in PHP.
- Radius default: 25 km, max 50 results, sorted by distance ascending.

### Models

- **`app/Models/Post.php`** — added `latitude`, `longitude`, `location_name` to `$fillable`; casts lat/lng to float
- **`app/Models/Post` factory** — `withLocation()` state for tests

### Services

- **`app/Services/SupabasePostService.php`** — added `getNearbyPosts(string $postId, float $lat, float $lng, int $radiusKm = 25, int $limit = 50): array` (direct DB query with schema prefix)
- **`app/Services/LocationService.php`** (new) — `resolveLocationName(?string $name, ?float $lat, ?float $lng): ?string`; if `location_name` is empty and coordinates exist, reverse-geocodes via Nominatim/OSM with a 7-day cache keyed by rounded coordinates

### Controllers

- **`app/Http/Controllers/Post/PostController.php`** — `store()` now persists `latitude`/`longitude`/`location_name`; falls back to server reverse geocode when only coordinates are sent
- **`app/Http/Controllers/Post/PostLocationController.php`** (new) — `show(string $postId)` returns JSON `{ success, post, nearby, map }` for the map modal; `map` (tile grid + pin offsets) is computed by `StaticMapService` when `width`/`height` query params are provided; 404 when post has no location

### Routes

All under `auth` middleware:

| Method | URI | Name |
|--------|-----|------|
| GET | `/posts/{postId}/location` | `post.location.show` |

### Frontend

- **`resources/views/components/feed/posts/list.blade.php`** — blue location text (pin icon + `location_name`, fallback "Ver no mapa") rendered below the username; click opens the map modal
- **`resources/views/components/feed/location-modal.blade.php`** — **static CARTO Voyager map** (no Leaflet/map library). The clicked post renders a central blue pin; nearby posts render as small image cards. Tiles are composed as absolutely-positioned 256×256 `<img>` from `map.tiles`; attribution `© OpenStreetMap contributors © CARTO` is always shown in the map's bottom-left corner; the footer shows `location_name` (fallback "Localização exata").
- **`app/Services/StaticMapService.php`** — pure Web Mercator tile math (`worldX`, `worldY`, `tileUrl`, `tileGrid`, `pixelOffset`), the single source of truth shared with mobile. Subdomain `a–d` chosen deterministically via `abs(x + y) % 4`. Fixed zoom configured in `config/staticmap.php`.
- **`resources/views/components/feed/posts/index.blade.php`** — includes the location modal
- **`resources/views/components/create-post-modal.blade.php`** — opt-in "Adicionar localização" toggle using `navigator.geolocation` + Nominatim reverse geocode

### Tests

- `tests/Feature/PostLocationControllerTest.php` — endpoint returns post + nearby + static map grid, 404 without location, auth required
- `tests/Unit/StaticMapServiceTest.php` — Web Mercator math, tile URL/subdomain determinism (incl. negative coords), grid coverage, zoom from config
- `tests/Feature/PostNearbyServiceTest.php` — `getNearbyPosts` returns nearby posts sorted by distance
- `tests/Feature/LocationServiceTest.php` — Nominatim fallback with `Http::fake()`
- `tests/Feature/PostControllerTest.php` — store persists location and validates invalid lat/lng
- `tests/Unit/RequestRulesTest.php` — new validation rules

## Mobile

See `docs/mobile-location-ui-guide.md` for the Expo/React Native contract (expo-location + PostgREST INSERT).
