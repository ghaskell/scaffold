<?php

namespace Ghaskell\Scaffold\Providers;

use Ghaskell\Scaffold\Console\Commands\ScaffoldGenerate;
use Ghaskell\Scaffold\Facades\Code;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . './../../config/scaffold.php';

    public function boot()
    {
        Code::addExtension('stub',  'vibro');
        $this->publishes([
            self::CONFIG_PATH => config_path('scaffold.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ScaffoldGenerate::class,
            ]);
        }
        $this->publishes([
            realpath(__DIR__ . '/../stubs') => app_path('Scaffold/stubs')
        ], 'Scaffold Stubs');
    }
    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'scaffold'
        );
    }
}
