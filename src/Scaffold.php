<?php

namespace Ghaskell\Scaffold;

use Ghaskell\Scaffold\Facades\Vibro;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Scaffold
{
    protected $model;
    protected $rules;
    protected $migration;
    protected $columns;
    protected $variables;
    protected $migrationFileName;
    protected $template;
    protected $blade;

    public $messages = [];
    public $created = [];



    public function __construct($migration)
    {
        $this->model = new \stdClass();
        $this->migration = $migration;
        $this->files = new Filesystem();
        $this->requireFiles();
        $migrationInstance = $this->resolve(
            $name = $this->getMigrationName($this->migration)
        );
        $this->migration = $migrationInstance;

        $reflector = new \ReflectionClass($migrationInstance);
        $this->migrationFileName = $reflector->getFileName();

        $this->migration = $this->files->get($this->migrationFileName);
        $this->model->name = $this->parseModelNameFromMigration();
        $this->columns = $this->parseColumnsFromMigration();
        $this->setTemplateVariables();


//        GenerateModel::make($migrationInstance)->save();
//        GenerateRequest::make($migrationInstance)->save();
//        GenerateApiController::make($migrationInstance)->save();
    }

    public static function create($migration)
    {
        return new Scaffold($migration);
    }


    /**
     * Resolve a migration instance from a file.
     *
     * @param  string  $file
     * @return object
     */
    public function resolve($file)
    {
        $class = Str::studly(implode('_', array_slice(explode('_', $file), 4)));

        return new $class;
    }

    /**
     * Get the name of the migration.
     *
     * @param  string  $path
     * @return string
     */
    public function getMigrationName($path)
    {
        return str_replace('.php', '', basename($path));
    }

    /**
     * Get all of the migration files in a given path.
     *
     * @param  string|array  $paths
     * @return array
     */
    public function getMigrationFiles($paths)
    {
        return Collection::make($paths)->flatMap(function ($path) {
            return $this->files->glob($path.'/*_*.php');
        })->filter()->sortBy(function ($file) {
            return $this->getMigrationName($file);
        })->values()->keyBy(function ($file) {
            return $this->getMigrationName($file);
        })->all();
    }

    /**
     * Require in all the migration files in a given path.
     *
     * @return void
     */
    public function requireFiles()
    {
        $files = new Filesystem;
        $files->requireOnce($this->migration);
    }

    private function parseModelNameFromMigration()
    {
        preg_match("/(?<=create\(\')(.*)(?=\')/", $this->migration, $result);
        return str_singular(studly_case($result[0]));
    }

    private function parseColumnsFromMigration()
    {
        preg_match_all('/(?<=\$table\-\>)(.*)(?=\;)/', $this->migration, $lines);
        $columns = [];
        foreach ($lines[0] as $line) {
            preg_match("([^\(]+)", $line, $type);
            switch ($type[0]) {
                case 'timestamps':
                    $columns[] = $this->addColumn('created_at', 'timestamp');
                    $columns[] = $this->addColumn('updated_at', 'timestamp');
                    break;
                case 'softDeletes':
                    $this->model->softDeletes = true;
                    $columns[] = $this->addColumn('deleted_at', 'timestamp');
                    break;
                default:
                    preg_match("/['](.*?)[']/", $line, $name);
                    $columns[] = $this->addColumn($name[1], $type[0]);
            }
        }
        return $columns;
    }

    private function setTemplateVariables()
    {
        $this->variables['modelName'] = $this->model->name;

        $this->variables['studlyName'] = Str::studly($this->model->name);
        $this->variables['studlyNamePlural'] = Str::plural(Str::studly($this->model->name));

        $this->variables['modelVariable'] = "$" . Str::camel($this->model->name);
        $this->variables['modelVariablePlural'] = "$" . Str::plural(Str::camel($this->model->name));

        $this->variables['modelNameLower'] = Str::camel($this->model->name);
        $this->variables['modelNameLowerPlural'] = Str::plural(Str::camel($this->model->name));


        if ($this->rules) {
            $this->variables['rules'] = self::arrayStringify($this->rules);
        } else {
            $this->variables['rules'] = "[]";
        }

        if (!empty($this->model->fillable)) {
            $this->variables['fillable'] = self::arrayStringify($this->model->fillable);
        } else {
            $this->variables['fillable'] = "[]";
        }

        if (!empty($this->model->dates)) {
            $this->variables['dates'] = self::arrayStringify($this->model->dates);
        } else {
            $this->variables['dates'] = "[]";
        }

        if (!empty($this->model->touches)) {
            $this->variables['touches'] = self::arrayStringify($this->model-touches);
        } else {
            $this->variables['touches'] = "[]";
        }

        if (!empty($this->model->casts)) {
            $this->variables['casts'] = self::arrayStringify($this->model->casts);
        } else {
            $this->variables['casts'] = "[]";
        }
        if (!empty($this->model->softDeletes)) {
            $this->variables['softDeletes'] = 'use SoftDeletes;';
        }
    }

    protected function addColumn($name, $type)
    {
        $column = new \stdClass(); // todo: make proper class for this
        $column->name = $name;
        $column->type = $type;

        $columnConfig = config("scaffold.columnTypes.$type");

        if (!empty($columnConfig['fillable'])) {
            $this->model->fillable[] = $name;
        }

        if (!empty($columnConfig['dates'])) {
            $this->model->dates[] = $name;
        }

        if (!empty($columnConfig['touches'])) {
            $this->model->touches[] = $name;
        }

        if (!empty($columnConfig['casts'])) {
            $this->model->casts[$name] = $columnConfig['casts'];
        }
        if (!empty($columnConfig['rules'])) {
            $this->rules[$name] = $columnConfig['rules'];
        }

        return $column;
    }

    public static function arrayStringify(array $array)
    {
        $export = str_replace(['array (', ')', '&#40', '&#41'], ['[', ']', '(', ')'], var_export($array, true));
        $export = preg_replace("/ => \n[^\S\n]*\[/m", ' => [', $export);
        $export = preg_replace("/ => \[\n[^\S\n]*\]/m", ' => []', $export);
        $export = preg_replace('/[\r\n]+/', "\n", $export);
        $export = preg_replace('/[ \t]+/', ' ', $export);
        return $export; //don't fear the trailing comma
    }

    public function build($stub)
    {
        $path = $this->getStubPath($stub);
        return Vibro::compileFile($path, $this->variables );
    }

    public function __call($method, $arguments)
    {
        if (starts_with($method, 'build')) {
            $target = Str::camel(str_replace('build', '', $method));
            $config = config("scaffold.files.$target");
            $pathPrefix = $config['path'];
            $fileName = Vibro::compileFileName($config['fileNamePattern'], $this->variables);

            $path = app_path("$pathPrefix/$fileName");
                if($this->files->exists("$path")) {
                    $this->messages[] = "File '$path' exists and was not overwritten.";
                    return $this;
                }

            if(!$this->files->isDirectory(app_path($pathPrefix))) {
                $this->files->makeDirectory(app_path("$pathPrefix"));
            }

            $content = $this->build($target);
                if($content) {
                    $this->files->put("$path", $content);
                    $this->created[] = $path;
                } else {
                    $this->messages[] = "File stub $target not found.";
                }

                return $this;
        }
    }

    protected function getStub($stub)
    {
        $stub = Str::camel($stub);
        if($this->files->exists(app_path("Scaffold/stubs/$stub.stub"))) {
                return $this->files->get(app_path("Scaffold/stubs/$stub.stub"));
        } else {
            return false;
        }
    }

    protected function getStubPath($stub)
    {
        $stub = Str::camel($stub);
        return app_path("Scaffold/stubs/$stub.stub");
    }

    public function addRoutes()
    {
        foreach(config('scaffold.routes') as $key => $route)
        {
            $namespace = Str::studly($key);
            $routeContent = $this->files->get($route['fileName']);
            $uri = Str::plural(strtolower(preg_replace(
                '/([a-zA-Z])(?=[A-Z])/',
                '$1-', $this->model->name
            )));
            if($key = 'web') {
                $routeString = "\n\nRoute::resource('$uri', '$namespace\\{$this->model->name}Controller')->only(['index', 'show']);";
            } else {
                $routeString = "\n\nRoute::apiResource('$uri', '$namespace\\{$this->model->name}Controller');";
            }

            if (!str_contains($routeContent, $routeString)) {
                $this->files->append($route['fileName'], $routeString);
            }
        }
        return $this;
    }



}