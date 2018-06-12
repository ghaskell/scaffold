<?php

namespace Ghaskell\Scaffold\Providers;

use Ghaskell\Scaffold\Console\Commands\ScaffoldGenerate;
use Ghaskell\Scaffold\Facades\Code;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $configPath;

    public function __construct(\Illuminate\Contracts\Foundation\Application $app)
    {
        $this->configPath = realpath(__DIR__ . './../../config/scaffold.php');
        parent::__construct($app);
    }

    public function boot()
    {
        Code::addExtension('stub',  'vibro');
        $this->publishes([
            $this->configPath => config_path('scaffold.php'),
        ], 'Scaffold');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ScaffoldGenerate::class,
            ]);
        }
        $this->publishes([
            realpath(__DIR__ . '/../stubs') => app_path('Scaffold/stubs')
        ], 'Scaffold');
    }
    public function register()
    {
        $this->mergeConfigFrom(
            $this->configPath,
            'scaffold'
        );
    }
}
