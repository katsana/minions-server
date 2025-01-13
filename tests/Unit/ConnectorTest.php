<?php

namespace Minions\Server\Tests\Unit;

use Illuminate\Contracts\Container\Container;
use Laravie\Stream\Logger;
use Minions\Configuration;
use Minions\Http\Router;
use Minions\Server\Connector;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\Http\Message\Response;

class ConnectorTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_create_unsecured_server()
    {
        $eventLoop = Factory::create();
        $logger = m::mock(Logger::class);
        $container = m::mock(Container::class);
        $router = m::mock(Router::class);

        $hostname = '0.0.0.0:8085';

        $logger->shouldReceive('info')->with("Server running at http://{$hostname}\n")->andReturnNull();
        $router->shouldReceive('__invoke')->andReturn(new Response());

        $connector = new Connector($hostname, $eventLoop, $logger);

        $connector->handle($router, ['secure' => false]);

        $this->addToAssertionCount(1);

        $eventLoop->stop();
    }

    /** @test */
    public function it_can_create_secured_server()
    {
        $eventLoop = Factory::create();
        $logger = m::mock(Logger::class);
        $container = m::mock(Container::class);
        $router = m::mock(Router::class);

        $hostname = '0.0.0.0:8086';

        $logger->shouldReceive('info')->with("Server running at https://{$hostname}\n")->andReturnNull();
        $router->shouldReceive('__invoke')->andReturn(new Response());

        $connector = new Connector($hostname, $eventLoop, $logger);

        $connector->handle($router, ['secure' => true]);

        $this->addToAssertionCount(1);

        $eventLoop->stop();
    }
}
