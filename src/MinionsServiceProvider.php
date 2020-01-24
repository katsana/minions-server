<?php

namespace Minions\Server;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Minions\Concerns\Configuration;
use Orchestra\Canvas\Core\CommandsProvider;

class MinionsServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/minions-server.php' => \config_path('minions-server.php'),
        ], 'config');


        $this->mergeConfigFrom(__DIR__.'/../config/minions-server.php', 'minions-server');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\StartJsonRpcServer::class,
            ]);
        }
    }
}
