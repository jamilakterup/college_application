<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\UniversitySubject;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class SubjectListController extends Controller
{
    public function index()
    {
        try{
            return view(pages('teacher.subject.index'));
        } catch (\Illuminate\Database\QueryException $e) {
            return \redirect()->back()->with('error', $e->errorInfo[2]);
        }
    }


    public function datasource(Request $request){
        $records = UniversitySubject::query();

        return DataTables::of($records)
            ->addColumn('actions',pages('teacher.subject.action_buttons'))
            ->setRowAttr([
                'data-row-id' => function($records) {
                    return $records->id;
                },
                'dt-index' => function() {
                    static $index = 0;
                    return ++$index;
                }
            ])
            ->addIndexColumn()
            ->setRowData([
                'data-name' => function($records) {
                    return 'row-'.$records->id;
                }
                ])
            ->rawColumns(['actions', 'active_status'])
            // ->escapeColumns([])
            ->make(true);
    
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $html = view(pages('teacher.subject.form'))->render();
        return response()->json([
                'static' => true,
                'html' => $html
            ],Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => "required|max:200"
        ]);

        try {
            $id = $request->id;

            $action_type = $request->action_type;
            
            if($action_type == 'update'){
                $data = UniversitySubject::find($id);
                $msg = "Subject Updated Successful for *$data->name* ";
                $status = 'info';
            }else{
                $data = new UniversitySubject();
                $msg = 'Subject Added Successfully';
                $status = 'success';
            }

            $data->sub_name = $request->name;
            $data->save();

            return response()->json([
                'status' => $status,
                'message' => $msg,
                'id' => $data->id,
                'table' => 'datatable'
            ],Response::HTTP_OK);

            
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST); // 400
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $list = UniversitySubject::find($id);
        $data = [
            'list' => $list
        ];

        $html = view(pages('teacher.subject.form'), $data)->render();

        return response()->json([
                'static' => true,
                'html' => $html
            ],Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            UniversitySubject::find($id)->delete();

            return response()->json([
                'status' => 'warning',
                'message' => 'Subject Deleted Successfully',
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
