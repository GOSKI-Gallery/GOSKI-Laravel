# Location — Mobile (React Native) Implementation Guide

This document specifies how the GOSKI mobile app must capture and submit post location, following the existing patterns from `docs/edge-function-mobile-prompt.txt` and `docs/mobile-comments-ui-guide.md`.

---

## 1. Overview

Posts may optionally carry a location. The mobile app:

1. Captures the device position **only if the user opts in** (toggle in the post composer).
2. Reverse-geocodes the coordinates to a human-readable name using `expo-location`.
3. Sends `latitude`, `longitude`, `location_name` alongside the post INSERT via PostgREST.
4. If the user declines, none of the location fields are sent.

Privacy is opt-in by default. Never capture or send location without the user's consent.

---

## 2. Capturing the location (`expo-location`)

Install:
```
npx expo install expo-location
```

Request permission (must happen from an explicit user action — the toggle "Adicionar localização"):

```ts
import * as Location from 'expo-location';

const { status } = await Location.requestForegroundPermissionsAsync();
if (status !== 'granted') {
  // show a friendly message, do NOT send any location data
  return;
}

const pos = await Location.getCurrentPositionAsync({
  accuracy: Location.Accuracy.Balanced,
});

const [place] = await Location.reverseGeocodeAsync({
  latitude: pos.coords.latitude,
  longitude: pos.coords.longitude,
});

const locationName = place
  ? [place.city, place.region, place.country].filter(Boolean).join(', ')
  : null;
```

> `expo-location` reverse geocoding on iOS uses Apple Maps; on Android uses Google. It works offline on Android only if the device has a cached provider — always show the text result to the user before posting.

---

## 3. Submitting the post (PostgREST)

Same flow as `docs/edge-function-mobile-prompt.txt` — upload the image first, then INSERT into `laravel.posts` **without** `moderation_status` / `is_nsfw` (the moderation trigger must fire).

```
POST {SUPABASE_URL}/rest/v1/posts
Headers:
  apikey: {SUPABASE_ANON_KEY}
  Authorization: Bearer {user_jwt}
  Content-Profile: laravel
  Accept-Profile: laravel
```

Body — with location:
```json
{
  "user_id": "{auth.uid()}",
  "image_url": "{public_url_do_upload}",
  "description": "descrição opcional",
  "latitude": -14.8871,
  "longitude": -47.8071,
  "location_name": "Alto Paraíso de Goiás, Goiás, Brazil"
}
```

Body — WITHOUT location (user declined):
```json
{
  "user_id": "{auth.uid()}",
  "image_url": "{public_url_do_upload}",
  "description": "descrição opcional"
}
```

Field constraints (mirrors the web `CreatePostRequest`):
| Field | Type | Rules |
|---|---|---|
| `latitude` | number (nullable) | optional, between -90 and 90, up to 7 decimals |
| `longitude` | number (nullable) | optional, between -180 and 180, up to 7 decimals |
| `location_name` | string (nullable) | optional, max 255 chars |

> ❗ **Obrigatório:** os headers `Content-Profile: laravel` e `Accept-Profile: laravel` são obrigatórios para acessar tabelas no schema `laravel`.

---

## 4. Reading a post's location

```
GET {SUPABASE_URL}/rest/v1/posts?id=eq.{postId}
Headers:
  apikey: {SUPABASE_ANON_KEY}
  Authorization: Bearer {user_jwt}
  Accept-Profile: laravel
```

Response includes the new columns:
```json
{
  "id": 1,
  "user_id": "uuid",
  "image_url": "https://...",
  "latitude": -14.8871,
  "longitude": -47.8071,
  "location_name": "Alto Paraíso de Goiás, Goiás, Brazil"
}
```

When `latitude`/`longitude` are `null`, the post has no location — hide the location UI entirely.

---

## 5. UX requirements (composer)

- **Toggle "Adicionar localização"** — off by default. Tapping it requests permission and captures position.
- While resolving: show a loading hint ("Obtendo localização...").
- After resolving: show the resolved place name so the user can confirm before posting.
- The user can remove the location before posting (toggle resets all three fields).
- Never block posting if permission is denied or resolution fails — location is optional.

---

## 6. Nearby posts (map in web)

The web map shows other posts near a given post (default radius 25 km). This is server-computed; mobile only needs to send/store coordinates. No extra mobile work required.

---

## 7. Edge cases

| Scenario | Behavior |
|---|---|
| User denies permission | Do not send location; post proceeds normally |
| Reverse geocode fails | Send `latitude`/`longitude` only, `location_name` omitted (web falls back to server reverse geocode) |
| Location accuracy low | Accept as-is; distance shown on map is approximate |
| Latitude/longitude out of range | Rejected by server validation — validate client-side before sending |

---

## 8. Colors (follows the web component)

Location text/icon color on cards: `text-blue-600` / dark `text-blue-400`. Pin icon path:
`M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z`
