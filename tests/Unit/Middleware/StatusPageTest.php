<?php

namespace Minions\Server\Tests\Unit\Middleware;

use Minions\Server\Middleware\StatusPage;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class StatusPageTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_show_status_page_on_get_request()
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getMethod')->once()->andReturn('GET')
            ->shouldReceive('getUri->getPath')->once()->andReturn('/');

        $middleware = new StatusPage();

        $response = $middleware($request, function ($request) {
            //
        });

        $this->assertInstanceOf('React\Http\Response', $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['Content-Type' => ['text/plain']], $response->getHeaders());
        $this->assertSame('OK', (string) $response->getBody());
    }

    /** @test */
    public function it_can_pass_through_middleware()
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getMethod')->once()->andReturn('POST');

        $middleware = new StatusPage();

        $response = $middleware($request, function ($request) {
            return 'foo';
        });

        $this->assertSame('foo', $response);
    }
}
