<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\Group;
use App\Models\Subject;
use App\Models\UserGroup;
use App\Models\UserPermissionAssign;
use App\Models\UserSubAssign;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:user.create', ['only' => ['create','store']]);
         $this->middleware('permission:user.edit', ['only' => ['edit','update']]);
         $this->middleware('permission:user.delete', ['only' => ['destroy']]);
         $this->middleware(
            'permission:user.index|user.create|user.edit|user.delete', ['only' => ['index','show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Easy CollegeMate - User Management';
        $breadcrumb = 'admin.user.index:User Management|admin.user.index:Role|Dashboard';

        $users = User::paginate(Study::paginate());
        return view('BackEnd.admin.user.index', compact('users', 'title', 'breadcrumb'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $title = 'Easy CollegeMate - Add User';
        $breadcrumb = 'admin.user.index:User Management|Add New User';
        $user='';
        $level_year = [];$exam_year = [];$hsc_group = [];$faculty = []; $session = [];$department = [];
        $roles = Role::pluck('name', 'id');
        return view('BackEnd.admin.user.create', compact('title', 'breadcrumb', 'roles', 'user', 'level_year','exam_year', 'hsc_group', 'faculty','session','department'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation Data
        $data = $request->all();
        $validation = User::validate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;  

        //Role Checker
        if($request->get('roles') == '') :
            $error_message = 'Please select a role!';
            return Redirect::back()->withInput()->with('error',$error_message);
        endif;  

        $role_exists = Role::whereIn('id',$request->get('roles'))->count();
        if($role_exists == 0) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->withInput()->with('error',$error_message);
        endif;  
        
        //Insert To User
        $user = new User;
        $user->username = $request->get('username');
        $user->email = $request->get('email');
        $user->password = Hash::make($request->get('password'));
        $user->full_name = $request->get('full_name');
        $user->user_type = Study::$user_type['operator'];
        $user->save();

        $id = $user->id;

        $level_year = $request->level_year;
        $exam_year = $request->exam_year;
        $hsc_group = $request->hsc_group;
        $faculty = $request->faculty;
        $session = $request->session;
        $department = $request->department;
        $hsc_subject = $request->hsc_subject;

        UserPermissionAssign::where('user_id',$id)->delete();

        if(!empty($level_year))
        for ($i=0; $i<count($level_year); $i++) {
            $data_array = ['type'=> 'level', 'user_id' => $id, 'value' => $level_year[$i]];
            UserPermissionAssign::create($data_array);
        }

        if(!empty($exam_year))
        for ($i=0; $i<count($exam_year); $i++) {
            $data_array = ['type'=> 'exam_year', 'user_id' => $id, 'value' => $exam_year[$i]];
            UserPermissionAssign::create($data_array);
        }

        if(!empty($hsc_group))
        for ($i=0; $i<count($hsc_group); $i++) {
            $data_array = ['type'=> 'hsc_group', 'user_id' => $id, 'value' => $hsc_group[$i]];
            UserPermissionAssign::create($data_array);
        }

        if(!empty($faculty))
        for ($i=0; $i<count($faculty); $i++) {
            $data_array = ['type'=> 'faculty', 'user_id' => $id, 'value' => $faculty[$i]];
            UserPermissionAssign::create($data_array);
        }

        if(!empty($session))
        for ($i=0; $i<count($session); $i++) {
            $data_array = ['type'=> 'session', 'user_id' => $id, 'value' => $session[$i]];
            UserPermissionAssign::create($data_array);
        }

        if(!empty($department))
        for ($i=0; $i<count($department); $i++) {
            $data_array = ['type'=> 'department', 'user_id' => $id, 'value' => $department[$i]];
            UserPermissionAssign::create($data_array);
        }

        if(!empty($hsc_subject))
        for ($i=0; $i<count($hsc_subject); $i++) {
            $data_array = ['type'=> 'hsc_subject', 'user_id' => $id, 'value' => $hsc_subject[$i]];
            UserPermissionAssign::create($data_array);
        }
        
        
        if ($request->roles) {
            $user->assignRole($request->roles);
        }

        //Page Calculate
        $count = User::where('user_type',Study::$user_type['operator'])->count();
        $page = ceil($count/Study::paginate());

        $message = 'You have successfully created a new user';
        return Redirect::route('admin.user.index', ['page' => $page])
                        ->withMessage($message)
                        ->withId($id);
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
        
        $title = 'Easy CollegeMate - Edit User';
        $breadcrumb = 'admin.user.index:User Management|Edit User';

        $user = User::find($id);
        $roles = Role::select('name', 'id')->get();
        $departments = Group::orderBy('id')->get();
        $subjects = Subject::orderBy('id')->get();

        $level_year = UserPermissionAssign::where('user_id', $id)->where('type','level')->pluck('value','value')->toArray();
        $exam_year = UserPermissionAssign::where('user_id', $id)->where('type','exam_year')->pluck('value','value')->toArray();
        $hsc_group = UserPermissionAssign::where('user_id', $id)->where('type','hsc_group')->pluck('value','value')->toArray();
        $faculty = UserPermissionAssign::where('user_id', $id)->where('type','faculty')->pluck('value','value')->toArray();;
        $session = UserPermissionAssign::where('user_id', $id)->where('type','session')->pluck('value','value')->toArray();
        $department = UserPermissionAssign::where('user_id', $id)->where('type','department')->pluck('value','value')->toArray();
        $hsc_subject = UserPermissionAssign::where('user_id', $id)->where('type','hsc_subject')->pluck('value','value')->toArray();

        return view('BackEnd.admin.user.edit', compact('title', 'breadcrumb', 'user', 'roles', 'level_year','exam_year', 'hsc_group', 'faculty','session','department','hsc_subject'));
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

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';          
            return Redirect::back()->with('error',$error_message);
        endif;

        $data = $request->all();
        $validation = User::updateValidate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;  

        //Role Checker
        if($request->get('roles') == '') :
            $error_message = 'Please select a role!';
            return Redirect::back()->withInput()->with('error',$error_message);
        endif;  

        $role_exists = Role::whereIn('id',$request->get('roles'))->count();
        if($role_exists == 0) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->withInput()->with('error',$error_message);
        endif;      

        //Update User
        $user = User::find($id);
        $user->full_name = $request->get('full_name');
        $user->username = $request->get('username');
        $user->email = $request->get('email');
        $user->update();

        $user->roles()->detach();
        if ($request->roles) {
            $user->assignRole($request->roles);
        }

        //$id = $user->id;
       
        $level_year = $request->level_year;
        $exam_year = $request->exam_year;
        $hsc_group = $request->hsc_group;
        $faculty = $request->faculty;
        $session = $request->session;
        $department = $request->department;
        $hsc_subject = $request->hsc_subject;

        UserPermissionAssign::where('user_id',$id)->delete();
        if(!empty($level_year))
        for ($i=0; $i<count($level_year); $i++) {
            $data_array = ['type'=> 'level', 'user_id' => $id, 'value' => $level_year[$i]];
            UserPermissionAssign::create($data_array);
        }

        if(!empty($exam_year))
        for ($i=0; $i<count($exam_year); $i++) {
            $data_array = ['type'=> 'exam_year', 'user_id' => $id, 'value' => $exam_year[$i]];
            UserPermissionAssign::create($data_array);
        }

        if(!empty($hsc_group))
        for ($i=0; $i<count($hsc_group); $i++) {
            $data_array = ['type'=> 'hsc_group', 'user_id' => $id, 'value' => $hsc_group[$i]];
            UserPermissionAssign::create($data_array);
        }

        if(!empty($faculty))
        for ($i=0; $i<count($faculty); $i++) {
            $data_array = ['type'=> 'faculty', 'user_id' => $id, 'value' => $faculty[$i]];
            UserPermissionAssign::create($data_array);
        }

        if(!empty($session))
        for ($i=0; $i<count($session); $i++) {
            $data_array = ['type'=> 'session', 'user_id' => $id, 'value' => $session[$i]];
            UserPermissionAssign::create($data_array);
        }

        if(!empty($department))
        for ($i=0; $i<count($department); $i++) {
            $data_array = ['type'=> 'department', 'user_id' => $id, 'value' => $department[$i]];
            UserPermissionAssign::create($data_array);
        }
        if(!empty($hsc_subject))
        for ($i=0; $i<count($hsc_subject); $i++) {
            $data_array = ['type'=> 'hsc_subject', 'user_id' => $id, 'value' => $hsc_subject[$i]];
            UserPermissionAssign::create($data_array);
        }

        //Page count
        $count = User::where('id', '<=', $id)->count();
        $page = ceil($count/Study::paginate());

        $message = 'You have successfully updated the user';
        return Redirect::route('admin.user.index', ['page' => $page])
                        ->with('info',$message)
                        ->withId($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!is_null($user)) {
            $user->delete();
        }

        session()->flash('warning', 'User has been deleted !!');
        return back();
    }

    public function reset($id) {

        $title = 'Easy CollegeMate - Reset User Password';
        $breadcrumb = 'admin.user.index:User Management|Reset User Password';

        $user = User::find($id);

        return view('BackEnd.admin.user.reset')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withUser($user);

    }



    public function postReset(Request $request, $id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';          
            return Redirect::back()->withError_message($error_message);
        endif;

        $data = $request->all();
        $validation = User::resetValidate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;  

        $user = User::find($id);
        $user->password = bcrypt($request->get('password'));
        $user->update();

        //Page count
        $count = User::where('id', '<=', $id)->count();
        $page = ceil($count/Study::paginate());

        $message = 'You have successfully reset the user password';
        return Redirect::route('admin.user.index', ['page' => $page])
                        ->with('success',$message)
                        ->withId($id);          

    }
}
