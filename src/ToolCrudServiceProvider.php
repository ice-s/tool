<?php

namespace Ices\Tool;

use Ices\Tool\Commands\CrudApiCommand;
use Ices\Tool\Commands\CrudDestroyCommand;
use Illuminate\Support\ServiceProvider;

class ToolCrudServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {

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
        ]);
    }
}