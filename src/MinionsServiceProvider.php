<?php

namespace Minions\Server;

use Illuminate\Support\ServiceProvider;

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
