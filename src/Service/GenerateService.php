<?php

namespace Ices\Tool\Service;

use Ices\Tool\Helper\FormHelper;
use Ices\Tool\Helper\ValidateHelper;
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

    protected $formHelper;
    protected $validateHelper;

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
        $this->frontendComponentsPath = resource_path('vue/components/admin');
        $this->frontendRoutesPath     = resource_path('vue/routes');

        $this->formHelper = new FormHelper();
        $this->validateHelper = new ValidateHelper();
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

    public function createAuthController($force = false)
    {
        $stubFile = $this->getStub('api/AuthController.stub');
        $template = file_get_contents($stubFile);

        if (!file_exists($this->controllerApiPath . "/AuthController.php") || $force) {
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
        return __DIR__ . '/../template/stub/' . $stubName;
    }

    protected function makeDir($folderPath)
    {
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
    }

    public function makeFrontendAuth($force = false)
    {
        if (!file_exists(resource_path('js/config.js')) || $force == true) {
            $this->createAuthController($force);

            $this->recurse_copy(__DIR__ . "/../template/vue", resource_path('vue'));
            copy(__DIR__ . "/../template/frontend/webpack.mix.js", base_path('webpack.mix.js'));
            copy(__DIR__ . "/../template/frontend/package.json", base_path('package.json'));
            copy(__DIR__ . "/../template/frontend/web.php", base_path('routes/web.php'));
            copy(__DIR__ . "/../template/frontend/vue.blade.php", resource_path('views/vue.blade.php'));
        }
    }

    public function makeFrontendModule()
    {
        $this->makeVueIndex();
        $this->makeVueCreate();
        $this->makeVueEdit2();

        $this->makeVueRoute();
        $this->makeVueNav();
    }

    public function makeVueIndex()
    {
        $stubIndex   = $this->getStub('frontend/component/index.vue.stub');
        $template    = file_get_contents($stubIndex);
        $dataReplace = [
            'name'        => mb_strtolower($this->varName),
            'displayName'  => $this->varName,
        ];
        $template    = $this->replaceStubVue($template, $dataReplace);
        if (!file_exists($this->frontendComponentsPath . "/" . mb_strtolower($this->varName) . "/Index.vue") || $this->varForce) {
            $this->makeDir($this->frontendComponentsPath . "/" . mb_strtolower($this->varName));
            file_put_contents($this->frontendComponentsPath . "/" . mb_strtolower($this->varName) . "/Index.vue",
                $template);
        }

        $stubIndex   = $this->getStub('frontend/component/Table.vue.stub');
        $template    = file_get_contents($stubIndex);
        $dataReplace = [
            'name'         => mb_strtolower($this->varName),
            'column'   => json_encode($this->getTableColumnJson()),
            'filterObject'   => json_encode($this->getFilterJson())
        ];
        $template    = $this->replaceStubVue($template, $dataReplace);
        if (!file_exists($this->frontendComponentsPath . "/" . mb_strtolower($this->varName) . "/Table.vue") || $this->varForce) {
            $this->makeDir($this->frontendComponentsPath . "/" . mb_strtolower($this->varName));
            file_put_contents($this->frontendComponentsPath . "/" . mb_strtolower($this->varName) . "/Table.vue",
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
            'fieldModelItems' => $this->formHelper->createForm($this->columns),
            'convertDataSubmit' => $this->formHelper->convertDataSubmit($this->columns),
            'rulesValidate' => $this->validateHelper->createValidate($this->columns)
        ];
        $template    = $this->replaceStubVue($template, $dataReplace);
        if (!file_exists($this->frontendComponentsPath . "/" . mb_strtolower($this->varName) . "/Create.vue") || $this->varForce) {
            $this->makeDir($this->frontendComponentsPath . "/" . mb_strtolower($this->varName));
            file_put_contents($this->frontendComponentsPath . "/" . mb_strtolower($this->varName) . "/Create.vue",
                $template);
        }
    }

    public function makeVueEdit2()
    {
        $stubCreate  = $this->getStub('frontend/component/edit.vue.stub');
        $template    = file_get_contents($stubCreate);
        $dataReplace = [
            'displayName' => $this->varName,
            'name'        => mb_strtolower($this->varName),
            'fieldModelItems' => $this->formHelper->createForm($this->columns, true),
            'convertMoment' => $this->formHelper->convertMoment($this->columns),
            'convertDataSubmit' => $this->formHelper->convertDataSubmit($this->columns, true),
            'rulesValidate' => $this->validateHelper->createValidate($this->columns),
        ];
        $template    = $this->replaceStubVue($template, $dataReplace);
        if (!file_exists($this->frontendComponentsPath . "/" . mb_strtolower($this->varName) . "/Edit.vue") || $this->varForce) {
            $this->makeDir($this->frontendComponentsPath . "/" . mb_strtolower($this->varName));
            file_put_contents($this->frontendComponentsPath . "/" . mb_strtolower($this->varName) . "/Edit.vue",
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
        if (!file_exists($this->frontendRoutesPath . "/admin/" . mb_strtolower($this->varName) . "/" . $this->varName . ".js") || $this->varForce) {
            $this->makeDir($this->frontendRoutesPath . "/admin/" . mb_strtolower($this->varName));
            file_put_contents($this->frontendRoutesPath . "/admin/" . mb_strtolower($this->varName) . "/" . $this->varName . ".js",
                $template);
        }

//        $file_contents = file_get_contents(resource_path('vue/routes/routes.js'));
//        $strRoute      = 'import ' . $this->varName . ' from "./admin/' . mb_strtolower($this->varName) . '/' . $this->varName . '";
//routes = [...' . $this->varName . ', ...routes];';
//        $strRouteRep   = $strRoute . '
//
//const router';
//
//        $file_contents = str_replace($strRoute, '', $file_contents);
//        $file_contents = preg_replace("/[\r\n]+/", "\n", $file_contents);
//        $file_contents = str_replace("const router", $strRouteRep, $file_contents);
//        file_put_contents(resource_path('vue/routes/routes.js'), $file_contents);
        $import = '';
        $list = '';
        if(file_exists(base_path('ConfigApp.json'))) {
            $configJson = file_get_contents(base_path('ConfigApp.json'));
            $configAll  = json_decode($configJson, true);
            foreach ($configAll as $config){
                $import .= 'import '.$config['model_name'].' from "./admin/'.mb_strtolower($config['model_name']).'/'.$config['model_name'].'";'."\n";
                $list .= "...".$config['model_name'].", ";
            }
        }
        $template = file_get_contents($this->getStub('frontend/js/routes.js'));
        $dataReplace = [
            'import' => $import,
            'list' => $list
        ];
        $template    = $this->replaceStub($template, $dataReplace);

        file_put_contents(resource_path('vue/routes/routes.js'), $template);
    }

    public function makeVueNav()
    {
        $menuContent = '';
        if(file_exists(base_path('ConfigApp.json'))) {
            $configJson = file_get_contents(base_path('ConfigApp.json'));
            $configAll  = json_decode($configJson, true);
            foreach ($configAll as $config){
                $navRoute      = $this->getStub('frontend/component/nav.vue.stub');
                $template    = file_get_contents($navRoute);
                $dataReplace   = [
                    'displayName' => $config['model_name'],
                    'name'        => mb_strtolower($config['model_name']),
                ];
                $template      = $this->replaceStubVue($template, $dataReplace);
                $menuContent .= $template;
            }
        }

        $sb      = $this->getStub('frontend/component/Sidebar.vue.stub');
        $template    = file_get_contents($sb);
        $dataReplace = [
            'menu' => $menuContent
        ];
        $file_contents    = $this->replaceStubVue($template, $dataReplace);
        file_put_contents(resource_path('vue/components/admin/_partials/Sidebar.vue'), $file_contents);
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
            case 'integer':
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
                $htmlType = 'number';
                break;
            case 'integer':
                $htmlType = 'number';
                break;
            case 'string':
                $htmlType = 'text';
                break;
            case 'float':
                $htmlType = 'number';
                break;
            case 'date':
                $htmlType = 'date';
                break;
            case 'boolean':
                $str = "\n" . '<div class="form-group row">
                    <label for="' . $name . '" class="col-sm-2 col-form-label">' . $displayName . '</label>
                    <div class="col-sm-10">
                        <input type="radio" id="male" name="gender" value="1" v-model="object.' . $name . '">
                            <label for="male">Yes</label>
                        <input type="radio" id="female" name="gender" value="0" v-model="object.' . $name . '">
                            <label for="female">No</label>
                    </div>
                </div>';
                break;
            case 'datetime':
                $htmlType = 'datetime-local';
                break;
            case 'text':
                $str = "\n" . '<div class="form-group row">
                    <label for="' . $name . '" class="col-sm-2 col-form-label">' . $displayName . '</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="' . $name . '" name="' . $name . '" rows="3" v-model="object.' . $name . '"></textarea>
                    </div>
                </div>';
                break;
        }

        if ($str) {
            return $str;
        }

        $str = "\n" . '<div class="form-group row">
                    <label for="' . $name . '" class="col-sm-2 col-form-label">' . $displayName . '</label>
                    <div class="col-sm-10">
                        <input type="' . $htmlType . '" class="form-control" id="' . $name . '" name="' . $name . '" v-model="object.' . $name . '">
                    </div>
                </div>';

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
            $filter = "";
            if ($col['type'] == 'date') {
                $filter = " | formatDate ";
            }

            if ($col['type'] == 'datetime') {
                $filter = " | formatDateTime ";
            }

            $str   .= '<td>{{ item.' . $col['name'] . $filter.' }}</td>';
            $first = false;
        }

        return $str;
    }

    public function getVueFilterTable()
    {
        $str   = '';
        foreach ($this->columns as $key => $col) {
            if (isset($col['filter'])) {
                $type = 'text';
                $width = '2';
                if (in_array($col['type'], ['bigint', 'integer', 'float'])) {
                    $type = 'number';
                }

                if (in_array($col['type'], ['date', 'datetime'])) {
                    $type = 'date';
                }

                $str .= str_repeat(' ', 24) . '<div class="col-12 col-sm-' . $width . '">
                            <div class="form-group">
                                <label>' . $col['display'] . '</label>
                                <input type="' . $type . '" class="form-control" v-model="form.' . $col['name'] . '">
                            </div>
                        </div>' . "\n";
            }
        }

        return $str;
    }

    public function getTableColumnJson()
    {
        $column = [];
        foreach ($this->columns as $key => $col) {
            if(isset($col['filter'])) {
                $obj            = new \stdClass();
                $obj->title     = $col['display'];
                $obj->dataIndex = $col['name'];
                $obj->sorter    = true;
                $column[]       = $obj;
            }
        }

        $obj            = new \stdClass();
        $obj->title     = 'Action';
        $obj->key = 'action';
        $obj->width    = '10%';
        $obj->scopedSlots    = new \stdClass();
        $obj->scopedSlots->customRender = 'action';
        $column[]       = $obj;

        return $column;
    }

    public function getFilterJson() {
        $filter = [];
        foreach ($this->columns as $key => $col) {
            if(isset($col['filter'])) {
                $obj = new \stdClass();
                $obj->id = $col['name'];
                $obj->label = $col['display'];
                $obj->type = 'string';

                if (in_array($col['type'], ['bigint', 'integer', 'float'])) {
                    $obj->type = 'integer';
                }

                if (in_array($col['type'], ['float'])) {
                    $obj->type = 'double';
                }

                if (in_array($col['type'], ['date', 'datetime'])) {
                    $obj->type = 'date';
                    $validObj = new \stdClass();
                    $validObj->format = 'YYYY/MM/DD';
                    $obj->validation = $validObj;
                    $obj->plugin = 'datepicker';
                    $pluginConfig = new \stdClass();
                    $pluginConfig->format = 'yyyy/mm/dd';
                    $pluginConfig->todayBtn = 'linked';
                    $pluginConfig->todayHighlight = true;
                    $pluginConfig->autoclose = true;
                    $obj->plugin_config = $pluginConfig;
                }

                if (in_array($col['type'], ['boolean'])) {
                    $obj->type = 'boolean';
                }

                $filter[] = $obj;
            }
        }

        return $filter;
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
