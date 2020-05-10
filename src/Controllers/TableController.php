<?php

namespace Ices\Tool\Controllers;

use Ices\Tool\Service\ConfigService;
use Ices\Tool\Service\GenerateService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TableController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $generateService;

    protected $configService;

    public function __construct(GenerateService $generateService, ConfigService $configService)
    {
        $this->generateService = $generateService;
        $this->configService = $configService;
    }

    public function index(Request $request)
    {
        $table = $request->get('table');
        $assign['cols'] = [];
        $assign['tables'] = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        if (!$table) {
            return view('CRUD::tables.load', $assign);
        }

        if ($table) {
            if(file_exists(base_path('ConfigApp.json'))) {
                $configJson = file_get_contents(base_path('ConfigApp.json'));
                $configAll  = json_decode($configJson, true);

                if (isset($configAll[$table])) {
                    $request->request->set('config', $configAll[$table]);
                }
            }


            $cols = DB::getSchemaBuilder()->getColumnListing($table);
            foreach ($cols as $col) {
                $assign['cols'][$col] = [
                    'name' => $col,
                    'type' => DB::connection()->getDoctrineColumn($table, $col)->getType()->getName(),
                ];
            }
        }

        return view('CRUD::tables.index', $assign);
    }

    public function generate(Request $request)
    {
        $data  = $request->all();
        $table = $request->get('table');

        if (array_key_exists('save', $data['action'])) {
            $tableConfig = $data[$table];
            $this->configService->save($table, $tableConfig);

            return redirect()->back()->with('success', 'Save success');
        }

        if (array_key_exists('generate', $data['action'])) {
            $tableConfig = $data[$table];
            $this->configService->save($table, $tableConfig);

            $tableConfig = $data[$table];
            $modelName   = $tableConfig['model_name'];
            $columns     = $tableConfig['cols'];

            if (isset($tableConfig['hasAuth'])) {
                $this->generateService->makeFrontendAuth();
            }

            $this->generateService->generate($modelName, $columns, $table);

            return redirect()->back()->with('success', 'Generate success');
        }
    }

    public function makeAuth(){
        $this->generateService->makeFrontendAuth(true);
    }
}
