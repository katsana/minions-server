<?php

namespace Minions\Server;

use Laravie\Stream\Logger;
use Minions\Http\Router;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\Http\Message\Response;
use React\Http\HttpServer;
use React\Socket\SocketServer;

class Connector
{
    /**
     * The server hostname.
     *
     * @var string
     */
    protected $hostname;

    /**
     * The event loop implementation.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $eventLoop;

    /**
     * The console logger.
     *
     * @var \Laravie\Stream\Logger
     */
    protected $logger;

    /**
     * Construct a new HTTP Server connector.
     */
    public function __construct(string $hostname, LoopInterface $eventLoop, Logger $logger)
    {
        $this->hostname = $hostname;
        $this->eventLoop = $eventLoop;
        $this->logger = $logger;
    }

    /**
     * Create HTTP Server.
     */
    public function handle(Router $router, array $config): HttpServer
    {
        $handler = function (ServerRequestInterface $request) use ($router) {
            return $router($request);
        };

        $server = new HttpServer($this->eventLoop, $handler);

        return $this->bootServer($server, $config);
    }

    /**
     * HTTP request middlewares.
     */
    protected function middlewares(Router $router, callable $handler): array
    {
        return [
            new Middleware\LogRequest($this->logger),
            new Middleware\StatusPage(),
            $handler,
        ];
    }

    /**
     * Boot server either using HTTPS or HTTP.
     */
    protected function bootServer(HttpServer $server, array $config): HttpServer
    {
        if ($config['secure'] === true) {
            $this->bootSecuredServer($server, $config['options'] ?? []);
        } else {
            $this->bootUnsecuredServer($server);
        }

        return $server;
    }

    /**
     * Boot HTTPS Socket Server.
     */
    protected function bootSecuredServer(HttpServer $server, array $options): void
    {
        $server->listen(new SocketServer(
            "tls://{$this->hostname}",
            [
                'tls' => $options,
                'loop' => $this->eventLoop
            ]
        ));

        $this->logger->info("Server running at https://{$this->hostname}\n");
    }

    /**
     * Boot HTTP Socket Server.
     */
    protected function bootUnsecuredServer(HttpServer $server): void
    {
        $server->listen(new SocketServer(
            "tcp://{$this->hostname}",
            [
                'loop' => $this->eventLoop
            ]
        ));

        $this->logger->info("Server running at http://{$this->hostname}\n");
    }
}
