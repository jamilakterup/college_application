<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Libs\Study;
use DataTables;

class AdminPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::orderBy('id', 'desc')->get();
        return view('BackEnd.admin.permission.index', compact('permissions'));
    }

    public function datasource(Request $request){
        $permission = Permission::query();

        return Datatables::of($permission)
                ->addColumn('actions', function ($permission) {
                    $html = "<a href=".route('admin.permission.edit', $permission->id)." class='btn btn-primary type-b duplicate_data'><i class='fa fa-copy'></i></a>";
                    $html .= " <a href=".route('admin.permission.edit', $permission->id)." class='btn btn-primary type-b edit_data' data-id=".$permission->id."><i class='fa fa-pencil'></i></a>";
                    $html .= " <a href=".route('admin.permission.destroy', $permission->id)." class='btn btn-danger type-b delete_data' data-id=".$permission->id."><i class='fa fa-trash'></i></a>";
                    return $html;
                })
                ->filter(function ($query) use ($request) {

                    if ($request->has('name') && ! is_null($request->get('name'))) {
                        $query->where('name','like', '%' . $request->get('name').'%');
                    }

                    if ($request->has('group_name') && ! is_null($request->get('group_name'))) {
                        $query->where('group_name', $request->get('group_name'));
                    }

                    if ($request->has('parent_group_name') && ! is_null($request->get('parent_group_name')) ) {
                        $query->where('parent_group_name', $request->get('parent_group_name'));
                    }
                })
                ->setRowAttr([
                    'data-row-id' => function($permission) {
                        return $permission->id;
                    },
                    'class'=> function($permission) {
                        return 'text-center ' . Study::updatedRow('id', $permission->id);
                    }
                ])
                 // ->orderColumn('id', true)
                ->rawColumns(['actions'])
                // ->escapeColumns([])
                ->make(true);
                // ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $html = view('BackEnd.admin.permission.particles.form')->render();
        return response()->json([
                'status' => 202,
                'html' => $html

            ],Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id=null)
    {
        $this->validate($request, [
            'name' => 'required',
            'group_name' => 'required',
            'parent_group_name' => 'required',
            'guard_name' => 'required'
        ]);

        try {

            $id = $request->id;

            $action_type = $request->action_type;
            if($action_type == 'update'){

                $permission = Permission::find($id);
                $msg = 'Permission Updated Successfully';
                $status = 'info';
            }else{
                $count_p = Permission::where('name', $request->name)->where('group_name', $request->group_name)->count();

                if($count_p){
                    return response()->json([
                        'error' => 'Already exists with this name and guard name'
                    ],Response::HTTP_NOT_ACCEPTABLE);
                }

                $permission = new Permission;
                $msg = 'Permission Added Successfully';
                $status = 'success';
            }

            $permission->name = $request->name;
            $permission->group_name = $request->group_name;
            $permission->parent_group_name = $request->parent_group_name;
            $permission->guard_name = $request->guard_name;
            $permission->save();

            $objects = Permission::where('id',$permission->id)->get(['id', 'name', 'group_name', 'parent_group_name', 'guard_name'])->first()->toArray();
            $row_values = array_values($objects);

            \Artisan::call('cache:forget spatie.permission.cache');
            \Artisan::call('cache:clear');

            return response()->json([
                'status' => $status,
                'message' => $msg,
                'id' => $permission->id,
                'table' => 'datatable',
                'row_values' => $row_values
            ],Response::HTTP_OK);

            
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST); // 400
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::find($id);
        $data = [
            'name' => $permission->name,
            'group_name' => $permission->group_name,
            'parent_group_name' => $permission->parent_group_name,
            'guard_name' => $permission->guard_name,
        ];

        $html = view('BackEnd.admin.permission.particles.form', $data)->render();
        return response()->json([
                'status' => 200,
                'html' => $html

            ],Response::HTTP_OK);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

            $permission = Permission::find($id)->delete();

            \Artisan::call('cache:forget spatie.permission.cache');
            \Artisan::call('cache:clear');

            return response()->json([
                'status' => 'warning',
                'message' => 'Permission Deleted Successfully',
                'table' => 'datatable',
                'id' => $id,

            ],Response::HTTP_OK);

            
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST);
        }
    }
}
