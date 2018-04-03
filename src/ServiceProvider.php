<?php

namespace Ghaskell\Scaffold;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/scaffold.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('scaffold.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'scaffold'
        );

        $this->app->bind('scaffold', function () {
            return new Scaffold();
        });
    }
}
