<?php

namespace Ices\Tool\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CrudApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:make {--name=} {--service=} {--table=}  {--path=} {{--f}}';

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
    protected $routePath;

    protected $varName;
    protected $varService;
    protected $varTable;
    protected $varPath;
    protected $varForce = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->modelPath = app_path('Entities\Models');
        $this->repoPath = app_path('Repositories');
        $this->servicePath = app_path('Services');
        $this->resourcePath = app_path('Resources');
        $this->requestPath = app_path('Http\Requests');
        $this->controllerApiPath = app_path('Http\Controllers\Api');
        $this->routePath = base_path('routes');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->varName = $this->option('name');
        $this->varTable = $this->option('table');
        $this->varPath = $this->option('path') ? "\\" . $this->option('path') : '';
        $this->varForce = $this->option('f');

        $this->createBase();
        $this->create();
    }

    protected function createBase()
    {
        if (!file_exists($this->modelPath . "/BaseModel.php")) {
            $this->makeDir($this->modelPath);
            $stubFile = $this->getStub('base\BaseModel.stub');
            $template = file_get_contents($stubFile);
            $template = str_replace([], [], $template);
            file_put_contents($this->modelPath . "/BaseModel.php", $template);
            $this->info('Create new BaseModel');
        } else {
            $this->warn('BaseModel exist!');
        }

        if (!file_exists($this->repoPath . "/BaseRepository.php")) {
            $this->makeDir($this->repoPath);
            $stubFile = $this->getStub('base\BaseRepository.stub');
            $template = file_get_contents($stubFile);
            $template = str_replace([], [], $template);
            file_put_contents($this->repoPath . "/BaseRepository.php", $template);
            $this->info('Create new BaseRepository');
        } else {
            $this->warn('BaseRepository exist!');
        }

        if (!file_exists($this->servicePath . "/BaseService.php")) {
            $this->makeDir($this->servicePath);
            $stubFile = $this->getStub('base\BaseService.stub');
            $template = file_get_contents($stubFile);
            $template = str_replace([], [], $template);
            file_put_contents($this->servicePath . "/BaseService.php", $template);
            $this->info('Create new BaseService');
        } else {
            $this->warn('BaseService exist!');
        }

        if (!file_exists($this->resourcePath . "/BaseResource.php")) {
            $this->makeDir($this->resourcePath);
            $stubFile = $this->getStub('base\BaseResource.stub');
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
        if ($this->varName) {
            $this->createModal();
            $this->createRepository();
            $this->createService();
            $this->createResource();
            $this->createRequest();
            $this->createAuthController();
            $this->createController();
            $this->createRoute();
        } else {
            $this->error('Nothing to crud');
        }
    }

    public function createModal()
    {
        $stubFile = $this->getStub('Model.stub');
        $template = file_get_contents($stubFile);

        $dataReplace = [
            'Name'  => $this->varName,
            'Path'  => $this->varPath,
            'Table' => $this->varTable,
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
        $stubFile = $this->getStub('Repository.stub');
        $template = file_get_contents($stubFile);
        $dataReplace = [
            'Name' => $this->varName,
            'Path' => $this->varPath,
        ];
        $template = $this->replaceStub($template, $dataReplace);

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
        $stubFile = $this->getStub('Service.stub');
        $template = file_get_contents($stubFile);
        $dataReplace = [
            'Name' => $this->varName,
            'name' => lcfirst($this->varName),
            'Path' => $this->varPath,
        ];
        $template = $this->replaceStub($template, $dataReplace);

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
        $stubFile = $this->getStub('Resource.stub');
        $template = file_get_contents($stubFile);
        $dataReplace = [
            'Name' => $this->varName,
            'Path' => $this->varPath,
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
        $stubFile = $this->getStub('Request.stub');
        $template = file_get_contents($stubFile);
        $dataReplace = [
            'Name' => $this->varName,
            'Path' => $this->varPath,
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

    public function createController()
    {
        $stubFile = $this->getStub('Controller.stub');
        $template = file_get_contents($stubFile);
        $dataReplace = [
            'Name' => $this->varName,
            'name' => lcfirst($this->varName),
            'Path' => $this->varPath,
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

    public function createAuthController()
    {
        $stubFile = $this->getStub('AuthController.stub');
        $template = file_get_contents($stubFile);

        if (!file_exists($this->controllerApiPath . "/AuthController.php") || $this->varForce) {
            $this->makeDir($this->controllerApiPath);
            file_put_contents($this->controllerApiPath . "/AuthController.php", $template);
            $this->info("AuthController.php has created");
        } else {
            $this->warn("AuthController.php exist");
        }
    }

    public function createRoute(){
        $stubFile = $this->getStub('Route.stub');
        $template = file_get_contents($stubFile);
        $dataReplace = [
            'Name' => $this->varName,
            'Path' => $this->varPath,
            'Controller' => $this->varName . "Controller",
            'prefix' => mb_strtolower($this->varName),
        ];

        $template = $this->replaceStub($template, $dataReplace);
        if (!file_exists($this->routePath . $this->varPath . "/" . $this->varName . ".php") || $this->varForce) {
            $this->makeDir($this->routePath . $this->varPath);
            file_put_contents($this->routePath . $this->varPath . "/" . $this->varName . ".php",
                $template);
            $this->info($this->varPath . "/" . $this->varName . ".php has created");
        } else {
            $this->warn($this->varPath . "/" . $this->varName . ".php exist");
        }
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
}
