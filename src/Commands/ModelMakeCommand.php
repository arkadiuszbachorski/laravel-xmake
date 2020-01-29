<?php

namespace Quez\Xmake\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ModelMakeCommand extends ExtendedGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'xmake:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    protected $stubName = 'model.stub';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (parent::handle() === false && ! $this->option('force')) {
            return false;
        }

        if ($this->option('all')) {
            $this->input->setOption('factory', true);
            $this->input->setOption('migration', true);
            $this->input->setOption('controller', true);
            $this->input->setOption('request', true);
        }

        if ($this->option('factory')) {
            $this->createFactory();
        }

        if ($this->option('migration')) {
            $this->createMigration();
        }

        if ($this->option('request')) {
            $this->createRequest();
        }

        if ($this->option('controller')) {
            $this->createController();
        }
    }

    /**
     * Create a model factory for the model.
     *
     * @return void
     */
    protected function createFactory()
    {
        $factory = Str::studly(class_basename($this->argument('name')));

        $args = [
            'name' => "{$factory}Factory",
            '--model' => $this->qualifyClass($this->getNameInput()),
        ];

        if ($this->option('fields')) {
            $args['--fields'] = $this->option('fields');
        }

        $this->call('xmake:factory', $args);
    }

    /**
     * Create a migration file for the model.
     *
     * @return void
     */
    protected function createMigration()
    {
        $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));


        $args = [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ];

        if ($this->option('fields')) {
            $args['--fields'] = $this->option('fields');
        }

        $this->call('xmake:migration', $args);
    }

    /**
     * Create a controller for the model.
     *
     * @return void
     */
    protected function createController()
    {
        $controller = Str::studly(class_basename($this->argument('name')));

        $modelName = $this->qualifyClass($this->getNameInput());

        $args = [
            'name' => "{$controller}Controller",
            '--model' => $modelName,
            '--fields' => $this->option('fields') ?? null,
            '--api' => !!$this->option('api'),
            '--request' => $this->option('request') ? $this->generateRequestName() : null,
        ];

        $this->call('xmake:controller', $args);
    }

    protected function generateRequestName()
    {
        $model = class_basename($this->argument('name'));

        return "{$model}Request";
    }

    /**
     * Create a migration file for the model.
     *
     * @return void
     */
    protected function createRequest()
    {
        $args = [
            'name' => $this->generateRequestName(),
        ];

        if ($this->option('fields')) {
            $args['--fields'] = $this->option('fields');
        }

        $this->call('xmake:request', $args);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],

            ['api', null, InputOption::VALUE_NONE, 'Create API Controller'],

            ['fields', null, InputOption::VALUE_OPTIONAL, 'Get fields array, use comma as separator'],

            ['request', 'r', InputOption::VALUE_NONE, 'Create a new request file for the model'],

            ['all', 'a', InputOption::VALUE_NONE, 'Generate a migration, factory, and resource controller for the model'],

            ['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'],

            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],

            ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model'],

        ];
    }
}
