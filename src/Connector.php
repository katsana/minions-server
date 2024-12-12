<?php

namespace Minions\Server;

use Laravie\Stream\Logger;
use Minions\Http\Router;
use Minions\Http\Middleware\LogRequest;
use Minions\Http\Middleware\StatusPage;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
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
        $server = new HttpServer(
            $this->eventLoop,
            new Middleware\LogRequest($this->logger),
            new Middleware\StatusPage(),
            function (ServerRequestInterface $request) use ($router) {
                $reply = $router->handle($request);

                return new Response(
                    $reply->status(), $reply->headers(), $reply->body()
                );
            }
        );

        return $this->bootServer($server, $config);
    }

    /**
     * Boot HTTP Server with socket server.
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
            $this->hostname,
            array_merge([
                'tls' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false
                ]
            ], $options),
            $this->eventLoop
        ));

        $this->logger->info("Server running at https://{$this->hostname}\n");
    }

    /**
     * Boot HTTP Socket Server.
     */
    protected function bootUnsecuredServer(HttpServer $server): void
    {
        $server->listen(new SocketServer(
            $this->hostname,
            [],
            $this->eventLoop
        ));

        $this->logger->info("Server running at http://{$this->hostname}\n");
    }
}
