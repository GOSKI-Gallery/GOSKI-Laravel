# Push Token System

## Overview

The mobile app registers an **Expo Push Token** per device/user so the backend can deliver push notifications via the Expo Push Service (which routes to FCM on Android / APNs on iOS). Location is **opt-in**: tokens are only registered after the user grants notification permissions on a real device.

## Architecture

### Database

- **Table**: `push_tokens` (in `laravel` schema on PostgreSQL, plain on others)
- **Migration**: `database/migrations/2026_08_04_000002_create_push_tokens_table.php`
- Columns: `id`, `user_id` (UUID FK → `laravel.users`, cascade delete), `token` (text), `platform` (nullable), `created_at`, `updated_at`
- Uniqueness: `unique (user_id, token)` + `on_conflict` by `user_id` (aligned with the mobile app's `upsert` contract)
- Index on `user_id`
- RLS enabled; policies scoped per `auth.uid() = user_id`

### Models

- **`app/Models/PushToken.php`** — Eloquent model with `HasSchemaPrefix`, `HasFactory`; `user()` belongsTo relationship
- **`database/factories/PushTokenFactory.php`** — generates `ExponentPushToken[...]` tokens

### Services

- **`app/Services/SupabasePushTokenService.php`** — extends `SupabaseBaseService`:
  - `getTokensByUserId(string $userId): array` — direct DB read (schema prefix) for dispatching push
  - `upsertToken(string $userId, string $token, ?string $platform): bool` — direct DB upsert (service_role bypass)
  - `deleteToken(string $token): bool` — direct DB delete (cleanup of `DeviceNotRegistered` tokens)

### Controllers & Routes

`app/Http/Controllers/PushTokenController.php` (under `auth` middleware):

| Method | URI | Name |
|--------|-----|------|
| GET | `/push-tokens` | `push.tokens.index` |
| DELETE | `/push-tokens/{token}` | `push.tokens.destroy` |

> The mobile app writes tokens **directly via PostgREST** (`/rest/v1/push_tokens` upsert with `on_conflict=user_id`). These Laravel endpoints are for the web profile/admin area and backend dispatch.

### Frontend

No direct web UI is rendered for push tokens in this phase (the mobile app owns the lifecycle). The endpoints are JSON-only and intended for progressive enhancement of the profile settings.

### Testing

- `tests/Feature/PushTokenServiceTest.php` — upsert create/update, get by user, delete, not-found handling
- `tests/Feature/PushTokenControllerTest.php` — auth guard, index returns user tokens, destroy deletes/removes, 422 on missing
- `tests/Feature/ModelFactoryTest.php` / `tests/Unit/ModelTest.php` — factory + fillable coverage
- Tests use SQLite in-memory (`RefreshDatabase`); backend read/write use `DB::table` with the schema prefix (driver-agnostic)

## Mobile

See `docs/mobile-push-tokens-ui-guide.md` for the Expo/React Native contract (`expo-location` permission flow, `ExponentPushToken[...]`, PostgREST upsert, lifecycle and cleanup notes).

## Notification dispatch flow (backend)

1. A notification event occurs (e.g. new like, follow, comment) — see `SupabaseNotificationService`.
2. Laravel reads the target user's tokens via `SupabasePushTokenService::getTokensByUserId()`.
3. POST to the Expo Push endpoint (`https://exp.host/--/api/v2/push/send`) with `{ to, title, body, data }` for each token.
4. On `DeviceNotRegistered`, the backend calls `deleteToken(token)` to clean up stale tokens.
