<?php

namespace App\Http\Controllers\Student;

use DB;
use Validator;
use DataTables;
use App\Libs\Study;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\DegProbableList;
use App\Models\HscProbableList;
use App\Models\MscProbableList;
use App\Models\HonsProbableList;
use App\Http\Controllers\Controller;

class ProbableListController extends Controller
{
    public function index(){
        $title = 'Easy CollegeMate - Probable List Management';
        $breadcrumb = 'student.prblist.index:Probable List|Dashboard';

        return view('BackEnd.student.formfillup.probableList.index', compact('title','breadcrumb'));
    }

    public function honours_index(){
        $title = 'Easy CollegeMate - Probable List Management';
        $breadcrumb = 'student.prblist.index:Probable List|Dashboard';
        $path = 'BackEnd.student.formfillup.probableList.honours';

        return view($path, compact('title', 'breadcrumb'));
    }
    public function masters_index(){
        $title = 'Easy CollegeMate - Probable List Management';
        $breadcrumb = 'student.prblist.index:Probable List|Dashboard';
        $path = 'BackEnd.student.formfillup.probableList.masters';
        
        return view($path, compact('title', 'breadcrumb'));
    }
    public function degree_index(){
        $title = 'Easy CollegeMate - Probable List Management';
        $breadcrumb = 'student.prblist.index:Probable List|Dashboard';
        $path = 'BackEnd.student.formfillup.probableList.degree';

        return view($path, compact('title', 'breadcrumb'));
    }

    public function hsc_index(){
        $title = 'Easy CollegeMate - Probable List Management';
        $breadcrumb = 'student.prblist.index:Probable List|Dashboard';
        $path = 'BackEnd.student.formfillup.probableList.hsc';

        return view($path, compact('title', 'breadcrumb'));
    }

    public function upload(){
        $title = 'Easy CollegeMate - Probable List Management';
        $path = '';
        $breadcrumb = '';

        if(request()->type == 'honours'){
            $path = 'BackEnd.student.formfillup.probableList.honours_upload';
            $breadcrumb = 'student.prblist.honours:Honours Probable List|Dashboard';
        }
        if(request()->type == 'masters'){
            $path = 'BackEnd.student.formfillup.probableList.masters_upload';
            $breadcrumb = 'student.prblist.masters:Masters Probable List|Dashboard';
        }
        if(request()->type == 'degree'){
            $path = 'BackEnd.student.formfillup.probableList.degree_upload';
            $breadcrumb = 'student.prblist.degree:Degree Probable List|Dashboard';
        }
        if(request()->type == 'hsc'){
            $path = 'BackEnd.student.formfillup.probableList.hsc_upload';
            $breadcrumb = 'student.prblist.hsc:HSC Probable List|Dashboard';
        }

        return view($path, compact('title', 'breadcrumb'));
    }

    public function create(Request $request){
        if($request->type == 'honours')
            $html =  view('BackEnd.student.formfillup.probableList.particles.hons_form')->render();
        if($request->type == 'masters')
            $html =  view('BackEnd.student.formfillup.probableList.particles.msc_form')->render();
        if($request->type == 'degree')
            $html =  view('BackEnd.student.formfillup.probableList.particles.deg_form')->render();
        if($request->type == 'hsc')
            $html =  view('BackEnd.student.formfillup.probableList.particles.hsc_form')->render();

        return response()->json([
                'status' => 202,
                'modal' => 'modal-lg',
                'html' => $html

            ],Response::HTTP_OK);
    }

    public function upload_exe(Request $request){
        $this->validate($request, [
            'csv' => 'required'
        ]);
        
        if ($request->hasFile('csv'))
        {
            DB::beginTransaction();
            try {
                if($request->type =='honours') $table = 'student_info_hons_formfillup';
                if($request->type =='masters') $table = 'student_info_masters_formfillup';
                if($request->type =='degree') $table = 'student_info_degree_formfillup';
                if($request->type =='hsc') $table = 'student_info_hsc_formfillup';

                $name = $request->file('csv');
                $extension = $name->getClientOriginalExtension();
                
                if(strtolower($extension) == 'csv'){
                    function csv_to_array($filename='', $delimiter=',') {
                        if(!file_exists($filename) || !is_readable($filename)) {
                            return false;
                        }

                        $handle = fopen($filename, 'r');
                        $header = NULL;
                        $data = array();

                        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                            if(!$header) {
                                $header = array_map('trim', $row);
                            } else {
                                $trimmed_row = array_map('trim', $row);
                                $data[] = array_combine($header, $trimmed_row);
                            }
                        }

                        fclose($handle);

                        return $data;
                    }

                    $name = $request->file('csv');

                    $extension = $name->getClientOriginalExtension();

                    if(strtolower($extension) == 'csv'){
                        $csvFile = $request->file('csv');
                        
                        $areas = csv_to_array($csvFile);
                        
                        $chunkSize = 1000;
                        
                        foreach (array_chunk($areas, $chunkSize) as $chunk) {
                            DB::table($table)->insert($chunk);
                        }
                    }

                    DB::commit();

                    $message = 'You have successfully uploaded';
                    return redirect()->back()->with('success',$message);
                }
                
                $message = 'Format Not Match';
                    return redirect()->back()
                    ->with('error',$message);
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollback();
                $message = $e->errorInfo[2];
                    return redirect()->back()
                    ->with('error',$message);
            }
        }
    }

    public function destroy(Request $request){

        try {
            $deleted = false;
            if($request->type == 'honours'){
                $list = HonsProbableList::where('auto_id', $request->id)->delete();
                $deleted = true;
            }elseif($request->type == 'degree'){
                $list = DegProbableList::where('auto_id', $request->id)->delete();
                $deleted = true;
            }elseif($request->type == 'masters'){
                $list = MscProbableList::where('auto_id', $request->id)->delete();
                $deleted = true;
            }elseif($request->type == 'hsc'){
                $list = HscProbableList::where('auto_id', $request->id)->delete();
                $deleted = true;
            }

            if($deleted){
                $message = 'Probable Student Deleted Successfully';
            }else{
                $message = 'Something Went Wrong';
            }

            return response()->json([
                'status' => 'warning',
                'message' => $message,
                'id' => $request->id,
                'table' => 'datatable'
            ],Response::HTTP_OK);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST);
        }
    }


    public function honours_datasource(Request $request){
        $lists = HonsProbableList::query();

        return Datatables::of($lists)
                ->addColumn('actions', function ($lists) {
                    $html = " <a href=".route('student.prblist.honours.edit', $lists->auto_id)." class='btn btn-primary type-b duplicate_data' data-id=".$lists->auto_id."  data-label='Honours Probable Student'><i class='fas fa-copy'></i></a>";
                    $html .= " <a href=".route('student.prblist.honours.edit', $lists->auto_id)." class='btn btn-primary type-b edit_data' data-id=".$lists->auto_id." data-label='Honours Probable Student'><i class='fas fa-pencil'></i></a>";
                    $html .= " <a href=".route('student.prblist.destroy', ['type'=> 'honours' ,'id'=> $lists->auto_id])." class='btn btn-danger type-b delete_data' data-id=".$lists->auto_id."><i class='fas fa-trash'></i></a>";
                    return $html;
                })->filter(function ($query) use ($request) {

                    if ($request->has('student_id') && ! is_null($request->get('student_id'))) {
                        $query->where('id', $request->get('student_id'));
                    }

                    if ($request->has('faculty_name') && ! is_null($request->get('faculty_name'))) {
                        $query->where('faculty_name', $request->get('faculty_name'));
                    }

                    if ($request->has('dept_name') && ! is_null($request->get('dept_name'))) {
                        $query->where('dept_name', $request->get('dept_name'));
                    }

                    if ($request->has('current_level') && ! is_null($request->get('current_level'))) {
                        $query->where('current_level', $request->get('current_level'));
                    }

                    if ($request->has('session') && ! is_null($request->get('session'))) {
                        $query->where('session', $request->get('session'));
                    }

                    if ($request->has('registration_type') && ! is_null($request->get('registration_type'))) {
                        $query->where('registration_type', $request->get('registration_type'));
                    }
                })
                
                ->setRowAttr([
                    'data-row-id' => function($lists) {
                        return $lists->auto_id;
                    },
                    'class'=> function($lists) {
                        return 'text-center ' . Study::updatedRow('id', $lists->auto_id);
                    }
                ])
                ->rawColumns(['actions', 'operations'])
                // ->escapeColumns([])
                ->make(true);
    }

    public function masters_datasource(Request $request){
        $lists = MscProbableList::query();

        return Datatables::of($lists)
                ->addColumn('actions', function ($lists) {
                    $html = " <a href=".route('student.prblist.masters.edit', $lists->auto_id)." class='btn btn-primary type-b duplicate_data' data-id=".$lists->auto_id."  data-label='Masters Probable Student'><i class='fas fa-copy'></i></a>";
                    $html .= " <a href=".route('student.prblist.masters.edit', $lists->auto_id)." class='btn btn-primary type-b edit_data' data-id=".$lists->auto_id." data-label='Masters Probable Student'><i class='fas fa-pencil'></i></a>";
                    $html .= " <a href=".route('student.prblist.destroy', ['type'=> 'masters' ,'id'=> $lists->auto_id])." class='btn btn-danger type-b delete_data' data-id=".$lists->auto_id."><i class='fas fa-trash'></i></a>";
                    return $html;
                })->filter(function ($query) use ($request) {

                    if ($request->has('student_id') && ! is_null($request->get('student_id'))) {
                        $query->where('id', $request->get('student_id'));
                    }

                    if ($request->has('faculty_name') && ! is_null($request->get('faculty_name'))) {
                        $query->where('faculty_name', $request->get('faculty_name'));
                    }

                    if ($request->has('dept_name') && ! is_null($request->get('dept_name'))) {
                        $query->where('dept_name', $request->get('dept_name'));
                    }

                    if ($request->has('current_level') && ! is_null($request->get('current_level'))) {
                        $query->where('current_level', $request->get('current_level'));
                    }

                    if ($request->has('session') && ! is_null($request->get('session'))) {
                        $query->where('session', $request->get('session'));
                    }

                    if ($request->has('registration_type') && ! is_null($request->get('registration_type'))) {
                        $query->where('registration_type', $request->get('registration_type'));
                    }
                })
                
                ->setRowAttr([
                    'data-row-id' => function($lists) {
                        return $lists->auto_id;
                    },
                    'class'=> function($lists) {
                        return 'text-center ' . Study::updatedRow('id', $lists->auto_id);
                    }
                ])
                ->rawColumns(['actions', 'operations'])
                // ->escapeColumns([])
                ->make(true);
    }

    public function degree_datasource(Request $request){
        $lists = DegProbableList::query();

        return Datatables::of($lists)
                ->addColumn('actions', function ($lists) {
                    $html = " <a href=".route('student.prblist.degree.edit', $lists->auto_id)." class='btn btn-primary type-b duplicate_data' data-id=".$lists->auto_id."  data-label='Degree Probable Student'><i class='fas fa-copy'></i></a>";
                    $html .= " <a href=".route('student.prblist.degree.edit', $lists->auto_id)." class='btn btn-primary type-b edit_data' data-id=".$lists->auto_id." data-label='Degree Probable Student'><i class='fas fa-pencil'></i></a>";
                    $html .= " <a href=".route('student.prblist.destroy', ['type'=> 'degree' ,'id'=> $lists->auto_id])." class='btn btn-danger type-b delete_data' data-id=".$lists->auto_id."><i class='fas fa-trash'></i></a>";
                    return $html;
                })->filter(function ($query) use ($request) {

                    if ($request->has('student_id') && ! is_null($request->get('student_id'))) {
                        $query->where('id', $request->get('student_id'));
                    }

                    if ($request->has('faculty_name') && ! is_null($request->get('faculty_name'))) {
                        $query->where('faculty_name', $request->get('faculty_name'));
                    }

                    if ($request->has('dept_name') && ! is_null($request->get('dept_name'))) {
                        $query->where('dept_name', $request->get('dept_name'));
                    }

                    if ($request->has('current_level') && ! is_null($request->get('current_level'))) {
                        $query->where('current_level', $request->get('current_level'));
                    }

                    if ($request->has('session') && ! is_null($request->get('session'))) {
                        $query->where('session', $request->get('session'));
                    }

                    if ($request->has('registration_type') && ! is_null($request->get('registration_type'))) {
                        $query->where('registration_type', $request->get('registration_type'));
                    }
                })
                
                ->setRowAttr([
                    'data-row-id' => function($lists) {
                        return $lists->auto_id;
                    },
                    'class'=> function($lists) {
                        return 'text-center ' . Study::updatedRow('id', $lists->auto_id);
                    }
                ])
                ->rawColumns(['actions', 'operations'])
                // ->escapeColumns([])
                ->make(true);
    }

    public function hsc_datasource(Request $request){
        $lists = HscProbableList::query();

        return Datatables::of($lists)
                ->addColumn('actions', function ($lists) {
                    $html = " <a href=".route('student.prblist.hsc.edit', $lists->auto_id)." class='btn btn-primary type-b duplicate_data' data-id=".$lists->auto_id."  data-label='HSC Probable Student'><i class='fas fa-copy'></i></a>";
                    $html .= " <a href=".route('student.prblist.hsc.edit', $lists->auto_id)." class='btn btn-primary type-b edit_data' data-id=".$lists->auto_id." data-label='HSC Probable Student'><i class='fas fa-pencil'></i></a>";
                    $html .= " <a href=".route('student.prblist.destroy', ['type'=> 'hsc' ,'id'=> $lists->auto_id])." class='btn btn-danger type-b delete_data' data-id=".$lists->auto_id."><i class='fas fa-trash'></i></a>";
                    return $html;
                })->filter(function ($query) use ($request) {

                    if ($request->has('student_id') && ! is_null($request->get('student_id'))) {
                        $query->where('id',"LIKE",'%' . $request->get('student_id') . '%')->orWhere('class_roll',"LIKE",'%' . $request->get('student_id') . '%');
                    }

                    if ($request->has('groups') && ! is_null($request->get('groups'))) {
                        $query->where('groups', $request->get('groups'));
                    }

                    if ($request->has('current_level') && ! is_null($request->get('current_level'))) {
                        $query->where('current_level', $request->get('current_level'));
                    }

                    if ($request->has('session') && ! is_null($request->get('session'))) {
                        $query->where('session', $request->get('session'));
                    }

                    if ($request->has('registration_type') && ! is_null($request->get('registration_type'))) {
                        $query->where('registration_type', $request->get('registration_type'));
                    }
                })
                
                ->setRowAttr([
                    'data-row-id' => function($lists) {
                        return $lists->auto_id;
                    },
                    'class'=> function($lists) {
                        return 'text-center ' . Study::updatedRow('id', $lists->auto_id);
                    }
                ])
                ->rawColumns(['actions', 'operations'])
                // ->escapeColumns([])
                ->make(true);
    }

    public function honours_edit($auto_id){
        $list = HonsProbableList::where('auto_id', $auto_id)->firstOrFail();

        $data = [
            'data' => $list,
            'id' => $list->auto_id,
            'student_id' => $list->id,
            'name'=> $list->name,
            'session' => $list->session,
            'current_level' => $list->current_level,
            'faculty_name' => $list->faculty_name,
            'dept_name' => $list->dept_name,
            'student_type'=> $list->student_type,
            'registration_type' => $list->registration_type,
            'papers' => $list->papers,
            'status' => $list->status
        ];

        $html = view('BackEnd.student.formfillup.probableList.particles.hons_form', $data)->render();
        return response()->json([
                'status' => 200,
                'modal' => 'modal-lg',
                'html' => $html

            ],Response::HTTP_OK);
    }

    public function masters_edit($auto_id){
        $list = MscProbableList::where('auto_id', $auto_id)->firstOrFail();

        $data = [
            'data' => $list,
            'id' => $list->auto_id,
            'student_id' => $list->id,
            'name'=> $list->name,
            'session' => $list->session,
            'current_level' => $list->current_level,
            'faculty_name' => $list->faculty_name,
            'dept_name' => $list->dept_name,
            'student_type'=> $list->student_type,
            'registration_type' => $list->registration_type,
            'papers' => $list->papers,
            'status' => $list->status
        ];

        $html = view('BackEnd.student.formfillup.probableList.particles.msc_form', $data)->render();
        return response()->json([
                'status' => 200,
                'modal' => 'modal-lg',
                'html' => $html

            ],Response::HTTP_OK);
    }

    public function degree_edit($auto_id){
        $list = DegProbableList::where('auto_id', $auto_id)->firstOrFail();

        $data = [
            'data' => $list,
            'id' => $list->auto_id,
            'student_id' => $list->id,
            'name'=> $list->name,
            'session' => $list->session,
            'current_level' => $list->current_level,
            'faculty_name' => $list->faculty_name,
            'groups' => $list->dept_name,
            'student_type'=> $list->student_type,
            'registration_type' => $list->registration_type,
            'papers' => $list->papers,
            'status' => $list->status
        ];

        $html = view('BackEnd.student.formfillup.probableList.particles.deg_form', $data)->render();
        return response()->json([
                'status' => 200,
                'modal' => 'modal-lg',
                'html' => $html

            ],Response::HTTP_OK);
    }

    public function hsc_edit($auto_id){
        $list = HscProbableList::where('auto_id', $auto_id)->firstOrFail();

        $data = [
            'data' => $list,
            'id' => $list->auto_id,
            'student_id' => $list->id,
            'class_roll' => $list->class_roll,
            'name'=> $list->name,
            'total_amount'=> $list->total_amount,
            'session' => $list->session,
            'current_level' => $list->current_level,
            'faculty_name' => $list->faculty_name,
            'groups' => $list->groups,
            'student_type'=> $list->student_type,
            'registration_type' => $list->registration_type,
            'papers' => $list->papers,
            'status' => $list->status,
            'promotion_status' => $list->promotion_status
        ];

        $html = view('BackEnd.student.formfillup.probableList.particles.hsc_form', $data)->render();
        return response()->json([
                'status' => 200,
                'modal' => 'modal-lg',
                'html' => $html

            ],Response::HTTP_OK);
    }

    public function honours_store(Request $request){
        DB::beginTransaction();

        try {

            $auto_id = $request->id;

            $action_type = $request->action_type;

            if($action_type == 'update'){
                $list = HonsProbableList::where('auto_id', $auto_id)->firstOrFail();
                $msg = 'Probable Student Updated Successfully';
                $status = 'info';
            }else{
                $list = new HonsProbableList;
                $msg = 'Probable Student Created Successfully';
                $status = 'success';
            }

            $data = $request->all();

            $rules[] = HonsProbableList::validateRules($data);


            $this->validate($request, Arr::collapse($rules));

            $list->id = $request->student_id;
            $list->name = $request->name;
            $list->session = $request->session;
            $list->current_level = $request->current_level;
            $list->faculty_name = $request->faculty_name;
            $list->dept_name = $request->dept_name;
            $list->student_type = $request->student_type;
            $list->registration_type = $request->registration_type;
            $list->papers = $request->papers;
            $list->status = $request->status;
            $list->total_amount = $request->total_amount;
            $list->save();

            $list_id = $list->id;

            if($action_type == 'update'){
                $list_id = $list->auto_id;
            }
            
            $objects = HonsProbableList::where('auto_id',$list_id)->get(['auto_id','id', 'name', 'session', 'dept_name', 'student_type','registration_type'])->first()->toArray();
            
            $row_values = array_values($objects);
            
            DB::commit();
            return response()->json([
                'status' => $status,
                'message' => $msg,
                'table' => 'datatable',
                'id' => $list->auto_id,
                'row_values' => $row_values

            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST); // 400
        }  
    }

    public function masters_store(Request $request){
        DB::beginTransaction();

        try {

            $auto_id = $request->id;

            $action_type = $request->action_type;

            if($action_type == 'update'){
                $list = MscProbableList::where('auto_id', $auto_id)->firstOrFail();
                $msg = 'Probable Student Updated Successfully';
                $status = 'info';
            }else{
                $list = new MscProbableList;
                $msg = 'Probable Student Created Successfully';
                $status = 'success';
            }

            $data = $request->all();

            $rules[] = MscProbableList::validateRules($data);


            $this->validate($request, Arr::collapse($rules));

            $list->id = $request->student_id;
            $list->name = $request->name;
            $list->session = $request->session;
            $list->current_level = $request->current_level;
            $list->faculty_name = $request->faculty_name;
            $list->dept_name = $request->dept_name;
            $list->student_type = $request->student_type;
            $list->registration_type = $request->registration_type;
            $list->papers = $request->papers;
            $list->status = $request->status;
            $list->total_amount = $request->total_amount;
            $list->save();

            $list_id = $list->id;

            if($action_type == 'update'){
                $list_id = $list->auto_id;
            }
            
            DB::commit();
            return response()->json([
                'status' => $status,
                'message' => $msg,
                'table' => 'datatable',
                'id' => $list->auto_id

            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST); // 400
        }  
    }

    public function degree_store(Request $request){
        DB::beginTransaction();

        try {

            $auto_id = $request->id;

            $action_type = $request->action_type;

            if($action_type == 'update'){
                $list = DegProbableList::where('auto_id', $auto_id)->firstOrFail();
                $msg = 'Probable Student Updated Successfully';
                $status = 'info';
            }else{
                $list = new DegProbableList;
                $msg = 'Probable Student Created Successfully';
                $status = 'success';
            }

            $data = $request->all();

            $rules[] = DegProbableList::validateRules($data);


            $this->validate($request, Arr::collapse($rules));

            $list->id = $request->student_id;
            $list->name = $request->name;
            $list->session = $request->session;
            $list->current_level = $request->current_level;
            $list->faculty_name = $request->groups;
            $list->dept_name = $request->groups;
            $list->student_type = $request->student_type;
            $list->registration_type = $request->registration_type;
            $list->papers = $request->papers;
            $list->status = $request->status;
            $list->total_amount = $request->total_amount;
            $list->save();

            $list_id = $list->id;

            if($action_type == 'update'){
                $list_id = $list->auto_id;
            }
            
            DB::commit();
            return response()->json([
                'status' => $status,
                'message' => $msg,
                'table' => 'datatable',
                'id' => $list->auto_id

            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST); // 400
        }  
    }

    public function hsc_store(Request $request){
        DB::beginTransaction();

        try {

            $auto_id = $request->id;

            $action_type = $request->action_type;

            if($action_type == 'update'){
                $list = HscProbableList::where('auto_id', $auto_id)->firstOrFail();
                $msg = 'Probable Student Updated Successfully';
                $status = 'info';
            }else{
                $list = new HscProbableList;
                $msg = 'Probable Student Created Successfully';
                $status = 'success';
            }

            $data = $request->all();

            $rules[] = HscProbableList::validateRules($data);

            $this->validate($request, Arr::collapse($rules));

            $list->id = $request->student_id;
            $list->class_roll = $request->class_roll;
            $list->name = $request->name;
            $list->session = $request->session;
            $list->current_level = $request->current_level;
            $list->groups = $request->groups;
            $list->student_type = $request->student_type;
            $list->registration_type = $request->registration_type;
            $list->papers = $request->papers;
            $list->status = $request->status;
            $list->promotion_status = $request->promotion_status;
            $list->total_amount = $request->total_amount;
            $list->save();

            $list_id = $list->id;

            if($action_type == 'update'){
                $list_id = $list->auto_id;
            }
            
            DB::commit();
            return response()->json([
                'status' => $status,
                'message' => $msg,
                'table' => 'datatable',
                'id' => $list->auto_id

            ],Response::HTTP_OK);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST); // 400
        }
    }
}
