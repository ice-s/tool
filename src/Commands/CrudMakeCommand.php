<?php

namespace Ices\Tool\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CrudMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:make {--auth} {--table=} {--name=} {--path=} {{--f}} {{--api}}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $modelPath;
    protected $repoPath;
    protected $servicePath;
    protected $resourcePath;
    protected $requestPath;
    protected $controllerApiPath;
    protected $controllerWebPath;
    protected $routeApiPath;
    protected $routeWebPath;

    protected $varName;
    protected $varTable;
    protected $varPath;
    protected $varForce = false;
    protected $varApi = true;
    protected $varWeb = false;
    protected $varAuth = false;
    protected $varNameSpace;

    protected $apiNameSpace = "Api";
    protected $webNameSpace = "Web";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->modelPath         = app_path('Entities/Models');
        $this->repoPath          = app_path('Repositories');
        $this->servicePath       = app_path('Services');
        $this->resourcePath      = app_path('Resources');
        $this->requestPath       = app_path('Http/Requests');
        $this->controllerApiPath = app_path('Http/Controllers/Api');
        $this->controllerWebPath = app_path('Http/Controllers/Web');
        $this->routeApiPath      = base_path('routes/Api');
        $this->routeWebPath      = base_path('routes/Web');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->varName      = $this->option('name');
        $this->varTable     = $this->option('table');
        $this->varPath      = $this->option('path') ? "/" . $this->option('path') : '';
        $this->varNameSpace = $this->option('path') ? '\\' . $this->option('path') : '';
        $this->varForce     = $this->option('f');
        $this->varAuth      = $this->option('auth');

        $this->createBase();
        $this->create();
    }

    protected function createBase()
    {
        if (!file_exists($this->modelPath . "/BaseModel.php")) {
            $this->makeDir($this->modelPath);
            $stubFile = $this->getStub('base/BaseModel.stub');
            $template = file_get_contents($stubFile);
            $template = str_replace([], [], $template);
            file_put_contents($this->modelPath . "/BaseModel.php", $template);
            $this->info('Create new BaseModel');
        } else {
            $this->warn('BaseModel exist!');
        }

        if (!file_exists($this->repoPath . "/BaseRepository.php")) {
            $this->makeDir($this->repoPath);
            $stubFile = $this->getStub('base/BaseRepository.stub');
            $template = file_get_contents($stubFile);
            $template = str_replace([], [], $template);
            file_put_contents($this->repoPath . "/BaseRepository.php", $template);
            $this->info('Create new BaseRepository');
        } else {
            $this->warn('BaseRepository exist!');
        }

        if (!file_exists($this->servicePath . "/BaseService.php")) {
            $this->makeDir($this->servicePath);
            $stubFile = $this->getStub('base/BaseService.stub');
            $template = file_get_contents($stubFile);
            $template = str_replace([], [], $template);
            file_put_contents($this->servicePath . "/BaseService.php", $template);
            $this->info('Create new BaseService');
        } else {
            $this->warn('BaseService exist!');
        }

        if (!file_exists($this->resourcePath . "/BaseResource.php")) {
            $this->makeDir($this->resourcePath);
            $stubFile = $this->getStub('base/BaseResource.stub');
            $template = file_get_contents($stubFile);
            $template = str_replace([], [], $template);
            file_put_contents($this->resourcePath . "/BaseResource.php", $template);
            $this->info('Create new BaseResource');
        } else {
            $this->warn('BaseResource exist!');
        }
    }

    public function create()
    {
        if ($this->varAuth) {
            $this->createAuthController();
            $this->makeFrontendAuth();
        }

        if ($this->varName) {
            $this->createModal();
            $this->createRepository();
            $this->createService();

            if ($this->varApi) {
                $this->createResource();
                $this->createRequest();
                $this->createApiController();
                $this->createApiRoute();
                $this->makeFrontendModule();
            }
        }
    }

    public function createModal()
    {
        $stubFile = $this->getStub('Model.stub');
        $template = file_get_contents($stubFile);

        $dataReplace = [
            'Name'      => $this->varName,
            'NameSpace' => $this->varNameSpace,
            'Table'     => $this->varTable,
        ];

        if ($this->varTable) {
            $dataReplace['fillable'] = $this->getFillableColumns($this->varTable);
        }

        $template = $this->replaceStub($template, $dataReplace);

        if (!file_exists($this->modelPath . $this->varPath . "/" . $this->varName . ".php") || $this->varForce) {
            $this->makeDir($this->modelPath . $this->varPath);
            file_put_contents($this->modelPath . $this->varPath . "/" . $this->varName . ".php", $template);
            $this->info($this->varPath . "/" . $this->varName . ".php has created");
        } else {
            $this->warn($this->varPath . "/" . $this->varName . ".php exist");
        }
    }

    public function createRepository()
    {
        $stubFile    = $this->getStub('Repository.stub');
        $template    = file_get_contents($stubFile);
        $dataReplace = [
            'Name'      => $this->varName,
            'NameSpace' => $this->varNameSpace,
        ];
        $template    = $this->replaceStub($template, $dataReplace);

        if (!file_exists($this->repoPath . $this->varPath . "/" . $this->varName . "Repository.php") || $this->varForce) {
            $this->makeDir($this->repoPath . $this->varPath);
            file_put_contents($this->repoPath . $this->varPath . "/" . $this->varName . "Repository.php", $template);
            $this->info($this->varPath . "/" . $this->varName . "Repository.php has created");
        } else {
            $this->warn($this->varPath . "/" . $this->varName . "Repository.php exist");
        }
    }

    public function createService()
    {
        $stubFile    = $this->getStub('Service.stub');
        $template    = file_get_contents($stubFile);
        $dataReplace = [
            'Name'      => $this->varName,
            'name'      => lcfirst($this->varName),
            'NameSpace' => $this->varNameSpace,
        ];
        $template    = $this->replaceStub($template, $dataReplace);

        if (!file_exists($this->servicePath . $this->varPath . "/" . $this->varName . "Service.php") || $this->varForce) {
            $this->makeDir($this->servicePath . $this->varPath);
            file_put_contents($this->servicePath . $this->varPath . "/" . $this->varName . "Service.php", $template);
            $this->info($this->varPath . "/" . $this->varName . "Service.php has created");
        } else {
            $this->warn($this->varPath . "/" . $this->varName . "Service.php exist");
        }
    }

    public function createResource()
    {
        $stubFile             = $this->getStub('api/Resource.stub');
        $template             = file_get_contents($stubFile);
        $dataReplace          = [
            'Name'      => $this->varName,
            'NameSpace' => $this->varNameSpace,
        ];
        $dataReplace['array'] = $this->getResourceFieldColumns($this->varTable);

        $template = $this->replaceStub($template, $dataReplace);

        if (!file_exists($this->resourcePath . $this->varPath . "/" . $this->varName . "Resource.php") || $this->varForce) {
            $this->makeDir($this->resourcePath . $this->varPath);
            file_put_contents($this->resourcePath . $this->varPath . "/" . $this->varName . "Resource.php", $template);
            $this->info($this->varPath . "/" . $this->varName . "Resource.php has created");
        } else {
            $this->warn($this->varPath . "/" . $this->varName . "Resource.php exist");
        }

    }

    public function createRequest()
    {
        $stubFile    = $this->getStub('api/Request.stub');
        $template    = file_get_contents($stubFile);
        $dataReplace = [
            'Name'      => $this->varName,
            'NameSpace' => $this->varNameSpace,
        ];

        $template = $this->replaceStub($template, $dataReplace);

        if (!file_exists($this->requestPath . $this->varPath . "/" . $this->varName . "FormRequest.php") || $this->varForce) {
            $this->makeDir($this->requestPath . $this->varPath);
            file_put_contents($this->requestPath . $this->varPath . "/" . $this->varName . "FormRequest.php",
                $template);
            $this->info($this->varPath . "/" . $this->varName . "FormRequest.php has created");
        } else {
            $this->warn($this->varPath . "/" . $this->varName . "FormRequest.php exist");
        }
    }

    public function createApiController()
    {
        $stubFile    = $this->getStub('api/Controller.stub');
        $template    = file_get_contents($stubFile);
        $dataReplace = [
            'Name'      => $this->varName,
            'name'      => lcfirst($this->varName),
            'NameSpace' => $this->varNameSpace,
        ];

        $template = $this->replaceStub($template, $dataReplace);
        if (!file_exists($this->controllerApiPath . $this->varPath . "/" . $this->varName . "Controller.php") || $this->varForce) {
            $this->makeDir($this->controllerApiPath . $this->varPath);
            file_put_contents($this->controllerApiPath . $this->varPath . "/" . $this->varName . "Controller.php",
                $template);
            $this->info($this->varPath . "/" . $this->varName . "Controller.php has created");
        } else {
            $this->warn($this->varPath . "/" . $this->varName . "Controller.php exist");
        }
    }

    public function createWebController()
    {
        $stubFile    = $this->getStub('web/Controller.stub');
        $template    = file_get_contents($stubFile);
        $dataReplace = [
            'Name'      => $this->varName,
            'name'      => lcfirst($this->varName),
            'NameSpace' => $this->varNameSpace,
        ];

        $template = $this->replaceStub($template, $dataReplace);
        if (!file_exists($this->controllerWebPath . $this->varPath . "/" . $this->varName . "Controller.php") || $this->varForce) {
            $this->makeDir($this->controllerWebPath . $this->varPath);
            file_put_contents($this->controllerWebPath . $this->varPath . "/" . $this->varName . "Controller.php",
                $template);
            $this->info($this->varPath . "/" . $this->varName . "Controller.php has created");
        } else {
            $this->warn($this->varPath . "/" . $this->varName . "Controller.php exist");
        }
    }

    public function createAuthController()
    {
        $stubFile = $this->getStub('api/AuthController.stub');
        $template = file_get_contents($stubFile);

        if (!file_exists($this->controllerApiPath . "/AuthController.php") || $this->varForce) {
            $this->makeDir($this->controllerApiPath);
            file_put_contents($this->controllerApiPath . "/AuthController.php", $template);
            $this->info("AuthController.php has created");
        } else {
            $this->warn("AuthController.php exist");
        }
    }

    public function createApiRoute()
    {
        $stubFile    = $this->getStub('api/Route.stub');
        $template    = file_get_contents($stubFile);
        $dataReplace = [
            'Name'       => $this->varName,
            'NameSpace'  => $this->varNameSpace,
            'Controller' => $this->varName . "Controller",
            'prefix'     => mb_strtolower($this->varName),
        ];

        $template = $this->replaceStub($template, $dataReplace);
        if (!file_exists($this->routeApiPath . $this->varPath . "/" . $this->varName . ".php") || $this->varForce) {
            $this->makeDir($this->routeApiPath . $this->varPath);
            file_put_contents($this->routeApiPath . $this->varPath . "/" . $this->varName . ".php", $template);
            $this->info($this->varPath . "/" . $this->varName . ".php has created");
        } else {
            $this->warn($this->varPath . "/" . $this->varName . ".php exist");
        }

        File::append(base_path('routes/api.php'),
            "Route::group(['middleware' => 'jwt.auth'], function () {\n    " . 'Routes\Api' . $this->varNameSpace . '\\' . $this->varName . "::route();\n});\n");
    }

    public function createWebRoute()
    {
        $stubFile    = $this->getStub('web/Route.stub');
        $template    = file_get_contents($stubFile);
        $dataReplace = [
            'Name'       => $this->varName,
            'NameSpace'  => $this->varNameSpace,
            'Controller' => $this->varName . "Controller",
            'prefix'     => mb_strtolower($this->varName),
        ];

        $template = $this->replaceStub($template, $dataReplace);
        if (!file_exists($this->routeWebPath . $this->varPath . "/" . $this->varName . ".php") || $this->varForce) {
            $this->makeDir($this->routeWebPath . $this->varPath);
            file_put_contents($this->routeWebPath . $this->varPath . "/" . $this->varName . ".php", $template);
            $this->info($this->varPath . "/" . $this->varName . ".php has created");
        } else {
            $this->warn($this->varPath . "/" . $this->varName . ".php exist");
        }

        File::append(base_path('routes/web.php'),
            'Routes\Web' . $this->varNameSpace . '\\' . $this->varName . "::route();\n");
    }

    public function getFillableColumns($table)
    {
        $cols = DB::getSchemaBuilder()->getColumnListing($table);

        foreach ($cols as &$col) {
            $col = "\n" . "        '" . $col . "'";
        }

        return "[" . implode(',', $cols) . "\n   ]";
    }

    public function getResourceFieldColumns($table)
    {
        $cols = DB::getSchemaBuilder()->getColumnListing($table);

        $arrayString = "[";
        foreach ($cols as $col) {
            $arrayString .= "\n            '$col' => \$this->$col,";
        }
        $arrayString .= "\n        ]";

        return $arrayString;
    }

    public function replaceStub($stubContent, $options = [])
    {
        foreach ($options as $replaceSearch => $replaceTo) {
            $stubContent = str_replace("{{" . $replaceSearch . "}}", $replaceTo, $stubContent);
        }

        return $stubContent;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub($stubName)
    {
        return __DIR__ . '/crud/stub/' . $stubName;
    }

    protected function makeDir($folderPath)
    {
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
    }

    public function makeFrontendAuth()
    {
        if (!file_exists(resource_path('js/config.js'))) {
            $this->recurse_copy(__DIR__ . "/crud/frontend/js", resource_path('js'));
            $this->recurse_copy(__DIR__ . "/crud/frontend/sass", resource_path('sass'));
            copy(__DIR__ . "/crud/frontend/package.json", base_path('package.json'));
            copy(__DIR__ . "/crud/frontend/web.php", base_path('routes/web.php'));
            copy(__DIR__ . "/crud/frontend/welcome.blade.php", resource_path('views/welcome.blade.php'));
        } else {
            $this->warn('VueJS exist!');
        }
    }

    public function makeFrontendModule()
    {
        //TODO
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
