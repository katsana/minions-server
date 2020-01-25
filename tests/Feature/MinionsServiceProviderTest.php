<?php

namespace Minions\Server\Tests\Feature;

use Orchestra\Testbench\TestCase;

class MinionsServiceProviderTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Laravie\Stream\Laravel\StreamServiceProvider::class,
            \Minions\MinionsServiceProvider::class,
            \Minions\Http\MinionsServiceProvider::class,
            \Minions\Server\MinionsServiceProvider::class,
        ];
    }

    /** @test */
    public function it_registered_the_configuration()
    {
        $this->assertSame('127.0.0.1', config('minions-server.host'));
        $this->assertSame(8085, config('minions-server.port'));
        $this->assertFalse(config('minions-server.secure'));
    }
}
