<?php

namespace ArkadiuszBachorski\Xmake\Commands\MakeCommands;

use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\InputOption;

class FactoryMakeCommand extends ExtendedGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'xmake:factory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model factory';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Factory';

    protected $stubName = "factory.stub";


    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $replace = [];

        $replace = $this->buildModelReplacements($replace);
        $replace = $this->buildFieldsReplacements($replace);

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }


    protected function buildModelReplacements(array $replace)
    {
        $namespaceModel = $this->option('model')
            ? $this->qualifyClass($this->option('model'))
            : trim($this->rootNamespace(), '\\').'\\Model';

        $model = class_basename($namespaceModel);

        return array_merge($replace, [
            'NamespacedDummyModel' => $namespaceModel,
            'DummyModel' => $model,
        ]);
    }

    protected function buildFieldsReplacements(array $replace)
    {
        if ($this->option('fields')) {
            $fields = $this->getFields();
            $factory = $fields->buildFactory();
        } else {
            $factory = $this->prefix("//", 2, true);
        }

        return array_merge($replace, [
            'DummyRules' => $factory,
        ]);
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace(
            ['\\', '/'], '', $this->argument('name')
        );

        return $this->laravel->databasePath()."/factories/{$name}.php";
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The name of the model'],
            ['fields', null, InputOption::VALUE_OPTIONAL, 'Get fields array, use comma as separator'],
        ];
    }
}
