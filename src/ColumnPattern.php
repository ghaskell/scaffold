<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 5/29/2018
 * Time: 12:51 PM
 */

namespace Ghaskell\Scaffold;


class ColumnPattern
{
    public $type;
    public $name;
    public $config;

    public static function make(string $name, string $type) {
        $column  = new ColumnPattern();
        $column->name = $name;
        $column->type = $type;
        $column->config = config("scaffold.columnTypes.$type");
        return $column;
    }
}