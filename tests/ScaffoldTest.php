<?php

namespace Ghaskell\Scaffold\Tests;

use Ghaskell\Scaffold\Facades\Scaffold;
use Ghaskell\Scaffold\ServiceProvider;
use Orchestra\Testbench\TestCase;

class ScaffoldTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'scaffold' => Scaffold::class,
        ];
    }

    public function testExample()
    {
        assertEquals(1, 1);
    }
}
