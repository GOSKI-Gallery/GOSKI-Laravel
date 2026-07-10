---
name: goski-supabase
description: Supabase integration conventions for GOSKI Laravel
---
# GOSKI Supabase — Laravel + Supabase Conventions

## Connection architecture
- **Direct DB**: Laravel's `DB::connection('pgsql')` via Supabase pgBouncer pool
- **REST API**: HTTP calls to `{supabase_url}/rest/v1/` with service role key
- Schema: `laravel` (all tables are in the `laravel` schema)

## Configuration
- `DB_CONNECTION=pgsql` — direct PostgreSQL connection through pgBouncer
- `SUPABASE_URL`, `SUPABASE_SERVICE_ROLE_KEY`, `SUPABASE_ANON_KEY` — REST API credentials
- Schema prefix: `$prefix = DB::getDriverName() === 'pgsql' ? 'laravel.' : ''`

## Service pattern
All Supabase services extend `App\Services\SupabaseBaseService` which provides:
- `$this->client(bool $useServiceKey = true)` — HTTP client with:
  - `apikey` header
  - `Authorization` header (service role or anon key)
  - `Accept-Profile: laravel` header
  - `Content-Profile: laravel` header

### Service naming
`App\Services\Supabase{Entity}Service.php` — e.g.:
- `SupabasePostService` — post CRUD, likes
- `SupabaseUserService` — user CRUD, follows
- `SupabaseNotificationService` — read-only notification queries
- `SupabaseAuthService` — authentication via Supabase Auth

### Read operations
Use **direct DB queries** via `DB::table($prefix.'table_name')` for complex joins:
```php
DB::table($prefix . 'likes')
    ->join($prefix . 'posts', 'likes.post_id', '=', 'posts.id')
    ->where('posts.user_id', $userId)
    ->select(...)
    ->get();
```

### Write operations
Use **Supabase REST API** via `$this->client()` for inserts/updates/deletes:
```php
$this->client()->post("{$this->url}/rest/v1/likes", [
    'user_id' => $userId,
    'post_id' => $postId,
    'created_at' => now()->toIso8601String(),
])->json();
```

## Schema notes
- All tables have `created_at` and `updated_at` columns
- User IDs are UUID format
- Post IDs are auto-incrementing integers
- The `laravel` schema must be used for ALL queries (via prefix or Profile header)

## RLS (Row Level Security)
- RLS policies are managed in Supabase dashboard
- Anon key has limited access — service role key has full access
- `ensureProfile` helper handles RLS errors silently during registration

## Moderation flow
1. Post inserted with `moderation_status: 'POSSIBLE'`
2. DB trigger fires on insert/update
3. Trigger calls edge function via `net.http_post`
4. Edge function sends image to OpenAI Vision API
5. Edge function calls back to update `is_nsfw` and `moderation_status`

## Key tables
| Table | Key columns | Notes |
|-------|-------------|-------|
| `users` | `id` (uuid), `username`, `email`, `profile_photo_url` | Supabase Auth linked |
| `posts` | `id`, `user_id` (uuid), `image_url`, `description`, `is_nsfw`, `moderation_status` | |
| `likes` | `id`, `user_id` (uuid), `post_id`, `created_at` | |
| `follows` | `id`, `follower_id` (uuid), `followed_id` (uuid), `created_at` | |
