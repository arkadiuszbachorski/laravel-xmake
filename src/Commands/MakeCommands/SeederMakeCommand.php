<?php

namespace ArkadiuszBachorski\Xmake\Commands\MakeCommands;

use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\InputOption;

class SeederMakeCommand extends ExtendedGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'xmake:seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new database seeder';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Seeder';

    protected $stubName = "seeder.stub";


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
        $replace = $this->buildAmountReplacement($replace);

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

    protected function buildAmountReplacement(array $replace)
    {
        $amount = $this->option('amount') ?? config('xmake.seeder.defaultAmount');

        return array_merge($replace, [
            'DummyAmount' => $amount,
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

        return $this->laravel->databasePath()."/seeds/{$name}.php";
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
            ['amount', null, InputOption::VALUE_OPTIONAL, 'Amount of created'],
        ];
    }
}
