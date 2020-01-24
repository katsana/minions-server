<?php

namespace Minions\Server\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;

class StatusPage
{
    /**
     * Show status page on `GET /`.
     *
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        if ($request->getMethod() === 'GET' && $request->getUri()->getPath() === '/') {
            return new Response(200, ['Content-Type' => 'text/plain'], 'OK');
        }

        return $next($request);
    }
}
