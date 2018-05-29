<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 5/28/2018
 * Time: 1:09 PM
 */

namespace Ghaskell\Scaffold\Providers;

use Ghaskell\Scaffold\VibroCompiler;
use Illuminate\Support\Facades\App;
use Illuminate\View\ViewServiceProvider;

class VibroServiceProvider extends ViewServiceProvider
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
        $this->app->singleton('vibro.compiler', function () {
            return new VibroCompiler(
                $this->app['files'], $this->app['config']['view.compiled']
            );
        });

        App::bind('vibro', function()
        {
            return new VibroCompiler(
                $this->app['files'], $this->app['config']['view.compiled']
            );
        });
    }

//    public function registerVibroEngine($resolver)
//    {
//        $this->app->singleton('blade.compiler', function () {
//            return new Vibroblade(
//                $this->app['files'], $this->app['config']['view.compiled']
//            );
//        });
//
//        $resolver->register('blade', function () {
//            return new CompilerEngine($this->app['blade.compiler']);
//        });
//    }
}