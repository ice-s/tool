<?php

namespace Ices\Tool\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CrudFrontendCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:frontend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->modelPath = app_path('Entities/Models');
        $this->repoPath = app_path('Repositories');
        $this->servicePath = app_path('Services');
        $this->resourcePath = app_path('Resources');
        $this->requestPath = app_path('Http/Requests');
        $this->controllerApiPath = app_path('Http/Controllers/Api');
        $this->controllerWebPath = app_path('Http/Controllers/Web');
        $this->routeApiPath = base_path('routes/Api');
        $this->routeWebPath = base_path('routes/Web');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->recurse_copy(__DIR__."/crud/frontend/js", resource_path('js'));
        $this->recurse_copy(__DIR__."/crud/frontend/sass", resource_path('sass'));
        copy(__DIR__."/crud/frontend/package.json", base_path('package.json'));
        copy(__DIR__."/crud/frontend/web.php", base_path('routes/web.php'));
        copy(__DIR__."/crud/frontend/welcome.blade.php", resource_path('views/welcome.blade.php'));
    }

    function recurse_copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
