<?php

namespace Ices\Tool\Controllers;

use Ices\Tool\Service\GenerateService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $generateService;

    public function __construct(GenerateService $generateService)
    {
        $this->generateService = $generateService;
    }

    public function index(Request $request)
    {
        $table = $request->get('table');

        if (!$table) {
            return view('CRUD::tables.load');
        }

        $assign['cols'] = [];

        if ($table) {
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
        $modelName = $request->get('model_name');
        $table = $request->get('table');
        $columns = $request->get('cols');
        if ($request->get('hasAuth')) {
            $this->generateService->generateAuth();
        }

        $this->generateService->generate($modelName, $columns, $table);

        return redirect()->back()->with('success', 'Generate success');
    }
}
