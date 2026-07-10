---
name: goski-testing
description: PHPUnit testing conventions for GOSKI Laravel
---
# GOSKI Testing — PHPUnit Conventions

## Test framework
- **PHPUnit** via Laravel's test command
- Tests live in `tests/Feature/` and `tests/Unit/`

## Running tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter="TestName"

# Run with verbose output
php artisan test -v

# Run specific testsuite
php artisan test --testsuite=Feature

# Run tests with coverage
php artisan test --coverage
```

## Conventions you MUST follow

### Test structure
- Extend `Tests\TestCase` for feature tests
- Name test files with `Test` suffix: `MyFeatureTest.php`
- Use PHP 8.x features (typed properties, named arguments, etc.)

### Supabase mocking
- Mock service classes directly (they extend `SupabaseBaseService`)
- Use `Http::fake()` for external HTTP calls to Supabase REST API
- Use `DB::shouldReceive()` for database query mocking
- Never make real HTTP calls to Supabase in tests

### Test patterns for controllers
```php
public function test_it_returns_notifications()
{
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->getJson(route('notifications.index'));
    $response->assertStatus(200);
    $response->assertJsonStructure([/* ... */]);
}
```

### Test patterns for services
- Test each service method in isolation
- Mock the HTTP client with `Http::fake()` for REST API calls
- Assert correct data was sent in the request
- Test error and edge cases (timeouts, empty responses, 4xx/5xx)

### Database
- Tests use SQLite in-memory (see `phpunit.xml`)
- Reset database between tests with `RefreshDatabase` trait or `RefreshDatabase`/`DatabaseMigrations`
- Use factories for test data creation

### Common assertions
```php
$response->assertStatus(200);
$response->assertJson(['success' => true]);
$response->assertJsonCount(3, 'data');
$this->assertDatabaseHas('likes', ['user_id' => $userId, 'post_id' => $postId]);
```

### BeforeEach / Setup
```php
protected function setUp(): void
{
    parent::setUp();
    Http::fake();
    // Reset any cached state
}
```
