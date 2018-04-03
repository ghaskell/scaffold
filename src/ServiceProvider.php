<?php

namespace Ghaskell\Scaffold;

use Ghaskell\Scaffold\Console\Commands\ScaffoldGenerate;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/scaffold.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('scaffold.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ScaffoldGenerate::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'scaffold'
        );
//
//        $this->app->bind('scaffold', function () {
//            return new Scaffold();
//        });
    }
}
