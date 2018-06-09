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
        if(!empty(config("scaffold.columnTypes.$type"))) {
            foreach(config("scaffold.columnTypes.$type") as $key => $value) {
                $key = str_replace('"', "", $key);
                $column->{$key} = $value;
            }
        }
        return $column;
    }
}