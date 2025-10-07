<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Auth;

class AdminCollegeController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:college.create', ['only' => ['create','store']]);
         $this->middleware('permission:college.edit', ['only' => ['edit','update']]);
         $this->middleware('permission:college.delete', ['only' => ['destroy']]);
         $this->middleware(
            'permission:college.index|college.create|college.edit|college.delete', ['only' => ['index','show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $title = 'Easy CollegeMate - College Management';
        $breadcrumb = 'admin.college.index:College Management|Dashboard';
        $colleges = College::paginate(Study::paginate());

        return view('BackEnd.admin.college.index', compact('title', 'breadcrumb', 'colleges'));
    }

    public function create() {

        $title = 'Easy CollegeMate - Add College';
        $breadcrumb = 'admin.college.index:College Management|Add New College';

        return view('BackEnd.admin.college.create', compact('title', 'breadcrumb'));

    }



    public function store(Request $request) {

        $data = $request->all();
        $validation = College::validate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;

        $logo = $request->file('logo');
        $filename = rand(1, 999999999) . $logo->getClientOriginalName();
        $upload_path = public_path('upload/college/' . $filename);
        $db_path = 'upload/college/' . $filename;

        Image::make($logo->getRealPath())->save($upload_path);
        
        
        
        $college = new College;
        $college->college_name = $request->get('college_name');
        $college->college_name_bengali = $request->get('college_name_bengali');
        $college->logo = $filename;
        $college->website = $request->get('website');
        $college->area_name = $request->get('area_name');
        $college->area_name_bengali = $request->get('area_name_bengali');
        $college->college_code = $request->get('college_code');
        $college->biller_id = $request->get('biller_id');      
        $college->phone = $request->get('phone');
        $college->establish_date = $request->get('establish_date');
        $college->save();

        $id = $college->id;

        $page = ceil(College::count()/Study::paginate());

        $message = 'You have successfully created a new college';
        return Redirect::route('admin.college.index', ['page' => $page])
                        ->with('success',$message)
                        ->withId($id);
        
    }



    public function show($id) {

        $college = College::find($id);
        $title = 'Easy CollegeMate - College - ' . $college->college_name;
        $breadcrumb = 'admin.college.index:College Management|' . $college->college_name;

        return view('admin.college.show')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withCollege($college);

    }



    public function edit($id) {

        $title = 'Easy CollegeMate - Edit College';
        $breadcrumb = 'admin.college.index:College Management|Edit College';
        $college = College::find($id);

        return view('BackEnd.admin.college.edit')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withCollege($college);

    }



    public function update(Request $request, $id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;          

        $data = $request->all();
        $validation = College::updateValidate($data);

        if($validation->fails()) :
            return Redirect::back()->withInput()->withErrors($validation);
        endif;  

        $college = College::find($id);      

        if($request->hasFile('logo')) :

            $logo = $request->file('logo');
            $filename = rand(1, 999999999) . $logo->getClientOriginalName();
            $upload_path = public_path('upload/college/' . $filename);
            $db_path = 'upload/college/' . $filename;

            Image::make($logo->getRealPath())->save($upload_path);

            //unlink existing logo
            $unlink_path = 'public/' . $college->logo; 
            if(file_exists($unlink_path)) :
                unlink($unlink_path);   
            endif;  

            $college->logo = $filename;         

        endif;

        $college->college_name = $request->get('college_name');
        $college->college_name_bengali = $request->get('college_name_bengali');
        $college->website = $request->get('website');
        $college->area_name = $request->get('area_name');
        $college->area_name_bengali = $request->get('area_name_bengali');
        $college->college_code = $request->get('college_code');
        $college->biller_id = $request->get('biller_id');      
        $college->phone = $request->get('phone');
        $college->establish_date = $request->get('establish_date');
        $college->update(); 

        $count = College::where('id', '<=', $id)->count();
        $page = ceil($count/Study::paginate()); 

        $message = 'You have successfully updated the college';     
        return Redirect::route('admin.college.index', ['page' => $page])
                        ->with('success',$message)
                        ->withId($id);                  

    }



    public function destroy(Request $request, $id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;

        $college = College::find($id);

        //unlink existing logo
        $unlink_path = 'public' . $college->logo; 
        if(file_exists($unlink_path)) :
            unlink($unlink_path);   
        endif;

        $college->delete();

        $error_message = 'You have deleted the college';
        return Redirect::back()->with('error',$error_message);         

    }



    public function status(Request $request, $id) {

        if($id !== $request->get('id')) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;

        $status = $request->get('status');

        if($status != 1 && $status != 0) :
            $error_message = 'Something went wrong! Please try again';
            return Redirect::back()->with('error',$error_message);
        endif;  

        if($status == 1) :
            $colleges = College::all();

            foreach($colleges as $college) :
                $college_id = $college->id;
                $college = College::find($college_id);
                $college->status = 0;
                $college->update();
            endforeach; 
        endif;

        $college = College::find($id);
        $college->status = $status;
        $college->update();

        if($status == 1) :
            $message = 'You have updated the college status. Only one college can active at a time';
            return Redirect::back()->with('success',$message);
        else :
            $error_message = 'You have deactivated the college';
            return Redirect::back()->with('warning',$error_message);     
        endif;  

    }

    public function subInput() {

        return view('admin.college.subinput');                    

    }   
    
        public function csvStudentupload() {

            if($request->hasFile('csv_file')) {
          
                $file = $request->file('csv_file');
                $extension = $file->getClientOriginalExtension();
                
                if(strtolower($extension) == 'csv') {
                    ini_set("auto_detect_line_endings", true);
                    $tmp_file = $file->getRealPath();
                    $handle = fopen($tmp_file, 'r');
                    $handle2 = fopen($tmp_file, 'r');
                    $csv_rolls = [];
                    $row = 1;
                    
                    while(($fileop = fgetcsv($handle, 1000,",")) !== FALSE ) {
                    
                        if($row != 1) {
                            /*if($row ==3)
                                return $fileop ;*/
                                            

                                $reg = $fileop[0];
                                $name = $fileop[1];
                                $type = $fileop[2];

                                
                                $regs = explode("<br>",$reg);
                            
                                $names = explode("<br>",$name);
                                $types = explode("<br>",$type);
                            
                                $id= $regs[0];
                                $name = $names[0];
                                $fname = $names[1];
                                $mname = $names[2];
                                $group = $types[1];
                                $type = str_replace("\n", '', $group);
                                $type = ltrim($type);
                                if($type=='SCIENCE'){
                                    $type = 'Science';
                                }
                                if($type=='HUMANITIES'){
                                    $type = 'Humanities';
                                }
                                if($type=='BUSI. STUDIES'){
                                    $type = 'Business Studies';
                                }
                                //print_r( $id.'-'.$type.'-'.$name.'-'.$fname.'<br />');
                               $have_student = DB::select("select * from `student_info_hsc` WHERE `student_info_hsc`.`id` = $id");
                               if(count($have_student)>0){
                                   DB::table('student_info_hsc')->where('id',$id)->delete();
                               }
                                DB::table('student_info_hsc')->insert(
                                array('id'=>$id,'class_roll'=>$id, 'groups'=>$type, 'name'=> $name, 'session'=>'2017-2018','current_level'=>'HSC 2nd Year','father_name'=>$fname,'mother_name'=>$fname)
                                );
                                
                                
                                
                        }
                        $row++;
                    }
                    
                }
                            
            }
            return 'ok';
    }
}
