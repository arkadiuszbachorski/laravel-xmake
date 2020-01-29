<?php

namespace Quez\Xmake\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ControllerMakeCommand extends ExtendedGeneratorCommand
{
    use BuildValidation;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'xmake:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    protected $stubName = "controller.model.stub";

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('model')) {
            $this->stubName = 'controller.model.stub';
        } else {
            dd("If you don't need any of advanced options - use native Artisan Make command then.");
        }

        if ($this->option('api')) {
            $this->stubName = str_replace('.stub', '.api.stub', $this->stubName);
        }

        if ($this->option('request')) {
            $this->stubName = str_replace('.stub', '.request.stub', $this->stubName);
        }


        return $this->getStubDir();
    }


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
        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

        $replace = $this->buildDocReplacements($replace);

        $replace = $this->buildFieldsReplacements($replace);

        $replace = $this->buildMethodsReplacements($replace);

        $replace = $this->buildRequestReplacements($replace);

        $replace["use {$controllerNamespace}\Controller;\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Build the replacements for a parent controller.
     *
     * @return array
     */
    protected function buildParentReplacements()
    {
        $parentModelClass = $this->parseModel($this->option('parent'));

        if (!class_exists($parentModelClass)) {
            if ($this->confirm("A {$parentModelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('xmake:model', ['name' => $parentModelClass]);
            }
        }

        return [
            'ParentDummyFullModelClass' => $parentModelClass,
            'ParentDummyModelClass' => class_basename($parentModelClass),
            'ParentDummyModelVariable' => lcfirst(class_basename($parentModelClass)),
        ];
    }

    /**
     * Build the model replacement values.
     *
     * @param  array $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('model'));
        $modelBase = class_basename($modelClass);
        $modelLower = lcfirst($modelBase);
        $modelLowerPlural = Str::plural($modelLower);

        if (!class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:xmodel', ['name' => $modelClass]);
            }
        }

        return array_merge($replace, [
            'DummyModelVariablePlural' => $modelLowerPlural,
            'DummyFullModelClass' => $modelClass,
            'DummyModelClass' => $modelBase,
            'DummyModelMainViewFolder' => $modelLower,
            'DummyModelVariable' => $modelLower,
        ]);
    }

    protected function buildFieldsReplacements(array $replace)
    {
        if ($this->option('fields')) {
            $this->getFieldsDataIfEmpty();
            $validation = $this->prefix('$data = $request->validate([' , 2, false);
            $validation .= $this->buildValidation();
            $validation .= $this->prefix("]);", 2, true);
        } else {
            $validation = $this->prefix('$data = $request->validate([]);', 2, false);
        }

        return array_merge($replace, [
            'DummyValidation' => $validation,
        ]);

    }

    /**
     * Build the docs replacement values.
     *
     * @param  array $replace
     * @return array
     */
    protected function buildDocReplacements(array $replace)
    {
        return array_merge($replace, [
            'DummyIndexDoc' => config('xmake.controller.docs.index'),
            'DummyCreateDoc' => config('xmake.controller.docs.create'),
            'DummyStoreDoc' => config('xmake.controller.docs.store'),
            'DummyShowDoc' => config('xmake.controller.docs.show'),
            'DummyEditDoc' => config('xmake.controller.docs.edit'),
            'DummyUpdateDoc' => config('xmake.controller.docs.update'),
            'DummyDestroyDoc' => config('xmake.controller.docs.destroy'),
        ]);
    }

    protected function buildMethodsReplacements(array $replace)
    {
        return array_merge($replace, [
            'DummyIndexMethod' => config('xmake.controller.methods.index'),
            'DummyCreateMethod' => config('xmake.controller.methods.create'),
            'DummyStoreMethod' => config('xmake.controller.methods.store'),
            'DummyShowMethod' => config('xmake.controller.methods.show'),
            'DummyEditMethod' => config('xmake.controller.methods.edit'),
            'DummyUpdateMethod' => config('xmake.controller.methods.update'),
            'DummyDestroyMethod' => config('xmake.controller.methods.destroy'),
        ]);
    }

    protected function buildRequestReplacements(array $replace)
    {
        if ($this->option('request')) {
            $request = $this->option('request');
            $namespacedRequest = "App\Http\Requests\\$request";

            if (!class_exists($namespacedRequest)) {
                if ($this->confirm("A {$request} request does not exist. Do you want to generate it?", true)) {
                    $this->call('xmake:request', [
                        'name' => $request,
                        '--fields' => $this->option('fields'),
                    ]);
                }
            }
        }

        return array_merge($replace, [
            'DummyRequest' => $request ?? '',
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
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource controller for the given model.'],
            ['request', 'r', InputOption::VALUE_OPTIONAL, 'Generate controller with injected given request'],
            ['api', null, InputOption::VALUE_NONE, 'Change responses to API'],
            ['fields', null, InputOption::VALUE_NONE, 'Get fields array, use comma as separator'],
        ];
    }
}


