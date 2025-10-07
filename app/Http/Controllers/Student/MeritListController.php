<?php

namespace App\Http\Controllers\Student;

use DB;
use Validator;
use DataTables;
use App\Libs\Study;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\DegreeMeritList;
use App\Models\HscMeritList;
use App\Models\MastersMeritList;
use App\Models\HonsMeritList;
use App\Http\Controllers\Controller;

class MeritListController extends Controller
{
    public function index()
    {
        $title = 'Easy CollegeMate - Merit List Management';
        $breadcrumb = 'student.meritlist.index:Merit List|Dashboard';

        return view('BackEnd.student.admission.meritList.index', compact('title', 'breadcrumb'));
    }

    public function honours_index()
    {
        if (!auth()->user()->can('merit.list.honours')) {
            abort(403);
        }
        $title = 'Easy CollegeMate - Merit List Management';
        $breadcrumb = 'student.meritlist.index:Merit List|Dashboard';
        $path = 'BackEnd.student.admission.meritList.honours';

        return view($path, compact('title', 'breadcrumb'));
    }
    public function masters_index()
    {
        if (!auth()->user()->can('merit.list.masters')) {
            abort(403);
        }
        $title = 'Easy CollegeMate - Merit List Management';
        $breadcrumb = 'student.meritlist.index:Merit List|Dashboard';
        $path = 'BackEnd.student.admission.meritList.masters';

        return view($path, compact('title', 'breadcrumb'));
    }
    public function degree_index()
    {
        if (!auth()->user()->can('merit.list.degree')) {
            abort(403);
        }
        $title = 'Easy CollegeMate - Merit List Management';
        $breadcrumb = 'student.meritlist.index:Merit List|Dashboard';
        $path = 'BackEnd.student.admission.meritList.degree';

        return view($path, compact('title', 'breadcrumb'));
    }

    public function hsc_index()
    {
        if (!auth()->user()->can('merit.list.hsc')) {
            abort(403);
        }
        $title = 'Easy CollegeMate - Merit List Management';
        $breadcrumb = 'student.meritlist.index:Merit List|Dashboard';
        $path = 'BackEnd.student.admission.meritList.hsc';

        return view($path, compact('title', 'breadcrumb'));
    }

    public function upload()
    {
        $title = 'Easy CollegeMate - Merit List Management';
        $path = '';
        $breadcrumb = '';

        if (request()->type == 'honours') {
            $path = 'BackEnd.student.admission.meritList.honours_upload';
            $breadcrumb = 'student.meritlist.honours:Honours Merit List|Dashboard';
        }
        if (request()->type == 'masters') {
            $path = 'BackEnd.student.admission.meritList.masters_upload';
            $breadcrumb = 'student.meritlist.masters:Masters Merit List|Dashboard';
        }
        if (request()->type == 'degree') {
            $path = 'BackEnd.student.admission.meritList.degree_upload';
            $breadcrumb = 'student.meritlist.degree:Degree Merit List|Dashboard';
        }
        if (request()->type == 'hsc') {
            $path = 'BackEnd.student.admission.meritList.hsc_upload';
            $breadcrumb = 'student.meritlist.hsc:HSC Merit List|Dashboard';
        }

        return view($path, compact('title', 'breadcrumb'));
    }

    public function create(Request $request)
    {
        if ($request->type == 'honours')
            $html =  view('BackEnd.student.admission.meritList.particles.hons_form')->render();
        if ($request->type == 'masters')
            $html =  view('BackEnd.student.admission.meritList.particles.msc_form')->render();
        if ($request->type == 'degree')
            $html =  view('BackEnd.student.admission.meritList.particles.deg_form')->render();
        if ($request->type == 'hsc')
            $html =  view('BackEnd.student.admission.meritList.particles.hsc_form')->render();

        return response()->json([
            'status' => 202,
            'modal' => 'modal-lg',
            'html' => $html

        ], Response::HTTP_OK);
    }

    public function upload_exe(Request $request)
    {
        $this->validate($request, [
            'csv' => 'required'
        ]);

        if ($request->hasFile('csv')) {
            DB::beginTransaction();
            try {
                if ($request->type == 'honours') $table = 'hons_merit_list';
                if ($request->type == 'masters') $table = 'masters_merit_list';
                if ($request->type == 'degree') $table = 'deg_merit_list';
                if ($request->type == 'hsc') $table = 'hsc_merit_list';

                $name = $request->file('csv');
                $extension = $name->getClientOriginalExtension();

                if (strtolower($extension) == 'csv') {
                    function csv_to_array($filename = '', $delimiter = ',')
                    {
                        if (!file_exists($filename) || !is_readable($filename))
                            return FALSE;

                        $header = NULL;
                        $data = array();
                        if (($handle = fopen($filename, 'r')) !== FALSE) {
                            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                                if (!$header)
                                    $header = $row;
                                else
                                    $data[] = array_combine($header, $row);
                            }
                            fclose($handle);
                        }
                        return $data;
                    }

                    $csvFile = $request->file('csv');

                    $areas = csv_to_array($csvFile);


                    DB::table($table)->insert($areas);

                    DB::commit();

                    $message = 'You have successfully uploaded';
                    return redirect()->back()->with('success', $message);
                }

                $message = 'Format Not Match';
                return redirect()->back()
                    ->with('error', $message);
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollback();
                $message = $e->errorInfo[2];
                return redirect()->back()
                    ->with('error', $message);
            }
        }
    }

    public function destroy(Request $request)
    {

        try {
            $deleted = false;
            if ($request->type == 'honours') {
                $list = HonsMeritList::where('id', $request->id)->delete();
                $deleted = true;
            } elseif ($request->type == 'degree') {
                $list = DegreeMeritList::where('id', $request->id)->delete();
                $deleted = true;
            } elseif ($request->type == 'masters') {
                $list = MastersMeritList::where('id', $request->id)->delete();
                $deleted = true;
            } elseif ($request->type == 'hsc') {
                $list = HscMeritList::where('id', $request->id)->delete();
                $deleted = true;
            }

            if ($deleted) {
                $message = 'Merit Student Deleted Successfully';
            } else {
                $message = 'Something Went Wrong';
            }

            return response()->json([
                'status' => 'warning',
                'message' => $message,
                'id' => $request->id,
                'table' => 'datatable'
            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ], Response::HTTP_BAD_REQUEST);
        }
    }


    public function honours_datasource(Request $request)
    {
        $lists = HonsMeritList::query();

        return Datatables::of($lists)
            ->addColumn('actions', function ($lists) {
                $html = " <a href=" . route('student.meritlist.honours.edit', $lists->id) . " class='btn btn-primary type-b duplicate_data' data-id=" . $lists->id . "  data-label='Honours Merit Student'><i class='fas fa-copy'></i></a>";
                $html .= " <a href=" . route('student.meritlist.honours.edit', $lists->id) . " class='btn btn-primary type-b edit_data' data-id=" . $lists->id . " data-label='Honours Merit Student'><i class='fas fa-pencil'></i></a>";
                $html .= " <a href=" . route('student.meritlist.destroy', ['type' => 'honours', 'id' => $lists->id]) . " class='btn btn-danger type-b delete_data' data-id=" . $lists->id . "><i class='fas fa-trash'></i></a>";
                return $html;
            })->filter(function ($query) use ($request) {

                if ($request->has('admission_roll') && ! is_null($request->get('admission_roll'))) {
                    $query->where('admission_roll', $request->get('admission_roll'));
                }

                if ($request->has('faculty') && ! is_null($request->get('faculty'))) {
                    $query->where('faculty', $request->get('faculty'));
                }

                if ($request->has('subject') && ! is_null($request->get('subject'))) {
                    $query->where('subject', $request->get('subject'));
                }

                if ($request->has('admission_status') && ! is_null($request->get('admission_status'))) {
                    $query->where('admission_status', $request->get('admission_status'));
                }

                if ($request->has('session') && ! is_null($request->get('session'))) {
                    $query->where('session', $request->get('session'));
                }
            })

            ->setRowAttr([
                'data-row-id' => function ($lists) {
                    return $lists->id;
                },
                'class' => function ($lists) {
                    return 'text-center ' . Study::updatedRow('id', $lists->id);
                }
            ])
            ->rawColumns(['actions', 'operations'])
            // ->escapeColumns([])
            ->make(true);
    }

    public function masters_datasource(Request $request)
    {
        $lists = MastersMeritList::query();

        return Datatables::of($lists)
            ->addColumn('actions', function ($lists) {
                $html = " <a href=" . route('student.meritlist.masters.edit', $lists->id) . " class='btn btn-primary type-b duplicate_data' data-id=" . $lists->id . "  data-label='Masters Merit Student'><i class='fas fa-copy'></i></a>";
                $html .= " <a href=" . route('student.meritlist.masters.edit', $lists->id) . " class='btn btn-primary type-b edit_data' data-id=" . $lists->id . " data-label='Masters Merit Student'><i class='fas fa-pencil'></i></a>";
                $html .= " <a href=" . route('student.meritlist.destroy', ['type' => 'masters', 'id' => $lists->id]) . " class='btn btn-danger type-b delete_data' data-id=" . $lists->id . "><i class='fas fa-trash'></i></a>";
                return $html;
            })->filter(function ($query) use ($request) {

                if ($request->has('admission_roll') && ! is_null($request->get('admission_roll'))) {
                    $query->where('admission_roll', $request->get('admission_roll'));
                }

                if ($request->has('faculty') && ! is_null($request->get('faculty'))) {
                    $query->where('faculty', $request->get('faculty'));
                }

                if ($request->has('subject') && ! is_null($request->get('subject'))) {
                    $query->where('subject', $request->get('subject'));
                }

                if ($request->has('admission_status') && ! is_null($request->get('admission_status'))) {
                    $query->where('admission_status', $request->get('admission_status'));
                }

                if ($request->has('current_level') && ! is_null($request->get('current_level'))) {
                    $query->where('current_level', $request->get('current_level'));
                }

                if ($request->has('session') && ! is_null($request->get('session'))) {
                    $query->where('session', $request->get('session'));
                }
            })

            ->setRowAttr([
                'data-row-id' => function ($lists) {
                    return $lists->id;
                },
                'class' => function ($lists) {
                    return 'text-center ' . Study::updatedRow('id', $lists->id);
                }
            ])
            ->rawColumns(['actions', 'operations'])
            // ->escapeColumns([])
            ->make(true);
    }

    public function degree_datasource(Request $request)
    {
        $lists = DegreeMeritList::query();

        return Datatables::of($lists)
            ->addColumn('actions', function ($lists) {
                $html = " <a href=" . route('student.meritlist.degree.edit', $lists->id) . " class='btn btn-primary type-b duplicate_data' data-id=" . $lists->id . "  data-label='Degree Merit Student'><i class='fas fa-copy'></i></a>";
                $html .= " <a href=" . route('student.meritlist.degree.edit', $lists->id) . " class='btn btn-primary type-b edit_data' data-id=" . $lists->id . " data-label='Degree Merit Student'><i class='fas fa-pencil'></i></a>";
                $html .= " <a href=" . route('student.meritlist.destroy', ['type' => 'degree', 'id' => $lists->id]) . " class='btn btn-danger type-b delete_data' data-id=" . $lists->id . "><i class='fas fa-trash'></i></a>";
                return $html;
            })->filter(function ($query) use ($request) {

                if ($request->has('admission_roll') && ! is_null($request->get('admission_roll'))) {
                    $query->where('admission_roll', $request->get('admission_roll'));
                }

                if ($request->has('groups') && ! is_null($request->get('groups'))) {
                    $query->where('groups', $request->get('groups'));
                }

                if ($request->has('admission_status') && ! is_null($request->get('admission_status'))) {
                    $query->where('admission_status', $request->get('admission_status'));
                }

                if ($request->has('session') && ! is_null($request->get('session'))) {
                    $query->where('session', $request->get('session'));
                }
            })

            ->setRowAttr([
                'data-row-id' => function ($lists) {
                    return $lists->id;
                },
                'class' => function ($lists) {
                    return 'text-center ' . Study::updatedRow('id', $lists->id);
                }
            ])
            ->rawColumns(['actions', 'operations'])
            // ->escapeColumns([])
            ->make(true);
    }

    public function hsc_datasource(Request $request)
    {
        $lists = HscMeritList::query();

        return Datatables::of($lists)
            ->addColumn('actions', function ($lists) {
                $html = " <a href=" . route('student.meritlist.hsc.edit', $lists->id) . " class='btn btn-primary type-b duplicate_data' data-id=" . $lists->id . "  data-label='Hsc Merit Student'><i class='fas fa-copy'></i></a>";
                $html .= " <a href=" . route('student.meritlist.hsc.edit', $lists->id) . " class='btn btn-primary type-b edit_data' data-id=" . $lists->id . " data-label='Hsc Merit Student'><i class='fas fa-pencil'></i></a>";
                $html .= " <a href=" . route('student.meritlist.destroy', ['type' => 'hsc', 'id' => $lists->id]) . " class='btn btn-danger type-b delete_data' data-id=" . $lists->id . "><i class='fas fa-trash'></i></a>";
                return $html;
            })->filter(function ($query) use ($request) {

                if ($request->has('ssc_roll') && ! is_null($request->get('ssc_roll'))) {
                    $query->where('ssc_roll', $request->get('ssc_roll'));
                }

                if ($request->has('ssc_group') && ! is_null($request->get('ssc_group'))) {
                    $query->where('ssc_group', $request->get('ssc_group'));
                }

                if ($request->has('ssc_board') && ! is_null($request->get('ssc_board'))) {
                    $query->where('ssc_board', $request->get('ssc_board'));
                }

                if ($request->has('admission_status') && ! is_null($request->get('admission_status'))) {
                    $query->where('admission_status', $request->get('admission_status'));
                }

                if ($request->has('quota') && ! is_null($request->get('quota'))) {
                    $query->where('quota', $request->get('quota'));
                }

                if ($request->has('session') && ! is_null($request->get('session'))) {
                    $query->where('session', $request->get('session'));
                }
            })

            ->setRowAttr([
                'data-row-id' => function ($lists) {
                    return $lists->id;
                },
                'class' => function ($lists) {
                    return 'text-center ' . Study::updatedRow('id', $lists->id);
                }
            ])
            ->rawColumns(['actions', 'operations'])
            // ->escapeColumns([])
            ->make(true);
    }

    public function honours_edit($id)
    {
        $list = HonsMeritList::where('id', $id)->firstOrFail();

        $data = [
            'id' => $list->id,
            'name' => $list->name,
            'admission_roll' => $list->admission_roll,
            'session' => $list->session,
            'merit_pos' => $list->merit_pos,
            'merit_status' => $list->merit_status,
            'faculty' => $list->faculty,
            'subject' => $list->subject,
            'password' => $list->password,
            'admission_status' => $list->admission_status
        ];

        $html = view('BackEnd.student.admission.meritList.particles.hons_form', $data)->render();
        return response()->json([
            'status' => 200,
            'modal' => 'modal-lg',
            'html' => $html

        ], Response::HTTP_OK);
    }

    public function masters_edit($id)
    {
        $list = MastersMeritList::where('id', $id)->firstOrFail();

        $data = [
            'id' => $list->id,
            'name' => $list->name,
            'admission_roll' => $list->admission_roll,
            'session' => $list->session,
            'merit_pos' => $list->merit_pos,
            'merit_status' => $list->merit_status,
            'faculty' => $list->faculty,
            'subject' => $list->subject,
            'hons_roll' => $list->hons_roll,
            'major_degree' => $list->major_degree,
            'password' => $list->password,
            'admission_status' => $list->admission_status
        ];

        $html = view('BackEnd.student.admission.meritList.particles.msc_form', $data)->render();
        return response()->json([
            'status' => 200,
            'modal' => 'modal-lg',
            'html' => $html

        ], Response::HTTP_OK);
    }

    public function degree_edit($id)
    {
        $list = DegreeMeritList::where('id', $id)->firstOrFail();

        $data = [
            'id' => $list->id,
            'name' => $list->name,
            'admission_roll' => $list->admission_roll,
            'session' => $list->session,
            'merit_pos' => $list->merit_pos,
            'merit_status' => $list->merit_status,
            'groups' => $list->groups,
            'password' => $list->password,
            'admission_status' => $list->admission_status
        ];

        $html = view('BackEnd.student.admission.meritList.particles.deg_form', $data)->render();
        return response()->json([
            'status' => 200,
            'modal' => 'modal-lg',
            'html' => $html

        ], Response::HTTP_OK);
    }

    public function hsc_edit($id)
    {
        $list = HscMeritList::where('id', $id)->firstOrFail();

        $data = [
            'id' => $list->id,
            'name' => $list->name,
            'ssc_roll' => $list->ssc_roll,
            'session' => $list->session,
            'current_level' => $list->current_level,
            'rank' => $list->rank,
            'merit_status' => $list->merit_status,
            'ssc_group' => $list->ssc_group,
            'ssc_board' => $list->ssc_board,
            'passing_year' => $list->passing_year,
            'password' => $list->password,
            'admission_status' => $list->admission_status
        ];

        $html = view('BackEnd.student.admission.meritList.particles.hsc_form', $data)->render();
        return response()->json([
            'status' => 200,
            'modal' => 'modal-lg',
            'html' => $html

        ], Response::HTTP_OK);
    }

    public function honours_store(Request $request)
    {
        DB::beginTransaction();

        try {

            $id = $request->id;

            $action_type = $request->action_type;

            if ($action_type == 'update') {
                $list = HonsMeritList::where('id', $id)->firstOrFail();
                $msg = 'Merit Student Updated Successfully';
                $status = 'info';
            } else {
                $list = new HonsMeritList;
                $msg = 'Merit Student Created Successfully';
                $status = 'success';
            }

            $data = $request->all();

            $rules[] = HonsMeritList::validateRules($data);


            $this->validate($request, Arr::collapse($rules));

            $list->name = $request->name;
            $list->session = $request->session;
            $list->admission_roll = $request->admission_roll;
            $list->faculty = $request->faculty;
            $list->subject = $request->subject;
            $list->merit_status = $request->merit_status;
            $list->merit_pos = $request->merit_pos;
            $list->admission_status = $request->admission_status;
            $list->password = $request->password;
            $list->save();

            $list_id = $list->id;

            DB::commit();
            return response()->json([
                'status' => $status,
                'message' => $msg,
                'table' => 'datatable',
                'id' => $list->id

            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ], Response::HTTP_BAD_REQUEST); // 400
        }
    }

    public function masters_store(Request $request)
    {
        DB::beginTransaction();

        try {

            $id = $request->id;

            $action_type = $request->action_type;

            if ($action_type == 'update') {
                $list = MastersMeritList::where('id', $id)->firstOrFail();
                $msg = 'Merit Student Updated Successfully';
                $status = 'info';
            } else {
                $list = new MastersMeritList;
                $msg = 'Merit Student Created Successfully';
                $status = 'success';
            }

            $data = $request->all();

            $rules[] = MastersMeritList::validateRules($data);


            $this->validate($request, Arr::collapse($rules));

            $list->name = $request->name;
            $list->session = $request->session;
            $list->current_level = $request->current_level;
            $list->admission_roll = $request->admission_roll;
            $list->faculty = $request->faculty;
            $list->subject = $request->subject;
            $list->hons_roll = $request->hons_roll;
            $list->major_degree = $request->major_degree;
            $list->merit_status = $request->merit_status;
            $list->merit_pos = $request->merit_pos;
            $list->admission_status = $request->admission_status;
            $list->password = $request->password;
            $list->save();

            $list_id = $list->id;

            DB::commit();
            return response()->json([
                'status' => $status,
                'message' => $msg,
                'table' => 'datatable',
                'id' => $list->id

            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ], Response::HTTP_BAD_REQUEST); // 400
        }
    }

    public function degree_store(Request $request)
    {
        DB::beginTransaction();

        try {

            $id = $request->id;

            $action_type = $request->action_type;

            if ($action_type == 'update') {
                $list = DegreeMeritList::where('id', $id)->firstOrFail();
                $msg = 'Merit Student Updated Successfully';
                $status = 'info';
            } else {
                $list = new DegreeMeritList;
                $msg = 'Merit Student Created Successfully';
                $status = 'success';
            }

            $data = $request->all();

            $rules[] = DegreeMeritList::validateRules($data);


            $this->validate($request, Arr::collapse($rules));

            $list->name = $request->name;
            $list->session = $request->session;
            $list->admission_roll = $request->admission_roll;
            $list->groups = $request->groups;
            $list->merit_status = $request->merit_status;
            $list->merit_pos = $request->merit_pos;
            $list->admission_status = $request->admission_status;
            $list->password = $request->password;
            $list->save();

            $list_id = $list->id;

            DB::commit();
            return response()->json([
                'status' => $status,
                'message' => $msg,
                'table' => 'datatable',
                'id' => $list->id

            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ], Response::HTTP_BAD_REQUEST); // 400
        }
    }

    public function hsc_store(Request $request)
    {
        DB::beginTransaction();

        try {

            $id = $request->id;

            $action_type = $request->action_type;

            if ($action_type == 'update') {
                $list = HscMeritList::where('id', $id)->firstOrFail();
                $msg = 'Merit Student Updated Successfully';
                $status = 'info';
            } else {
                $list = new HscMeritList;
                $msg = 'Merit Student Created Successfully';
                $status = 'success';
            }

            $data = $request->all();

            $rules[] = HscMeritList::validateRules($data);

            $this->validate($request, Arr::collapse($rules));

            $list->name = $request->name;
            $list->session = $request->session;
            $list->ssc_roll = $request->ssc_roll;
            $list->ssc_group = $request->ssc_group;
            $list->ssc_board = $request->ssc_board;
            $list->current_level = $request->current_level;
            $list->passing_year = $request->passing_year;
            $list->merit_status = $request->merit_status;
            $list->rank = $request->rank;
            $list->password = $request->password;
            $list->quota = $request->password != '' ? 1 : 0;
            $list->admission_status = $request->admission_status;
            $list->save();

            $list_id = $list->id;

            DB::commit();
            return response()->json([
                'status' => $status,
                'message' => $msg,
                'table' => 'datatable',
                'id' => $list->id

            ], Response::HTTP_OK);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ], Response::HTTP_BAD_REQUEST); // 400
        }
    }
}
