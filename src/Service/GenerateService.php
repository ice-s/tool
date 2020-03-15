<?php

namespace Ices\Tool\Service;

use Illuminate\Support\Facades\File;

class GenerateService
{
    protected $modelPath;
    protected $repoPath;
    protected $servicePath;
    protected $resourcePath;
    protected $requestPath;
    protected $controllerApiPath;
    protected $controllerWebPath;
    protected $routeApiPath;
    protected $routeWebPath;
    protected $frontendComponentsPath;
    protected $frontendRoutesPath;
    protected $columns;

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

    public function __construct()
    {
        $this->modelPath             = app_path('Entities/Models');
        $this->repoPath              = app_path('Repositories');
        $this->servicePath           = app_path('Services');
        $this->resourcePath          = app_path('Resources');
        $this->requestPath           = app_path('Http/Requests');
        $this->controllerApiPath     = app_path('Http/Controllers/Api');
        $this->controllerWebPath     = app_path('Http/Controllers/Web');
        $this->routeApiPath          = base_path('routes/Api');
        $this->routeWebPath          = base_path('routes/Web');
        $this->frontendComponentsPath = resource_path('js/components');
        $this->frontendRoutesPath     = resource_path('js/routes');
    }

    public function generateAuth()
    {
        $this->varAuth = true;
        $this->createAuth();
    }

    public function generate($name, $columns, $table)
    {
        $this->varName = $name;
        $this->columns = $columns;
        $this->varTable = $table;
        $this->varForce = true;
        $this->createBase();
        $this->create();
    }

    public function setUp()
    {
        $this->varName      = $this->option('name');
        $this->varTable     = $this->option('table');
        $this->varPath      = $this->option('path') ? "/" . $this->option('path') : '';
        $this->varNameSpace = $this->option('path') ? '\\' . $this->option('path') : '';
        $this->varForce     = $this->option('f');
        $this->varAuth      = $this->option('auth');
    }

    protected function createBase()
    {
        if (!file_exists($this->modelPath . "/BaseModel.php")) {
            $this->makeDir($this->modelPath);
            $stubFile = $this->getStub('base/BaseModel.stub');
            $template = file_get_contents($stubFile);
            $template = str_replace([], [], $template);
            file_put_contents($this->modelPath . "/BaseModel.php", $template);
        }

        if (!file_exists($this->repoPath . "/BaseRepository.php")) {
            $this->makeDir($this->repoPath);
            $stubFile = $this->getStub('base/BaseRepository.stub');
            $template = file_get_contents($stubFile);
            $template = str_replace([], [], $template);
            file_put_contents($this->repoPath . "/BaseRepository.php", $template);
        }

        if (!file_exists($this->servicePath . "/BaseService.php")) {
            $this->makeDir($this->servicePath);
            $stubFile = $this->getStub('base/BaseService.stub');
            $template = file_get_contents($stubFile);
            $template = str_replace([], [], $template);
            file_put_contents($this->servicePath . "/BaseService.php", $template);
        }

        if (!file_exists($this->resourcePath . "/BaseResource.php")) {
            $this->makeDir($this->resourcePath);
            $stubFile = $this->getStub('base/BaseResource.stub');
            $template = file_get_contents($stubFile);
            $template = str_replace([], [], $template);
            file_put_contents($this->resourcePath . "/BaseResource.php", $template);
        }
    }

    public function createAuth()
    {
        if ($this->varAuth) {
            $this->createAuthController();
            $this->makeFrontendAuth();
        }
    }

    public function create()
    {
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
            $dataReplace['fillable'] = $this->getFillableColumns();
        }

        $template = $this->replaceStub($template, $dataReplace);

        if (!file_exists($this->modelPath . $this->varPath . "/" . $this->varName . ".php") || $this->varForce) {
            $this->makeDir($this->modelPath . $this->varPath);
            file_put_contents($this->modelPath . $this->varPath . "/" . $this->varName . ".php", $template);
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
        $dataReplace['array'] = $this->getResourceFieldColumns();

        $template = $this->replaceStub($template, $dataReplace);

        if (!file_exists($this->resourcePath . $this->varPath . "/" . $this->varName . "Resource.php") || $this->varForce) {
            $this->makeDir($this->resourcePath . $this->varPath);
            file_put_contents($this->resourcePath . $this->varPath . "/" . $this->varName . "Resource.php", $template);
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
        }
    }

    public function createAuthController()
    {
        $stubFile = $this->getStub('api/AuthController.stub');
        $template = file_get_contents($stubFile);

        if (!file_exists($this->controllerApiPath . "/AuthController.php") || $this->varForce) {
            $this->makeDir($this->controllerApiPath);
            file_put_contents($this->controllerApiPath . "/AuthController.php", $template);
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
        }

        File::append(base_path('routes/web.php'),
            'Routes\Web' . $this->varNameSpace . '\\' . $this->varName . "::route();\n");
    }

    public function getFillableColumns()
    {
        $colsArr = [];
        foreach ($this->columns as $key => $col) {
            if (isset($col['fillable'])) {
                $colsArr[] = "\n" . "        '" . $col['name'] . "'";
            }
        }

        return "[" . implode(',', $colsArr) . "\n   ]";
    }

    public function getResourceFieldColumns()
    {
        $cols = array_keys($this->columns);

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

    public function replaceStubVue($stubContent, $options = [])
    {
        foreach ($options as $replaceSearch => $replaceTo) {
            $stubContent = str_replace("{{{" . $replaceSearch . "}}}", $replaceTo, $stubContent);
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
        return __DIR__ . '/../Commands/crud/stub/' . $stubName;
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
            $this->recurse_copy(__DIR__ . "/../Commands/crud/frontend/js", resource_path('js'));
            $this->recurse_copy(__DIR__ . "/../Commands/crud/frontend/sass", resource_path('sass'));
            copy(__DIR__ . "/../Commands/crud/frontend/package.json", base_path('package.json'));
            copy(__DIR__ . "/../Commands/crud/frontend/web.php", base_path('routes/web.php'));
            copy(__DIR__ . "/../Commands/crud/frontend/welcome.blade.php", resource_path('views/welcome.blade.php'));
        }
    }

    public function makeFrontendModule()
    {
        $this->makeVueIndex();
        $this->makeVueCreate();
        $this->makeVueEdit();
        $this->makeVueRoute();
    }

    public function makeVueIndex()
    {
        $stubIndex   = $this->getStub('frontend/component/index.vue.stub');
        $template    = file_get_contents($stubIndex);
        $dataReplace = [
            'displayName'  => $this->varName,
            'name'         => mb_strtolower($this->varName),
            'columnHeader' => $this->getVueColumnHeader(),
            'columnItem'   => $this->getVueColumnItem()
        ];
        $template    = $this->replaceStubVue($template, $dataReplace);
        if (!file_exists($this->frontendComponentsPath . "/" . mb_strtolower($this->varName) . "/Index.vue") || $this->varForce) {
            $this->makeDir($this->frontendComponentsPath . "/" . mb_strtolower($this->varName));
            file_put_contents($this->frontendComponentsPath . "/" . mb_strtolower($this->varName) . "/Index.vue",
                $template);
        }
    }

    public function makeVueCreate()
    {
        $stubCreate  = $this->getStub('frontend/component/create.vue.stub');
        $template    = file_get_contents($stubCreate);
        $dataReplace = [
            'displayName' => $this->varName,
            'name'        => mb_strtolower($this->varName),
            'form'        => $this->getForm(),
            'filter'        => $this->getObjectJavaScript(),
        ];
        $template    = $this->replaceStubVue($template, $dataReplace);
        if (!file_exists($this->frontendComponentsPath . "/" . mb_strtolower($this->varName) . "/Create.vue") || $this->varForce) {
            $this->makeDir($this->frontendComponentsPath . "/" . mb_strtolower($this->varName));
            file_put_contents($this->frontendComponentsPath . "/" . mb_strtolower($this->varName) . "/Create.vue",
                $template);
        }
    }

    public function makeVueEdit()
    {
        $stubEdit = $this->getStub('frontend/component/edit.vue.stub');


        $template    = file_get_contents($stubEdit);
        $dataReplace = [
            'displayName' => $this->varName,
            'name'        => mb_strtolower($this->varName),
            'form'        => $this->getFormEdit(),
            'filter'        => $this->getObjectJavaScript(),
        ];
        $template    = $this->replaceStubVue($template, $dataReplace);
        if (!file_exists($this->frontendComponentsPath . "/" . mb_strtolower($this->varName) . "/Edit.vue") || $this->varForce) {
            $this->makeDir($this->frontendComponentsPath . "/" . mb_strtolower($this->varName));
            file_put_contents($this->frontendComponentsPath . "/" . mb_strtolower($this->varName) . "/Edit.vue",
                $template);
        }
    }

    public function makeVueRoute()
    {
        $stubRoute   = $this->getStub('frontend/Route.stub');
        $template    = file_get_contents($stubRoute);
        $dataReplace = [
            'name' => mb_strtolower($this->varName)
        ];
        $template    = $this->replaceStub($template, $dataReplace);
        if (!file_exists($this->frontendRoutesPath . "/" . mb_strtolower($this->varName) . "/" . $this->varName . ".js") || $this->varForce) {
            $this->makeDir($this->frontendRoutesPath . "/" . mb_strtolower($this->varName));
            file_put_contents($this->frontendRoutesPath . "/" . mb_strtolower($this->varName) . "/" . $this->varName . ".js",
                $template);
        }

        $file_contents = file_get_contents(resource_path('js/routes/routes.js'));
        $strRoute      = 'import ' . $this->varName . ' from "./' . mb_strtolower($this->varName) . '/' . $this->varName . '";
routes = [...routes, ...' . $this->varName . '];';
        $strRouteRep   = $strRoute . '

const router';

        $file_contents = str_replace($strRoute, '', $file_contents);
        $file_contents = preg_replace("/[\r\n]+/", "\n", $file_contents);
        $file_contents = str_replace("const router", $strRouteRep, $file_contents);
        file_put_contents(resource_path('js/routes/routes.js'), $file_contents);
    }

    public function getForm(){
        $str = '';
        foreach ($this->columns as $col) {
            if (isset($col['fillable']) && !isset($col['primary'])) {
                $str .= $this->generateColByType($col['name'], $col['display'], $col['type']);
            }
        }

        return $str;
    }

    public function getFormEdit()
    {
        $str = '';

        foreach ($this->columns as $col) {
            if (isset($col['fillable']) && !isset($col['primary'])) {
                $str .= $this->generateColEditByType($col['name'], $col['display'], $col['type']);
            }
        }

        return $str;
    }

    public function getObjectJavaScript(){
        $object = "{";
        foreach ($this->columns as $col) {
            if (isset($col['fillable']) && !isset($col['primary'])) {
                $object .= $col['name']." : app.object.".$col['name'].",";
            }

        }
        $object .= "}";

        return $object;
    }

    public function generateColByType($name, $displayName, $type)
    {
        $str = '';
        switch ($type) {
            case 'bigint':
                $str = "\n".'<div class="form-group row">
                    <label for="'.$name.'" class="col-sm-2 col-form-label">'.$displayName.'</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="'.$name.'" name="'.$name.'" v-model="object.' . $name . '">
                    </div>
                </div>';
                break;
            case 'int':
                $str = "\n".'<div class="form-group row">
                    <label for="'.$name.'" class="col-sm-2 col-form-label">'.$displayName.'</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="'.$name.'" name="'.$name.'" v-model="object.' . $name . '">
                    </div>
                </div>';
                break;
            case 'string':
                $str = "\n".'<div class="form-group row">
                    <label for="'.$name.'" class="col-sm-2 col-form-label">'.$displayName.'</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="'.$name.'" name="'.$name.'" v-model="object.' . $name . '">
                    </div>
                </div>';
                break;

            case 'float':
                $str = "\n".'<div class="form-group row">
                    <label for="'.$name.'" class="col-sm-2 col-form-label">'.$displayName.'</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="'.$name.'" name="'.$name.'" v-model="object.' . $name . '">
                    </div>
                </div>';
                break;

            case 'date':
                $str = "\n".'<div class="form-group row">
                    <label for="'.$name.'" class="col-sm-2 col-form-label">'.$displayName.'</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="'.$name.'" name="'.$name.'" v-model="object.' . $name . '">
                    </div>
                </div>';
                break;

            case 'datetime':
                $str = "\n".'<div class="form-group row">
                    <label for="'.$name.'" class="col-sm-2 col-form-label">'.$displayName.'</label>
                    <div class="col-sm-10">
                        <input type="datetime-local" class="form-control" id="'.$name.'" name="'.$name.'" v-model="object.' . $name . '">
                    </div>
                </div>';
                break;
            case 'text':
                $str = "\n".'<div class="form-group row">
                    <label for="'.$name.'" class="col-sm-2 col-form-label">'.$displayName.'</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="'.$name.'" name="'.$name.'" rows="3" v-model="object.' . $name . '"></textarea>
                    </div>
                </div>';
                break;
        }

        return $str;
    }

    public function generateColEditByType($name, $displayName, $type)
    {
        $str = '';
        switch ($type) {
            case 'bigint':
                $str = "\n".'<div class="form-group row">
                    <label for="'.$name.'" class="col-sm-2 col-form-label">'.$displayName.'</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="'.$name.'" name="'.$name.'" v-model="object.' . $name . '">
                    </div>
                </div>';
                break;
            case 'int':
                $str = "\n".'<div class="form-group row">
                    <label for="'.$name.'" class="col-sm-2 col-form-label">'.$displayName.'</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="'.$name.'" name="'.$name.'" v-model="object.' . $name . '">
                    </div>
                </div>';
                break;
            case 'string':
                $str = "\n".'<div class="form-group row">
                    <label for="'.$name.'" class="col-sm-2 col-form-label">'.$displayName.'</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="'.$name.'" name="'.$name.'" v-model="object.' . $name . '">
                    </div>
                </div>';
                break;

            case 'float':
                $str = "\n".'<div class="form-group row">
                    <label for="'.$name.'" class="col-sm-2 col-form-label">'.$displayName.'</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="'.$name.'" name="'.$name.'" v-model="object.' . $name . '">
                    </div>
                </div>';
                break;

            case 'date':
                $str = "\n".'<div class="form-group row">
                    <label for="'.$name.'" class="col-sm-2 col-form-label">'.$displayName.'</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="'.$name.'" name="'.$name.'" v-model="object.' . $name . '">
                    </div>
                </div>';
                break;

            case 'datetime':
                $str = "\n".'<div class="form-group row">
                    <label for="'.$name.'" class="col-sm-2 col-form-label">'.$displayName.'</label>
                    <div class="col-sm-10">
                        <input type="datetime-local" class="form-control" id="'.$name.'" name="'.$name.'" v-model="object.' . $name . '">
                    </div>
                </div>';
                break;
            case 'text':
                $str = "\n".'<div class="form-group row">
                    <label for="'.$name.'" class="col-sm-2 col-form-label">'.$displayName.'</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="'.$name.'" name="'.$name.'" rows="3" v-model="object.' . $name . '"></textarea>
                    </div>
                </div>';
                break;
        }

        return $str;
    }

    public function getVueColumnHeader()
    {
        $str   = '';
        $first = true;
        foreach ($this->columns as $col) {
            if (!$first) {
                $str .= "\n" . str_repeat(' ', 40);
            }
            $str .= "<td>" . $col['display'] . '</td>';

            $first = false;
        }

        return $str;
    }

    public function getVueColumnItem()
    {
        $str   = '';
        $first = true;
        foreach ($this->columns as $key => $col) {
            if (!$first) {
                $str .= "\n" . str_repeat(' ', 40);
            }
            $str   .= '<td>{{ item.' . $col['name'] . ' }}</td>';
            $first = false;
        }

        return $str;
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
