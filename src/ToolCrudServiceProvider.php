<?php

namespace Ices\Tool;

use Ices\Tool\Commands\CrudApiCommand;
use Ices\Tool\Commands\CrudDestroyCommand;
use Ices\Tool\Commands\CrudFrontendCommand;
use Illuminate\Support\ServiceProvider;

class ToolCrudServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'CRUD');

        $this->app->register(RouteCrudServiceProvider::class);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerCommands();
    }

    function registerCommands()
    {
        $this->commands([
            CrudApiCommand::class,
            CrudDestroyCommand::class,
            CrudFrontendCommand::class,
        ]);
    }
}
