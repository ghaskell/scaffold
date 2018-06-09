<?php

namespace  Ghaskell\Scaffold\Console\Commands;

use Ghaskell\Scaffold\Facades\Vibro;
use Ghaskell\Scaffold\Scaffold;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Illuminate\Filesystem\Filesystem;

//use Illuminate\Console\GeneratorCommand;

class ScaffoldGenerate extends Command
{
    protected $migrationFiles;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $migrations;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scaffold:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate from migration';

    /**
     * Create a new command instance.
     *
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->files = $filesystem;
        $this->migrations = [];
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->getMigrations();
        $files = config('scaffold.files');

        $migration = $this->choice('Generate from which migration?', $this->migrations);
        $key = $this->choice('Generate which file?', array_keys($files));
//        $file = $files[$key];


//        foreach ($this->migrations as $migration) {
            $this->line("------------------------------------------------------------------------------------------------------------------");
            $this->info("Generating for {$migration}");
            $this->line("------------------------------------------------------------------------------------------------------------------");
            $scaffold = Scaffold::make($migration);
            $file = $files[$key];
            $scaffold->generate($key);
            if(!empty($file['dependencies'])) {
                foreach($file['dependencies'] as $dependency)
                {
                    $scaffold->generate($dependency);
                }
            }
            
            $scaffold->addRoutes();

            foreach($scaffold->created as $created) {
                $this->info("File '$created' generated.");
            }
            foreach($scaffold->messages as $message) {
                $this->comment($message);
            }
            $this->line("------------------------------------------------------------------------------------------------------------------");

//        }
    }
    protected function getMigrations()
    {
        //Remove built in files
        $finder = new Finder();
        $files = $finder->files()
            ->name('*table.php')
            ->notName('2014*')
            ->in(database_path() . '/migrations');

        foreach ($files as $file) {
            $this->migrations[] = $file->getRealPath() ;
        }
    }
}
