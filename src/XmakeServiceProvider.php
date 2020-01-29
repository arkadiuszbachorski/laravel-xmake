<?php

namespace Quez\Xmake;

use Quez\Xmake\Commands\ControllerMakeCommand;
use Quez\Xmake\Commands\ModelMakeCommand;
use Quez\Xmake\Commands\FactoryMakeCommand;
use Quez\Xmake\Commands\MigrationMakeCommand;
use Illuminate\Support\ServiceProvider;
use Quez\Xmake\Commands\RequestMakeCommand;


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