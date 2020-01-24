<?php

namespace Minions\Server\Tests\Unit\Middleware;

use Minions\Server\Middleware\LogRequest;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class LogRequestTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_pass_through_middleware()
    {
        $logger = m::mock('Laravie\Stream\Logger');
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $logger->shouldReceive('info')->with(m::type('String'))->andReturnNull();

        $request->shouldReceive('getMethod')->once()->andReturn('GET')
            ->shouldReceive('getUri')->once()->andReturn('/');

        $middleware = new LogRequest($logger);

        $response = $middleware($request, function ($request) {
            return 'foo';
        });

        $this->assertSame('foo', $response);
    }
}
