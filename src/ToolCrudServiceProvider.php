<?php

namespace Ices\Tool;

use Ices\Tool\Commands\CrudDestroyCommand;
use Ices\Tool\Commands\CrudFrontendCommand;
use Ices\Tool\Commands\CrudMakeCommand;
use Illuminate\Support\ServiceProvider;

class ToolCrudServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'CRUD');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->register(RouteCrudServiceProvider::class);
        $this->registerCommands();
    }

    function registerCommands()
    {
        $this->commands([
            CrudMakeCommand::class,
            CrudDestroyCommand::class
        ]);
    }
}
