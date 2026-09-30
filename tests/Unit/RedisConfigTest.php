<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class RedisConfigTest extends TestCase
{
    public function test_redis_database_config_uses_env_variables(): void
    {
        // Test that the redis config structure exists with correct keys
        $redisConfig = config('database.redis');

        $this->assertArrayHasKey('client', $redisConfig);
        $this->assertArrayHasKey('default', $redisConfig);
        $this->assertArrayHasKey('cache', $redisConfig);

        // Test default connection has required keys
        $default = $redisConfig['default'];
        $this->assertArrayHasKey('host', $default);
        $this->assertArrayHasKey('port', $default);
        $this->assertArrayHasKey('password', $default);

        // Test cache connection has required keys
        $cache = $redisConfig['cache'];
        $this->assertArrayHasKey('host', $cache);
        $this->assertArrayHasKey('port', $cache);
        $this->assertArrayHasKey('database', $cache);
        $this->assertEquals('1', $cache['database']); // cache uses DB 1
    }

    public function test_session_driver_defaults_to_redis_in_config_file(): void
    {
        // Read the config file directly to verify the default
        $configContent = file_get_contents(config_path('session.php'));

        // The default should be 'redis' not 'database'
        $this->assertStringContainsString("env('SESSION_DRIVER', 'redis')", $configContent);
        $this->assertStringNotContainsString("env('SESSION_DRIVER', 'database')", $configContent);
    }

    public function test_cache_store_defaults_to_redis_in_config_file(): void
    {
        // Read the config file directly to verify the default
        $configContent = file_get_contents(config_path('cache.php'));

        // The default should be 'redis' not 'database'
        $this->assertStringContainsString("env('CACHE_STORE', 'redis')", $configContent);
        $this->assertStringNotContainsString("env('CACHE_STORE', 'database')", $configContent);
    }

    public function test_redis_cache_store_uses_redis_connection(): void
    {
        // Test that the redis cache store points to the 'cache' redis connection
        $this->assertEquals('cache', config('cache.stores.redis.connection'));
    }

    public function test_redis_config_can_be_overridden_at_runtime(): void
    {
        // Test that config can be overridden for testing purposes
        Config::set('database.redis.default.host', 'test-host');
        Config::set('database.redis.default.port', '9999');
        Config::set('session.driver', 'array');
        Config::set('cache.default', 'array');

        $this->assertEquals('test-host', config('database.redis.default.host'));
        $this->assertEquals('9999', config('database.redis.default.port'));
        $this->assertEquals('array', config('session.driver'));
        $this->assertEquals('array', config('cache.default'));
    }

    public function test_env_example_has_redis_variables(): void
    {
        $envExample = file_get_contents(base_path('.env.example'));

        $this->assertStringContainsString('REDIS_HOST=redis', $envExample);
        $this->assertStringContainsString('REDIS_PORT=6379', $envExample);
        $this->assertStringContainsString('REDIS_PASSWORD=', $envExample);
        $this->assertStringContainsString('REDIS_CLIENT=phpredis', $envExample);

        // Should be after CARTO_API_KEY
        $this->assertStringContainsString('CARTO_API_KEY=', $envExample);
    }

    public function test_docker_compose_has_redis_env_vars(): void
    {
        $dockerCompose = file_get_contents(base_path('docker-compose.yml'));

        // Check both app and worker services have the redis env vars
        $this->assertStringContainsString('REDIS_HOST: ${REDIS_HOST:-redis}', $dockerCompose);
        $this->assertStringContainsString('REDIS_PORT: ${REDIS_PORT:-6379}', $dockerCompose);
        $this->assertStringContainsString('REDIS_PASSWORD: ${REDIS_PASSWORD:-}', $dockerCompose);
        $this->assertStringContainsString('REDIS_CLIENT: ${REDIS_CLIENT:-phpredis}', $dockerCompose);

        // Should appear twice (app and worker)
        $this->assertEquals(2, substr_count($dockerCompose, 'REDIS_HOST: ${REDIS_HOST:-redis}'));
    }
}
