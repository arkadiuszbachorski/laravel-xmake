<?php

namespace ArkadiuszBachorski\Xmake;

use ArkadiuszBachorski\Xmake\Commands\ControllerMakeCommand;
use ArkadiuszBachorski\Xmake\Commands\ModelMakeCommand;
use ArkadiuszBachorski\Xmake\Commands\FactoryMakeCommand;
use ArkadiuszBachorski\Xmake\Commands\MigrationMakeCommand;
use Illuminate\Support\ServiceProvider;
use ArkadiuszBachorski\Xmake\Commands\RequestMakeCommand;


class XmakeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ControllerMakeCommand::class,
                ModelMakeCommand::class,
                MigrationMakeCommand::class,
                FactoryMakeCommand::class,
                RequestMakeCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/config/xmake.php' => config_path('xmake.php'),
        ], 'xmake-config');

        $this->publishes([
            __DIR__ . '/resources/stubs' => base_path(config('xmake.paths.stubs')),
            __DIR__ . '/resources/fields.php' => base_path(config('xmake.paths.fields').'/fields.php'),
        ], 'xmake-resources');
    }

    public function register()
    {
        
    }
}