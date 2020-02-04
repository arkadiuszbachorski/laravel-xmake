<?php

namespace ArkadiuszBachorski\Xmake;

use ArkadiuszBachorski\Xmake\Commands\MakeCommands\ControllerMakeCommand;
use ArkadiuszBachorski\Xmake\Commands\MakeCommands\ModelMakeCommand;
use ArkadiuszBachorski\Xmake\Commands\MakeCommands\FactoryMakeCommand;
use ArkadiuszBachorski\Xmake\Commands\MakeCommands\MigrationMakeCommand;
use ArkadiuszBachorski\Xmake\Commands\MakeCommands\ResourceMakeCommand;
use ArkadiuszBachorski\Xmake\Commands\MakeCommands\SeederMakeCommand;
use ArkadiuszBachorski\Xmake\Commands\MakeCommands\RequestMakeCommand;
use ArkadiuszBachorski\Xmake\Commands\MakeCommands\XmakeCommand;
use Illuminate\Support\ServiceProvider;


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
                SeederMakeCommand::class,
                ResourceMakeCommand::class,
                XmakeCommand::class,
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