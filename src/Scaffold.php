<?php

namespace Ghaskell\Scaffold;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
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

    public $messages = [];



    public function __construct($migration)
    {
        $this->model = new \stdClass();
        $this->migration = $migration;
        $this->requireFiles();
        $migrationInstance = $this->resolve(
            $name = $this->getMigrationName($this->migration)
        );
        $this->migration = $migrationInstance;


        $this->template = new \Mustache_Engine([
            'delimiters' => '<% %>',
            'escape' => function ($value) {
                return $value; //no escaping.
            }
        ]);

        $this->files = new Filesystem();
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

        $this->variables['modelVariable'] = "$" . Str::camel($this->model->name);

        $this->variables['modelNameLower'] = Str::camel($this->model->name);


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
        $stub = $this->getStub($stub);
        if(!empty($stub)) {
            return $this->template
                ->render(
                    $stub,
                    $this->variables
                );
        } else {
            return false;
        }

    }

    public function __call($method, $arguments)
    {
        if (starts_with($method, 'build')) {
            if (str_contains($method, "Model")) {
                $path = app_path($this->model->name);
                if($this->files->exists("$path.php")) {
                    $this->messages[] = "File {$this->model->name}.php already exists.";
                    return $this;
                }
            } elseif (str_contains($method, "ApiController")) {
                $path = app_path("Http/Controllers/Api/".$this->model->name."Controller");
                if($this->files->exists("$path.php")) {
                    $this->messages[] = "File $path.php already exists.";
                    return $this;
                }
                $this->files->makeDirectory(app_path("Http/Controllers/Api"), 0755, false, true);
            } elseif (str_contains($method, "WebController")) {
                $path = app_path("Http/Controllers/Web/".$this->model->name."Controller");
                if($this->files->exists("$path.php")) {
                    $this->messages[] = "File $path.php already exists.";
                    return $this;
                }
                $this->files->makeDirectory(app_path("Http/Controllers/Web"), 0755, false, true);
            } elseif (str_contains($method, "Request")) {
                $path = app_path("Http/Requests/".$this->model->name."Request");
                if($this->files->exists("$path.php")) {
                    $this->messages[] = "File {$this->model->name}Request.php already exists.";
                    return $this;
                }
            } else {
                $path = app_path();
                if($this->files->exists("$path.php")) {
                    $this->messages[] = "File {$this->model->name}.php already exists.";
                    return $this;
                }
            }

            if($this->files->exists("$path.php")) {
                $this->messages[] = "File {$this->model->name}Controller already exists.";
                return $this;
            }
            $target = substr($method, 5);
            $content = $this->build($target);
            if($content) {
                $this->files->put("$path.php", $content);
            } else {
                $this->messages[] = "File stub $target not found.";
            }
            return $this;

        }
    }

    protected function getStub($stub)
    {
        if($this->files->exists(app_path("Scaffold/stubs/$stub.stub"))) {
                return $this->files->get(app_path("Scaffold/stubs/$stub.stub"));
        } else {
            return false;
        }
    }

    public function addRoutes()
    {
        $apiRoutes = $this->files->get(base_path("routes/api.php"));
        $webRoutes = $this->files->get(base_path("routes/web.php"));
        $uri = Str::plural(strtolower(preg_replace(
            '/([a-zA-Z])(?=[A-Z])/',
            '$1-', $this->model->name
        )));
        $apiRouteString = "\n\nRoute::apiResource('$uri', 'Api\{$this->model->name}Controller');";

        $webRouteString = "\n\nRoute::resource('$uri', 'Web\{$this->model->name}Controller')->only(['index', 'show']);";

        if (!str_contains($apiRoutes, $apiRouteString)) {
            $this->files->append(base_path("routes/api.php"), $apiRouteString);
        }

        if (!str_contains($webRoutes, $webRouteString)) {
            $this->files->append(base_path("routes/web.php"), $webRouteString);
        }

        return $this;
    }



}