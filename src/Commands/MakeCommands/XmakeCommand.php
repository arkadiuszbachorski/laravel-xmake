<?php

namespace ArkadiuszBachorski\Xmake\Commands\MakeCommands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class XmakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'xmake';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One command that makes everything for you';

    protected $fields = null;

    protected $modelName = null;

    protected $autoName = true;

    protected $permissions = null;

    protected $isApi = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->permissions = config('xmake.createEverything');
    }

    protected function hasPermission(string $key)
    {
        return $this->permissions[$key];
    }


    protected function askForFilename(string $autoName, string $question, string $permissionKey = "", string $confirmation = "")
    {
        $hasNoPermission = $permissionKey !== "" && $this->isInstant() && !$this->hasPermission($permissionKey);
        $doesntWantToCreateResource = $confirmation !== "" && !$this->isInstant() && !$this->confirm($confirmation, true);
        if ($hasNoPermission || $doesntWantToCreateResource) {
            return null;
        }

        return $this->autoName ? $autoName : $this->ask($question, $autoName);
    }

    protected function isInstant()
    {
        return $this->option('instant');
    }

    protected function askForPermissions()
    {
        if ($this->isInstant()) {
            if (!$this->option('all')) {
                $this->permissions = [
                    'model' => $this->option('model'),
                    'migration' => $this->option('migration'),
                    'factory' => $this->option('factory'),
                    'seeder' => $this->option('seeder'),
                    'request' => $this->option('request'),
                    'resource' => $this->isApi ? $this->option('resource') : false,
                    'controller' => $this->option('controller'),
                ];
            }
        } else {
            if ($this->confirm('Would you like to create everything?', true)) {
                return;
            } else {
                $this->line('Okay, then choose. Would you like to create...');
                foreach ($this->permissions as $name => $value) {
                    if ($name === "resource" && !$this->isApi) {
                        $this->permissions[$name] = false;
                    } else {
                        $this->permissions[$name] = $this->confirm(Str::ucfirst($name), true);
                    }
                }
            }
        }
    }

    protected function askForFields()
    {
        if ($this->isInstant()) {
            $this->fields = $this->option('fields');
        } else {
            $this->line('I might need fields keys from fields.php file. Separate them with comma without space.');
            $this->fields = $this->ask('Fields');
        }

    }

    protected function askForModelName()
    {
        if ($this->isInstant()) {
            $this->modelName = $this->option('modelName');
        } else {
            do {
                $this->line("Model name is necessary for many commands and can't be empty.");
                $name = $this->ask('Model name');
            } while (empty($name));

            $this->modelName = $name;
        }
    }

    protected function askIfIsApi()
    {
        if ($this->isInstant()) {
            $this->isApi = $this->option('api');
        } else {
            $this->line("You can create controller with methods returning JSON rather than views and enable resource creation if you are building APIs.");
            $this->isApi = $this->confirm("Is it for API?", true);
        }
    }

    protected function askWhetherShouldNameAutomatically()
    {
        if ($this->isInstant()) {
            $this->autoName = true;
        } else {
            $this->line("Xmake can create names automatically based on Laravel manner and your model name. I.e. with model name Foobar it creates FoobarController and FoobarRequest");
            $this->autoName = $this->confirm('Would you like to create names automatically?', true);
        }
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
        if ($this->hasPermission('resource') && $this->isApi) {
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
            if ($this->isApi) {
                $resource = $this->askForFilename("{$this->modelName}Resource", "Resource name for controller", "resource", "Would you like to include an external resource in controller?");
            }
            $request = $this->askForFilename("{$this->modelName}Request", "Request name for controller", "request", "Would you like to include an external request in controller?");

            $args = [
                'name' => $name,
                '--model' => $this->modelName,
                '--fields' => $this->fields,
                '--resource' => $resource ?? null,
                '--request' => $request,
                '--api' => $this->isApi,
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
        $this->askForModelName();
        $this->askForFields();
        $this->askIfIsApi();
        $this->askForPermissions();
        $this->askWhetherShouldNameAutomatically();
        $this->createModel();
        $this->createMigration();
        $this->createFactory();
        $this->createSeeder();
        $this->createRequest();
        $this->createResource();
        $this->createController();
    }


    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['instant', 'i', InputOption::VALUE_NONE, "Don't run interactive shell and make everything based on options"],
            ['modelName', null, InputOption::VALUE_REQUIRED, 'Required model name'],
            ['fields', null, InputOption::VALUE_OPTIONAL, 'Get fields array, use comma as separator'],
            ['all', null, InputOption::VALUE_NONE, 'Create everything based on your config'],
            ['api', null, InputOption::VALUE_NONE, 'Create API version of Controller and enable resource creating'],
            ['model', null, InputOption::VALUE_NONE, 'Create model with given modelName'],
            ['migration', null, InputOption::VALUE_NONE, 'Create migration with given fields prepared or filled'],
            ['factory', null, InputOption::VALUE_NONE, 'Create factory with given fields prepared of filled'],
            ['seeder', null, InputOption::VALUE_NONE, 'Create seeder that invokes factory'],
            ['request', null, InputOption::VALUE_NONE, 'Create request with given fields prepared or filled'],
            ['resource', null, InputOption::VALUE_NONE, 'Create resource with given fields prepared of filled'],
            ['controller', null, InputOption::VALUE_NONE, 'Create controller with various options - request, resource and api'],
        ];
    }

}