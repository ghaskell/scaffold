<?php
/**
 * Created by PhpStorm.
 * User: georg
 * Date: 5/29/2018
 * Time: 11:53 AM
 */

namespace Ghaskell\Scaffold;


use Ghaskell\Scaffold\Facades\Vibro;
use Illuminate\Database\Migrations\Migration;
use Ghaskell\Scaffold\ColumnPattern as Column;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class ModelPattern
{
    public $name;
    public $rules;
    public $fillable;
    public $dates;
    public $touches;
    public $casts;
    protected $softDeletes;
    protected $migration;
    public $columns;
    protected $files;

    public function __construct()
    {
        $this->files = new Filesystem();
    }

    public static function make(string $migration) {
        $model = new ModelPattern();
        $model->migration = $migration;
        $model->parseModelName()
            ->parseColumns()
            ->processColumns();
        return $model;
    }

    private function parseModelName()
    {
        preg_match("/(?<=create\(\')(.*)(?=\')/", $this->migration, $result);
        $this->name = Str::singular(Str::studly($result[0]));
        return $this;
    }

    private function parseColumns()
    {
        preg_match_all('/(?<=\$table\-\>)(.*)(?=\;)/', $this->migration, $lines);
        foreach ($lines[0] as $line) {
            preg_match("([^\(]+)", $line, $type);
            switch ($type[0]) {
                case 'timestamps':
                    $this->columns[] = Column::make('created_at', 'timestamp');
                    $this->columns[] = Column::make('updated_at', 'timestamp');
                    break;
                case 'softDeletes':
                    $this->softDeletes = true;
                    $this->columns[] = Column::make('deleted_at', 'timestamp');
                    break;
                default:
                    preg_match("/['](.*?)[']/", $line, $name);
                    $this->columns[] = Column::make($name[1], $type[0]);
            }
        }
        return $this;
    }

    private function processColumns() {
        foreach($this->columns as $column) {
            $this->processColumn($column);
        }
    }

    private function processColumn($column) {
        if (!empty($column->config['fillable'])) {
            $this->fillable[] = $column->name;
        }

        if (!empty($column->config['dates'])) {
            $this->dates[] = $column->name;
        }

        if (!empty($column->config['touches'])) {
            $this->touches[] = $column->name;
        }

        if (!empty($column->config['casts'])) {
            $this->casts[$column->name] = $column->config['casts'];
        }
        if (!empty($column->config['rules'])) {
            $this->rules[$column->name] = $column->config['rules'];
        }
    }
}