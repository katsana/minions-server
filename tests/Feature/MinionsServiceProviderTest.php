<?php

namespace Minions\Server\Tests\Feature;

use Minions\Server\MinionsServiceProvider;
use Orchestra\Testbench\TestCase;

class MinionsServiceProviderTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Laravie\Stream\Laravel\StreamServiceProvider::class,
            \Minions\MinionsServiceProvider::class,
            \Minions\Http\MinionsServiceProvider::class,
            MinionsServiceProvider::class,
        ];
    }

    /** @test */
    public function it_registered_the_configuration()
    {
        $this->assertSame('127.0.0.1', config('minions-server.host'));
        $this->assertSame(8085, config('minions-server.port'));
        $this->assertFalse(config('minions-server.secure'));
    }

    /** @test */
    public function it_can_publish_configuration_file()
    {
        $configFile = realpath(__DIR__.'/../..').'/src/../config/minions-server.php';

        if (DIRECTORY_SEPARATOR === '\\') {
            $configFile = realpath(__DIR__.'\..\..').'\src/../config/minions-server.php';
        }

        $this->assertSame([
            $configFile => $this->app->configPath('minions-server.php'),
        ], MinionsServiceProvider::pathsToPublish(MinionsServiceProvider::class, 'config'));
    }
}
