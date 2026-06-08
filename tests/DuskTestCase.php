<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\AfterClass;
use PHPUnit\Framework\Attributes\BeforeClass;
use Symfony\Component\Process\Process;

abstract class DuskTestCase extends BaseTestCase
{
    protected static ?Process $phpServer = null;

    protected static ?string $phpServerBaseUrl = null;

    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }

        static::startPhpServer();
    }

    /**
     * Skip if Dusk is not enabled.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (! getenv('DUSK_ENABLED')) {
            $this->markTestSkipped('Dusk tests are disabled. Set DUSK_ENABLED=true to run.');
        }
    }

    #[AfterClass]
    public static function stopPhpServer(): void
    {
        if (static::$phpServer !== null) {
            static::$phpServer->stop();
            static::$phpServer = null;
        }
    }

    protected static function startPhpServer(): void
    {
        if (isset($_ENV['DUSK_PHP_SERVER_DISABLED']) || isset($_SERVER['DUSK_PHP_SERVER_DISABLED'])) {
            return;
        }

        $port = $_ENV['DUSK_SERVER_PORT'] ?? $_SERVER['DUSK_SERVER_PORT'] ?? 9510;
        $host = '127.0.0.1';

        static::$phpServerBaseUrl = "http://{$host}:{$port}";

        static::$phpServer = new Process([
            PHP_BINARY,
            'artisan',
            'serve',
            "--port={$port}",
            "--host={$host}",
            '--no-reload',
        ], getcwd(), [
            'APP_ENV' => 'dusk',
            'APP_URL' => static::$phpServerBaseUrl,
        ]);

        static::$phpServer->setTimeout(15);
        static::$phpServer->start();

        static::$phpServer->waitUntil(function ($type, $output) {
            $lower = mb_strtolower($output);

            return str_contains($lower, 'server running on')
                || str_contains($lower, 'development server')
                || str_contains($lower, 'started');
        });

    }

    protected function baseUrl(): string
    {
        return static::$phpServerBaseUrl ?? rtrim(config('app.url'), '/');
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
