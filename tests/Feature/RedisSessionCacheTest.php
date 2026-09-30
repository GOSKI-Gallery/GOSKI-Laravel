<?php

namespace Tests\Feature;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class RedisSessionCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Override config to use redis for this test
        Config::set('session.driver', 'redis');
        Config::set('cache.default', 'redis');
        Config::set('cache.stores.redis.connection', 'cache');
    }

    protected function skipIfRedisUnavailable(): void
    {
        try {
            /** @var Application $app */
            $app = $this->app;
            $redis = $app->make('redis')->connection('cache');
            $redis->ping();
        } catch (\Throwable $e) {
            $this->markTestSkipped('Redis not available: '.$e->getMessage());
        }
    }

    public function test_cache_put_and_get_with_redis_store(): void
    {
        $this->skipIfRedisUnavailable();

        $key = 'test_key_'.uniqid();
        $value = 'test_value_'.uniqid();

        Cache::store('redis')->put($key, $value, 60);
        $this->assertEquals($value, Cache::store('redis')->get($key));

        // Cleanup
        Cache::store('redis')->forget($key);
    }

    public function test_cache_operations_with_default_redis_store(): void
    {
        $this->skipIfRedisUnavailable();

        $key = 'test_default_'.uniqid();
        $value = 'default_value_'.uniqid();

        Cache::put($key, $value, 60);
        $this->assertEquals($value, Cache::get($key));

        Cache::forget($key);
    }

    public function test_session_put_and_get_with_redis_driver(): void
    {
        $this->skipIfRedisUnavailable();

        $key = 'session_test_'.uniqid();
        $value = 'session_value_'.uniqid();

        Session::put($key, $value);
        $this->assertEquals($value, Session::get($key));

        Session::forget($key);
    }

    public function test_cache_and_session_work_together(): void
    {
        $this->skipIfRedisUnavailable();

        $cacheKey = 'combined_cache_'.uniqid();
        $sessionKey = 'combined_session_'.uniqid();
        $cacheValue = 'cache_val_'.uniqid();
        $sessionValue = 'session_val_'.uniqid();

        Cache::store('redis')->put($cacheKey, $cacheValue, 60);
        Session::put($sessionKey, $sessionValue);

        $this->assertEquals($cacheValue, Cache::store('redis')->get($cacheKey));
        $this->assertEquals($sessionValue, Session::get($sessionKey));

        Cache::store('redis')->forget($cacheKey);
        Session::forget($sessionKey);
    }

    public function test_array_store_works_in_ci(): void
    {
        // In CI, we use array store - this should always pass
        Config::set('cache.default', 'array');
        Config::set('session.driver', 'array');

        $key = 'ci_test_'.uniqid();
        $value = 'ci_value_'.uniqid();

        Cache::put($key, $value, 60);
        Session::put($key, $value);

        $this->assertEquals($value, Cache::get($key));
        $this->assertEquals($value, Session::get($key));
    }
}
