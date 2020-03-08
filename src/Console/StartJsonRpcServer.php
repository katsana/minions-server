<?php

namespace Minions\Server\Console;

use Illuminate\Console\Command;
use Illuminate\Database\DetectsLostConnections;
use Laravie\Stream\Log\Console as Logger;
use Minions\Server\Connector;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Input\InputOption;

class StartJsonRpcServer extends Command
{
    use DetectsLostConnections;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'minions:serve';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(LoopInterface $eventLoop, Logger $logger)
    {
        $config = \array_merge(\config('minions-server', []), [
            'host' => '0.0.0.0',
            'port' => $this->option('port'),
            'secure' => false,
        ]);

        $hostname = "{$config['host']}:{$config['port']}";

        $monolog = $this->laravel->make('log');

        $connector = new Connector($hostname, $eventLoop, $logger);

        $server = $connector->handle($this->laravel->make('minions.router'), $config);

        $server->on('error', function ($e) use ($eventLoop, $monolog) {
            $this->error($e->getMessage());
            $monolog->error((string) $e);

            if ($this->causedByLostConnection($e)) {
                $eventLoop->stop();
                exit(0);
            }
        });

        $eventLoop->run();

        return 0;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['port', null, InputOption::VALUE_OPTIONAL, 'The server port to use.', \config('minions-server.port', 8085)],
        ];
    }
}
