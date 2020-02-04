<?php

namespace ArkadiuszBachorski\Xmake\Commands\MakeCommands;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MigrationMakeCommand extends ExtendedGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'xmake:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Migration';

    protected $stubName = "migration.stub";


    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers';
    }


    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        $path = database_path('/migrations');

        $fileName = date('Y_m_d_His').'_'.$this->argument('name').'.php';

        $className = 'Create'.Str::studly($this->option('create')).'Table';

        if (class_exists($className)) {
            throw new InvalidArgumentException("A {$className} class already exists.");
        }

        $final = $path.'/'.$fileName;


        return $final;
    }


    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $replace = [];

        $replace = $this->buildCoreReplacements($replace);
        $replace = $this->buildFieldsReplacements($replace);

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    protected function buildCoreReplacements(array $replace)
    {
        $create = $this->option('create');
        $className = Str::studly($create);


        return array_merge($replace, [
            'DummyName' => $create,
            'DummyCN' => $className,
        ]);
    }


    protected function buildFieldsReplacements(array $replace)
    {
        if ($this->option('fields')) {
            $fields = $this->getFields();
            $data = $fields->buildMigration();
        }

        return array_merge($replace, [
            'DummyFields' => $data ?? '',
        ]);

    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (!Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace . $model;
        }

        return $model;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['create', 'm', InputOption::VALUE_REQUIRED, 'Table name'],
            ['fields', null, InputOption::VALUE_OPTIONAL, 'Get fields array, use comma as separator'],
        ];
    }
}


