<?php

namespace ArkadiuszBachorski\Xmake\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class XmakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xmake';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One command that makes everything for you';

    protected $fields = null;

    protected $modelName = null;

    protected $autoName = true;

    protected $permissions = [
        'model' => true,
        'migration' => true,
        'factory' => true,
        'seeder' => true,
        'request' => true,
        'resource' => true,
        'controller' => true,
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function hasPermission(string $key)
    {
        return $this->permissions[$key];
    }

    protected function askForFields()
    {
        $this->line('I might need fields keys from fields.php file');
        $this->fields = $this->ask('Fields');
    }

    protected function askForPermissions()
    {
        if ($this->confirm('Would you like to create everything?', true)) {
            return;
        } else {
            $this->line('Okay, then choose. Would you like to create...');
            foreach ($this->permissions as $name => $value) {
                $this->permissions[$name] = $this->confirm(Str::ucfirst($name), true);
            }
        }
    }

    protected function askForModelName()
    {
        do {
            $this->line("Model name is necessary for many other commands and can't be empty.");
            $name = $this->ask('Model name');
        } while (empty($name));

        $this->modelName = $name;
    }

    protected function askWhetherShouldNameAutomatically()
    {
        $this->line("Xmake can create names automatically based on Laravel manner and your model name. I.e. with model name Foobar it creates FoobarController and FoobarRequest");
        $this->autoName = $this->confirm('Would you like to create names automatically?', true);
    }

    protected function askForFilename(string $autoName, string $question, string $confirmation = "")
    {
        if ($confirmation !== "" && !$this->confirm($confirmation, true)) {
            return null;
        }

        return $this->autoName ? $autoName : $this->ask($question, $autoName);
    }

    protected function createModel()
    {
        if ($this->hasPermission('model')) {
            $args = [
                'name' => $this->modelName,
            ];

            $this->call('xmake:model', $args);
        }
    }

    protected function createMigration()
    {
        if ($this->hasPermission('migration')) {
            $table = $this->askForFilename(Str::snake(Str::pluralStudly($this->modelName)), 'Migration table to create');
            $name = $this->askForFilename("create_{$table}_table", 'Migration name');

            $args = [
                'name' => $name,
                '--create' => $table,
                '--fields' => $this->fields,
            ];

            $this->call('xmake:migration', $args);
        }
    }

    protected function createFactory()
    {
        if ($this->hasPermission('factory')) {
            $name = $this->askForFilename("{$this->modelName}Factory", 'Factory name');
            $args = [
                'name' => $name,
                '--model' => $this->modelName,
                '--fields' => $this->fields,
            ];

            $this->call('xmake:factory', $args);
        }
    }

    protected function createSeeder()
    {
        if ($this->hasPermission('seeder')) {
            $name = $this->askForFilename("{$this->modelName}Seeder", 'Seeder name');
            $args = [
                'name' => $name,
                '--model' => $this->modelName,
            ];

            $this->call('xmake:seeder', $args);
        }
    }

    protected function createRequest()
    {
        if ($this->hasPermission('request')) {
            $name = $this->askForFilename("{$this->modelName}Request", 'Request name');
            $args = [
                'name' => $name,
                '--fields' => $this->fields,
            ];

            $this->call('xmake:request', $args);
        }
    }

    protected function createResource()
    {
        if ($this->hasPermission('resource')) {
            $name = $this->askForFilename("{$this->modelName}Resource", 'Resource name');
            $args = [
                'name' => $name,
                '--fields' => $this->fields,
            ];

            $this->call('xmake:resource', $args);
        }
    }

    protected function createController()
    {
        if ($this->hasPermission('controller')) {
            $name = $this->askForFilename("{$this->modelName}Controller", "Controller name");
            $api = $this->confirm('Would you like to make API controller?');
            if ($api) {
                $resource = $this->askForFilename("{$this->modelName}Resource", "Resource name for controller", "Would you like to include an external resource in controller?");
            }
            $request = $this->askForFilename("{$this->modelName}Request", "Request name for controller", "Would you like to include an external request in controller?");

            $args = [
                'name' => $name,
                '--model' => $this->modelName,
                '--fields' => $this->fields,
                '--resource' => $resource ?? null,
                '--request' => $request,
                '--api' => $api,
            ];

            $this->call('xmake:controller', $args);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->askForPermissions();
        $this->askForModelName();
        $this->askForFields();
        $this->askWhetherShouldNameAutomatically();
        $this->createModel();
        $this->createMigration();
        $this->createFactory();
        $this->createSeeder();
        $this->createRequest();
        $this->createResource();
        $this->createController();
    }

}