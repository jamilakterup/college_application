<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MastersMeritList;
use Illuminate\Http\Request;
use App\Libs\Study;
use Illuminate\Support\Facades\Redirect;
use DB;

class MastersMeritListController extends Controller
{

    public function index() {

    $title = 'Easy CollegeMate - College Management';
        $breadcrumb = 'student.masters:Masters|Dashboard';
        $merit_students = MastersMeritList::where('delete_status',0)->paginate(Study::paginate());

        

        return view('BackEnd.student.admission.masters.meritlist', compact('title', 'breadcrumb', 'merit_students'));

    }

    public function show() {

        $title = 'Easy CollegeMate - College Management';
        $breadcrumb = 'student:College Management|Dashboard';
        $hons_merit_students = MastersMeritList::where('delete_status',0)->paginate(Study::paginate());

        

        return view('BackEnd.student.admission.masters.meritlist')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withHons_merit_students($hons_merit_students);


    }

    public function meritlist(Request $request)
   {
        $title = 'Easy CollegeMate - College Management';
        $breadcrumb = 'students.masters.meritlist:Masters Merit List|Masters Merit List';
        $admission_roll = $request->get('admission_roll');  
        $query = Study::searchMastersMeritlistStudent( $admission_roll);
        $num_rows = $query->count();
        $merit_students = $query->paginate(Study::paginate());
        return view('BackEnd.student.admission.masters.meritlist.index', compact('title', 'breadcrumb', 'merit_students','num_rows', 'admission_roll'));                                            
        
    }


    public function meritlistUpload()
   {

        $title = 'Easy CollegeMate - College Management';
        $breadcrumb = 'students.master.meritlist:Masters Merit List|Upload Merit List';
        $merit_students = MastersMeritList::where('delete_status', 0)->paginate(Study::paginate());

        return  view('BackEnd.student.admission.masters.meritlist.upload', compact('title', 'merit_students'));                    
    }


    public function meritListEdit($id)
   {
        $title = 'Easy CollegeMate - College Management';
        $breadcrumb = 'students.masters.meritlist:Masters Merit List| Merit List';
        $meritstudent = MastersMeritList::find($id);
        $group_list= selective_multiple_faculty();

        
        return view('BackEnd.student.admission.masters.meritlist.edit',compact('title', 'breadcrumb', 'group_list', 'id', 'meritstudent'));

        
    }


        public function postUpload(Request $request)
   {


    if ($request->hasFile('material_csv'))
   {

        $name = $request->file('material_csv');
        $extension = $name->getClientOriginalExtension();
       if(strtolower($extension) == 'csv')        
  
        {

    function csv_to_array($filename='', $delimiter=',')
         {
             if(!file_exists($filename) || !is_readable($filename))
                 return FALSE;
          
             $header = NULL;
             $data = array();
             if (($handle = fopen($filename, 'r')) !== FALSE)
             {
                 while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
                 {
                     if(!$header)
                         $header = $row;
                     else
                         $data[] = array_combine($header, $row);
                 }

                 fclose($handle);
             }
                        return $data;
         }

                    $csvFile = $request->file('material_csv');
                 
                    $areas = csv_to_array($csvFile);

                   // var_dump( $areas);

                    foreach ($areas as $area):
 
                       foreach($area as $key => $single_area) :


                            if($key=='admission_roll'):
                                $admission_roll=$single_area;
                            endif;
                            if($key=='hons_roll'):
                                $hons_roll=$single_area;

                            endif;

                             if($key=='name'):
                                $name=$single_area;
                            endif;
                            if($key=='father_name'):
                                $father_name=$single_area;
                            endif;
                             if($key=='mother_name'):
                                $mother_name=$single_area;
                            endif;
                             if($key=='faculty'):
                                $faculty=$single_area;
                            endif;
                             if($key=='subject'):
                                $subject=$single_area;
                            endif;

                             if($key=='merit_pos'):
                                $merit_pos=$single_area;
                            endif;
                              if($key=='merit_status'):
                                $merit_status=$single_area;
                            endif;
                              if($key=='admission_status'):
                                $admission_status=$single_area;
                            endif;                            
                              if($key=='major_degree'):
                                $major_degree=$single_area;
                            endif; 
                         endforeach; 

                         DB::table('masters_merit_list')->insert(
                          array('admission_roll' =>$admission_roll,'hons_roll'=>$hons_roll,'name'=>$name,
                          'father_name'=>$father_name,'mother_name'=>$mother_name,
                          'faculty'=>$faculty,'subject'=>$subject,'merit_pos'=>$merit_pos,'merit_status'=>$merit_status, 'admission_status'=>$admission_status,'major_degree'=>$major_degree));


                         endforeach;

                   $message = 'You have successfully uploaded';
                   return Redirect::route('students.masters.meritlist')
                        ->with('success',$message);
             }                
          

         $message = 'Format Not Match';
                   return Redirect::route('students.masters.meritlist')
                        ->with('warning',$message);

                    }                       
        
    }

    
public function meritlistFormatDownload() {

    return response()->download(public_path().'/csv/masters_merit_list.csv');

}


    public function meritlistSingleDelete(Request $request) {

        $title = 'Easy CollegeMate - Students HSC'; 
        $breadcrumb = 'students.masters.meritlist:Merit List|Dashboard';

        
                    
        $id=$request->get('id'); 
        
        $meritStudent = MastersMeritList::find($id); 
        
        $meritStudent->delete_status=1;
        $meritStudent->save();

        $message = 'You have successfully Deleted';

       return Redirect::route('student.masters.meritlist')
                        ->with('warning',$message);


    }

        public function meritListAlldelete(Request $request) {

        $title = 'Easy CollegeMate - Students HSC'; 
        $breadcrumb = 'students.honours.meritlist:Merit List|Dashboard';

        
                    
        $id=$request->get('id'); 
        
         MastersMeritList::truncate();     

        $message = 'You have successfully Deleted Merit List';
       return Redirect::route('students.masters.meritlist')
                        ->with('warning',$message);

    }


    public function meritListEditDone(Request $request) {
        $id =$request->get('id');
        $admission_roll=$request->get('admission_roll');
        $father_name=$request->get('father_name');     
        $name=$request->get('name');
        $mother_name=$request->get('mother_name');
        $faculty=$request->get('faculty');
        $subject=$request->get('subject');
        $major_degree=$request->get('major_degree');
        $merit_status=$request->get('merit_status');

        $hons_roll=$request->get('hons_roll');
        
    

        $meritstudent = MastersMeritList::find($id);

        $meritstudent->admission_roll=$admission_roll;
        $meritstudent->father_name=$father_name;
        $meritstudent->name=$name;
        $meritstudent->mother_name=$mother_name;
        $meritstudent->faculty=$faculty;

        $meritstudent->subject=$subject;
        $meritstudent->major_degree=$major_degree;
        $meritstudent->merit_status=$merit_status;
        $meritstudent->hons_roll=$hons_roll;


        $meritstudent->save();

        $title = 'Easy CollegeMate - Students Masters'; 
        $breadcrumb = 'student.masters.meritlist:Merit List|Dashboard';

        $count = MastersMeritList::where('id', '<=', $id)->count();
        $page = ceil($count/Study::paginate());

        $message = 'You have successfully Updated';

       return Redirect::route('students.masters.meritlist', ['page' => $page])
                        ->with('info', $message)
                        ->withId($id);

    }


    /*public function search() {

        $title = 'Easy CollegeMate - Students HSC';
        $breadcrumb = 'student:College Management|Dashboard';   
        
        //Search Material
        $id = $request->get('id');
        $ssc_roll = $request->get('ssc_roll');
        $groups = $request->get('groups');
        $current_level = $request->get('current_level');
        $session = $request->get('session');


        

        $hscstudents = Study::searchHscStudent($id, $ssc_roll, $groups, $current_level, $session);


        $group_lists = ['' => 'Select Group','science' => 'Science','arts' => 'Humanities','commerce' => 'Business Studies'];

        $current_level_lists = ['' => 'Study Level','HSC 1st year' => 'HSC 1st year','HSC 2nd year' => 'HSC 2nd year'];

        $session_lists = ['' => 'Select Session','2011-2012' => '2011-2012','2012-2013' => '2012-2013','2013-2014' => '2013-2014','2015-2016' => '2015-2016','2016-2017' => '2016-2017','2017-2018' => '2017-2018','2018-2019' => '2018-2019'];

        return view('student.honours.index')
                    ->withTitle($title)
                    ->withBreadcrumb($breadcrumb)
                    ->withHons_merit_students($hons_merit_students)
                    ->withCurrent_level_lists($current_level_lists) 
                    ->withSession_lists($session_lists)                                     
                    ->withGroup_lists($group_lists);

    }*/
}
