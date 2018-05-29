<?php

namespace Ghaskell\Scaffold;

use Ghaskell\Scaffold\Facades\Vibro;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class Scaffold
{
    protected $model;
    protected $migration;
    protected $variables;
    protected $migrationFileName;
    protected $files;

    public $messages = [];
    public $created = [];



    public function __construct($migration)
    {


    }

    public static function make($migrationName)
    {
        $scaffold =  new Scaffold($migrationName);
        $scaffold->files = new Filesystem();
        $scaffold->files->requireOnce($migrationName);
        $migrationInstance = $scaffold->resolve($name = $scaffold->getMigrationName($migrationName)); //parse into instance
        $reflector = new \ReflectionClass($migrationInstance);  //reflect
        $migrationFileName = $reflector->getFileName();
        $scaffold->migration = $scaffold->files->get($migrationFileName);
        $scaffold->model = ModelPattern::make($scaffold->migration);
        return $scaffold;

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
        return Vibro::compileFile($path, $this->model );
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

    public function __call($method, $arguments)
    {
        if (starts_with($method, 'build')) {
            $target = Str::camel(str_replace('build', '', $method));
            $config = config("scaffold.files.$target");
            $pathPrefix = $config['path'];
            $fileName = Vibro::compileFileName($config['fileNamePattern'], $this->model);

            $path = app_path("$pathPrefix/$fileName");
            if($this->files->exists("$path")) {
                $this->messages[] = "File '$path' exists and was not overwritten.";
                return $this;
            }

            if(!$this->files->isDirectory(app_path($pathPrefix))) {
                $this->files->makeDirectory(app_path("$pathPrefix"));
            }

//            dd($this->model->fillable);

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



}