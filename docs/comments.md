# Comment System

## Overview

The comment system allows users to add, view, and delete comments on posts. It follows the same architecture as the like system, using Supabase as the backend data store.

## Architecture

### Database

- **Table**: `comments` (in `laravel` schema on PostgreSQL, plain on others)
- **Migration**: `database/migrations/2026_02_25_040000_create_comments_table.php`
- Columns: `id`, `user_id` (FK → users), `post_id` (FK → posts), `body` (text), `timestamps`
- PostgreSQL RLS policies applied in migration

### Models

- **`app/Models/Comment.php`** — Eloquent model with `HasSchemaPrefix`, `HasFactory`; defines `post()` and `user()` relationships
- **`app/Models/Post.php`** — Added `comments()` HasMany relationship

### Services

- **`app/Services/SupabaseCommentService.php`** — Full CRUD against Supabase REST API:
  - `getComments(string $postId): array`
  - `addComment(string $userId, string $postId, string $body): ?array`
  - `deleteComment(string $commentId): bool`
  - `getCommentCount(string $postId): int`
- **`app/Services/SupabasePostService.php`** — Added `getCommentCount()` for feed rendering

### Controller

- **`app/Http/Controllers/CommentController.php`**
  - `index(string $postId)` — Returns JSON with comments (with joined user data)
  - `store(Request, string $postId)` — Creates a comment (AJAX + regular)
  - `destroy(string $commentId)` — Deletes a comment (AJAX + regular)

### Routes

All routes are under `auth` middleware:

| Method | URI | Name |
|--------|-----|------|
| GET | `/posts/{postId}/comments` | `post.comments.index` |
| POST | `/posts/{postId}/comments` | `post.comments.store` |
| DELETE | `/posts/comments/{commentId}` | `post.comments.destroy` |

### Frontend

- **`resources/views/components/feed/comments-drawer.blade.php`** — Slide-over drawer component with comment list form
- **`resources/views/components/feed/posts/list.blade.php`** — Added comment button with count, includes drawer per post
- **`resources/views/components/feed/posts/index.blade.php`** — Contains JavaScript for:
  - Opening/closing the drawer
  - Loading comments via AJAX (GET)
  - Submitting new comments (POST via AJAX)
  - Deleting comments (DELETE via AJAX)
  - Updating comment counts optimistically

### Tests

- **`tests/Feature/SupabaseCommentServiceTest.php`** — Unit tests for all service methods
- **`tests/Feature/CommentControllerTest.php`** — Feature tests for all controller endpoints

## Feed Integration

In `PostController@index`, each post is hydrated with `comments_count` via `SupabasePostService::getCommentCount()`.
