<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 5/28/2018
 * Time: 1:24 PM
 */

namespace Ghaskell\Scaffold\Facades;
use Illuminate\Support\Facades\Facade;
class Vibro extends Facade{
    protected static function getFacadeAccessor()
    {
        return 'vibro';
    }
}