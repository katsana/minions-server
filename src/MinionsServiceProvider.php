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
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\StartJsonRpcServer::class,
            ]);
        }
    }
}
