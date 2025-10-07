<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\FormFillupConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Response;
use DataTables;
use DB;

class AdminFormfillupController extends Controller
{
    public function index()
	{
		$title = 'Easy CollegeMate - FormFillup';
		$breadcrumb = 'admin.formfillup.index:FormFillup Management|Dashboard';
		$formfillup = FormFillupConfig::paginate(Study::paginate());
		return view('BackEnd.admin.formfillup.index')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withFormfillup($formfillup);
					
	}

	public function formfillup_config(){
        $title = 'Easy CollegeMate - FormFillup Management';
        $breadcrumb = 'admin.formfillup.index:Formfillup Management|Dashboard';

        return view('BackEnd.admin.formfillup.config_index')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb);
    }

    public function formfillup_config_datasource(Request $request){
        $configs = FormFillupConfig::query();

        return Datatables::of($configs)
                ->addColumn('actions', function ($config) {
                    $html = "<a href=".route('admin.formfillup.config.edit', $config->id)." class='btn btn-primary type-b duplicate_data'><i class='fa fa-copy'></i></a>";
                    $html .= " <a href=".route('admin.formfillup.config.edit', $config->id)." class='btn btn-primary type-b edit_data' data-id=".$config->id."><i class='fa fa-pencil'></i></a>";
                    $html .= " <a href=".route('admin.formfillup.config.destroy', $config->id)." class='btn btn-danger type-b delete_data' data-id=".$config->id."><i class='fa fa-trash'></i></a>";
                    return $html;
                })
                ->filter(function ($query) use ($request) {

                    if ($request->has('current_level') && ! is_null($request->get('current_level'))) {
                        $query->where('current_level', $request->get('current_level'));
                    }

                    if ($request->has('open') && ! is_null($request->get('open'))) {
                        $query->where('open', $request->get('open'));
                    }

                    if ($request->has('course') && ! is_null($request->get('course')) ) {
                        $query->where('course', $request->get('course'));
                    }
                })
                ->editColumn('open', function ($config) {
                    return get_badge_status('open', $config->open);
                })

                ->setRowAttr([
                    'data-row-id' => function($config) {
                        return $config->id;
                    },
                    'class'=> function($config) {
                        return 'text-center ' . Study::updatedRow('id', $config->id);
                    }
                ])
                 // ->orderColumn('id', true)
                ->rawColumns(['open','actions'])
                // ->escapeColumns()
                ->make(true);
                // ->toJson();
    }

    public function formfillup_config_edit($id){
        $config = FormFillupConfig::find($id);
        $data = [
            'current_level' => $config->current_level,
            'session' => $config->session,
            'exam_year' => $config->exam_year,
            'opening_date' => $config->opening_date,
            'clossing_date' => $config->clossing_date,
            'course' => $config->course,
            'open' => $config->open
        ];

        $html = view('BackEnd.admin.formfillup.particles.form', $data)->render();
        return response()->json([
                'status' => 200,
                'html' => $html

            ],Response::HTTP_OK);

    }

    public function formfillup_config_store(Request $request){
        $this->validate($request, [
            'current_level' => 'required',
            'session' => 'required',
            'open' => 'required',
            'exam_year' => 'required',
            'opening_date' => 'required',
            'clossing_date' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $id = $request->id;

            $action_type = $request->action_type;

            if($action_type == 'update'){
                $config = FormFillupConfig::find($id);
                $msg = 'You have successfully updated the admission Settings';
                $status = 'info';
            }else{
                $config = new FormFillupConfig;
                $msg = 'You have successfully created the admission Settings';
                $status = 'success';
            }

            $config->current_level= $request->get('current_level');
            $config->session= $request->get('session');
            $config->course= $request->get('course');
            $config->open= $request->get('open');
            $config->exam_year= $request->get('exam_year');
            $config->opening_date= $request->get('opening_date');
            $config->clossing_date= $request->get('clossing_date');
            $config->save();

            DB::commit();

            $array = FormFillupConfig::where('id',$config->id)->get(['id', 'current_level', 'session', 'open', 'exam_year','opening_date', 'clossing_date'])->first()->toArray();
            foreach ($array as $key => $val) {
                $value = $val;
                if($key == 'open'){
                    $value = get_badge_status('open', $val);
                }

                $values[$key] = $value;
            }

            $row_values = array_values($values);

            return response()->json([
                'status' => $status,
                'message' => $msg,
                'id' => $config->id,
                'table' => 'datatable',
                'row_values' => $row_values

            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST); // 400
        }
    }

    public function formfillup_config_destroy(Request $request, $id){
        try {
            FormFillupConfig::find($id)->delete();
            return response()->json([
                'status' => 'warning',
                'message' => 'Config Deleted Successfully',
                'id' => $id,
                'table' => 'datatable',

            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST);
        }
    }

}
