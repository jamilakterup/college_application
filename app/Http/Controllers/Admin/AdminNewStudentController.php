<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Image;
use DB;
use IdRollGenerate;

class AdminNewStudentController extends Controller
{
	function __construct()
    {
         $this->middleware('permission:add_new_student.manage');
    }

    public function index() {

		$title = 'Easy CollegeMate - Newstudent Information Header';
		$breadcrumb = 'admin.newstudent.index:New Student Management';

		return view('BackEnd.admin.new_student.index', compact('title', 'breadcrumb'));

	}


	public function hscnewstudent(){
		$title = 'Easy CollegeMate - Newstudent Information Header';
		$breadcrumb = 'admin.newstudent.index:New Student Management';
		   $admission_group = ['Science' => 'Science','Humanities' => 'Humanities','Business Studies' => 'Business Studies'];
		return view('BackEnd.admin.new_student.hscform',compact('title', 'breadcrumb'))->with('admission_group',$admission_group)
		;
	}

	public function hscGroupChange() {

	$group = $_POST['group'];
	$course = $_POST['course'];

	return view('BackEnd.admin.new_student.hsc_group_change')
						  ->withGroup($group)
						  ->withCourse($course);



	}
    public function hscsubmit(Request $request) {

    	$this->validate($request, [
    		'student_name' => 'required',
    		'session' => 'required',
    		'hsc_group' => 'required',
    		'photo' => 'required|mimes:jpeg,jpg,png',
    		'father_name' => 'required',
    		'mother_name' => 'required',
    		'gender' => 'required',
    		'ssc_group' => 'required',
    		'ssc_session' => 'required',
    		'current_level' => 'required',
    		'ssc_roll' => 'required|numeric',
    		'mobile' => 'numeric'
    	]);
		
		$temp_entry_time = date('Y-m-d G:i:s');
		$entry_time = date('Y-m-d G:i:s', strtotime($temp_entry_time));
        $filename='';  
		$logo = $request->file('photo');
		$insCode = INS_CODE;
		if($logo!=''){
			$folder = public_path('upload/college/hsc/'.$request->session);
			create_dir($folder);
			$filename = rand(1, 99999999999) .'.jpg';
			$upload_path = $folder.'/'.$filename;
			$db_path = $upload_path;

			Image::make($logo->getRealPath())->save($upload_path);

		}

		$hsc_group = $request->get('hsc_group');
		$compulsorycourse = $request->get('compulsorycourse');

		$compulsorycourse =  implode (",", $compulsorycourse);

		$selectivecourse = $request->get('selectivecourse');
		$selectivecourse =  implode (",", $selectivecourse);		
		 //$admission_session = DB::select("SELECT * from hsc_online_adm_config WHERE category='admission_session'");
		$session = $request->get('session');			
		$ssc_roll = $request->get('ssc_roll');
    	DB::table('hsc_admitted_students')->insert(
	       array('entry_time'=>$entry_time, 
				'photo'=>$filename , 
				'name' =>$request->get('student_name'),
				'compulsory' =>$compulsorycourse,
				'selective'=> $selectivecourse,
				'optional'=>$request->get('selecting'),
				'blood_group'=>$request->get('blood_group'),
				'hsc_group'=>$request->get('hsc_group'),
				'fathers_name'=>$request->get('father_name'),
				'mothers_name'=>$request->get('mother_name'),
				'date_of_birth'=>$request->get('birth_date'),
				'ssc_roll'=>$request->get('ssc_roll'),          
				'ssc_reg_no'=>$request->get('ssc_registration'),
				'ssc_group'=>$request->get('ssc_group'),
				'ssc_institution'=>$request->get('ssc_institute'),
				'ssc_session'=>$request->get('ssc_session'),
				'ssc_gpa'=>$request->get('ssc_gpa'),
				'sex'=>$request->get('gender'),
				'religion'=>$request->get('religion'),
				'mobile'=>$request->get('mobile'),
				'admission_session'=>$session
				)
        );
		  $courses =  DB::select("SELECT * FROM course_hsc_new WHERE `groups` = '".strtolower($hsc_group)."'");

		  $cods = array();

		  foreach($courses as $course){
			if (strpos($course->subjects, ',') !== FALSE) { 
			  $subjects = explode(",", $course->subjects);
			  $codes = explode(",", $course->codes);
			  foreach ($subjects as $key => $subject) {
				$cods[$codes[$key]] = $subject;
			  }
			} else {
			  $cods[$course->codes] = $course->subjects;
			}
		  }

		  $compulsory = explode(",", $compulsorycourse);
		  $selective = explode(",", $selectivecourse);
		  $optional = explode(",", $request->get('selecting'));

		  $compulsory_string = '';
		  $selective_string = '';
		  $optional_string = '';

		  foreach ($compulsory as $value) {
			$compulsory_string .= $cods[$value]."(".$value."),";
		  }
		  $compulsory_string=rtrim($compulsory_string,",");

		  foreach ($selective as $value) {
			$selective_string .= $cods[$value]."(".$value."),";
		  }
		  $selective_string=rtrim($selective_string,",");

		  foreach ($optional as $value) {
			$optional_string .= $cods[$value]."(".$value."),";
		  }
		  $optional_string=rtrim($optional_string,",");
		  
		  $compulsory_string = str_replace("-", ",", $compulsory_string);
		  $selective_string = str_replace("-", ",", $selective_string);
		  $optional_string = str_replace("-", ",", $optional_string);

		 $all_string = $compulsory_string.",".$selective_string.",".$optional_string;
		 
		  $prefix='hsc_';
		  $catagory="0"; // for hsc
		  $id = IdRollGenerate::id_generate_hsc($session,$hsc_group);
  		$class_roll= IdRollGenerate::roll_generate_hsc($id);
          $ref_id=DB::select("SELECT * from hsc_admitted_students WHERE admission_session='$session' AND ssc_roll= $ssc_roll");
		  $st_ref_id = $ref_id[0]->auto_id;
		 $current_level= $request->get('current_level');
       	DB::table('student_info_hsc')->insert(
       	array('id'=>$id, 'name' =>$request->get('student_name'),'class_roll'=>$class_roll,'session'=>$session ,'groups'=>$hsc_group ,'current_level'=>$current_level ,'father_name'=>$request->get('father_name'),'mother_name'=> $request->get('mother_name'),'birth_date'=>$request->get('birth_date'),'gender'=>$request->get('gender') ,'contact_no'=>$request->get('mobile'),'religion'=> $request->get('religion'),'image'=> $filename,'refference_id'=>$st_ref_id ,'ssc_roll'=> $ssc_roll,'hsc_subjects_info'=>$all_string, 'registration_id'=>$request->get('registration_id'))
        );
		
		$results =DB::select(" SELECT  * FROM payslipheaders 
	 	INNER join 
	 	payslipgenerators On payslipheaders.id = payslipgenerators.payslipheader_id
	 	WHERE pro_group='HSC' and group_dept='$hsc_group' and type = 'formfillup'  and start_date <= CURDATE() and end_date >= CURDATE()");
		foreach($results as $result){
		    DB::table('payment_info')->insert(
		       array('name'=>$request->get('student_name'), 'admission_name'=>'hsc_admission' , 'roll' => $class_roll, 'pro_group' => $hsc_group,'admission_session'=>$session,'slip_name'=>$result->title,'slip_type'=>$result->type,'total_amount'=>$result->fees,'status'=>'Pending','date_start'=>$result->start_date, 'date_end'=>$result->end_date, 'father_name'=>$request->get('father_name'), 'institute_code'=>$insCode)
		        ); 
				/*	DB::connection('rajbill')->table('payment_info_test')->insert(
				       array('name'=>$request->get('student_name'), 'admission_name'=>'HSC' , 'roll' => $class_roll, 'pro_group' => $hsc_group,'admission_session'=>$session,'slip_name'=>$result->title,'slip_type'=>$result->type,'total_amount'=>$result->fees,'status'=>'Pending','date_start'=>$result->start_date, 'date_end'=>$result->end_date, 'father_name'=>$request->get('father_name'), 'institute_code'=>$insCode)
				        ); 	*/				
		}
		DB::update("update id_roll set last_digit_used=last_digit_used+1 where session='$session' and dept_name='hsc_{$hsc_group}'");	
       return Redirect::route('admin.newstudent.hscnewstudent')->with('stuId', $id )->with('stuRoll', $class_roll );		

	}
	
	public function roll_generate_hsc($session,$groups){

		if($groups=='Humanities')  // in id_roll table, groups of hsc and degree are separated by prefix hsc_ and degree_ in 'dept_name' code.
		$cat="2";
		else if($groups=='Science')
		  $cat="1";
		else if($groups=='Business Studies')
		  $cat="3";

		$cat = '000';
		
		$groups='hsc_'.$groups;
	  $results= DB::select("select last_digit_used from id_roll where session='$session' and dept_name='$groups'");
	  //convert 1 as 001 for 3 digit roll
	  foreach($results as $result){ $digit=str_pad($result->last_digit_used+1,'3','0',STR_PAD_LEFT); break; }
	  $session=substr($session,0,4);
	  $class_roll=$session.$cat.$digit;
	  
	  return $class_roll; 
	}


	public function honnewstudent(){
		$title = 'Easy CollegeMate - Newstudent Information Header';
		$breadcrumb = 'admin.newstudent.index:New Student Management';
		return view('BackEnd.admin.new_student.honform', compact('title', 'breadcrumb'));
	}

	public function honSubmit(Request $request){
		$this->validate($request, [
    		'student_name' => 'required',
    		'session' => 'required',
    		'photo' => 'required|mimes:jpeg,jpg,png',
    		'father_name' => 'required',
    		'mother_name' => 'required',
    		'gender' => 'required',
    		'ssc_roll' => 'required|numeric',
    		'faculty' => 'required',
    		'subject' => 'required',
    		'current_level' => 'required',
    		'gender' => 'required',
    		'religion' => 'required',
    		'faculty' => 'required',
    		'admission_roll' => 'required|numeric'

    	]);

		$temp_entry_time = date('Y-m-d G:i:s');
		$entry_time = date('Y-m-d G:i:s', strtotime($temp_entry_time));
        $admission_roll = $request->get('admission_roll');
        $filename='';
		$logo = $request->file('photo');
		if($logo!=''){
			$folder = public_path('upload/college/honours/'.$request->session);
			create_dir($folder);
			$filename = rand(1, 99999999999) .'.jpg';
			$upload_path = $folder.'/'.$filename;
			$db_path = 'upload/college/honours/' . $filename;
			Image::make($logo->getRealPath())->save($upload_path);
		}
        $session = $request->get('session');
		$insCode = INS_CODE;
    	DB::table('hons_admitted_student')->insert(
	       array(
	            'entry_time'=>$entry_time,
	            'name'=>$request->get('student_name'),
	            'father_name'=>$request->get('father_name'),
	            'mother_name'=>$request->get('mother_name'),
	            'birth_date'=>$request->get('birth_date'),
	            'blood_group'=>$request->get('blood_group'),
	            'gender'=>$request->get('gender'),
	            'ssc_roll'=>$request->get('ssc_roll'),
	            'ssc_institute'=>$request->get('ssc_institution'),
	            'ssc_board'=>$request->get('ssc_board'),
	            'ssc_gpa'=>$request->get('ssc_gpa'),
	            'hsc_roll'=>$request->get('hsc_roll'),
	            'hsc_institute'=>$request->get('hsc_institution'),
	            'hsc_board'=> $request->get('hsc_board'),
	            'hsc_gpa'=>$request->get('hsc_gpa'),
	            'photo'=>$filename,
	            'session'=> $session ,
	            'permanent_mobile'=>$request->get('student_mobile'),
	            'ssc_pass_year'=>$request->get('ssc_passing_year'),
	            'hsc_pass_year'=>$request->get('hsc_passing_year'),
				'faculty'=>$request->get('faculty'),
				'subject'=>$request->get('subject'),
				'admission_roll'=>$request->get('admission_roll'),
				'religion'=>$request->get('religion')
				)
        );
		
	  $faculty = $request->get('faculty');
	  $prefix='honours_';
	  $catagory="4"; // for honours
	  $subject = $request->get('subject');
	  $class_roll=$this->roll_generate_honours($session,$subject,$prefix);
	  $id=$class_roll; //$this->id_generate_honours($session,$class_roll,$catagory); /*This id is the student_id*/
	  $class_roll=substr($class_roll, 4);
      $ref_id=DB::select("SELECT * from hons_admitted_student WHERE session='$session' AND admission_roll= $admission_roll");
		  $st_ref_id = $ref_id[0]->auto_id;	  
      DB::table('student_info_hons')->insert(
       array('id'=>$id, 'name'=>$request->get('student_name'), 'class_roll'=>$class_roll, 'faculty_name'=>$request->get('faculty'), 'dept_name'=>$request->get('subject'), 'current_level'=>$request->get('current_level'), 'father_name'=>$request->get('fathers_name'), 'mother_name'=>$request->get('mothers_name'), 'birth_date'=>$request->get('birth_date'), 'gender'=>$request->get('gender'), 'contact_no'=>$request->get('student_mobile'), 'religion'=>$request->get('religion'),  'image'=>$filename, 'refference_id'=>$st_ref_id, 'admission_roll'=>$admission_roll , 'session'=>$session)
        );
		$results =DB::select(" SELECT  * FROM payslipheaders 
		 INNER join 
		 payslipgenerators On payslipheaders.id = payslipgenerators.payslipheader_id
		 WHERE pro_group='Honours' and group_dept='$faculty' and type = 'formfillup'  and start_date <= CURDATE() and end_date >= CURDATE()");		
	foreach($results as $result){
			    DB::table('payment_info')->insert(
			       array('name'=>$request->get('student_name'), 'admission_name'=>'Honours' , 'roll' => $id, 'pro_group' => $request->get('faculty'),'admission_session'=> $session,'slip_name'=>$result->title,'slip_type'=>$result->type,'total_amount'=>$result->fees,'status'=>'Pending','date_start'=>$result->start_date, 'date_end'=>$result->end_date, 'father_name'=>$request->get('fathers_name'), 'institute_code'=>$insCode)
			        ); 
				// DB::connection('rajbill')->table('payment_info_test')->insert(
			 //       array('name'=>$request->get('student_name'), 'admission_name'=>'Honours' , 'roll' => $id, 'pro_group' => $request->get('faculty'),'admission_session'=> $session,'slip_name'=>$result->title,'slip_type'=>$result->type,'total_amount'=>$result->fees,'status'=>'Pending','date_start'=>$result->start_date, 'date_end'=>$result->end_date, 'father_name'=>$request->get('fathers_name'), 'institute_code'=>$insCode)
			 //        ); 					
	}		
      $date=date('Y-m-d');
      DB::update("update hons_admitted_student set payment_status='dbbl_Paid',paid_date='$date' where auto_id='$st_ref_id'");   
      DB::update("update id_roll set last_digit_used=last_digit_used+1 where session='$session' and dept_name='honours_{$subject}'");
	
		return Redirect::route('admin.newstudent.honnewstudent')->with('stuId', $id )->with('stuRoll', $class_roll );
	}

	/*public  function id_generate_honours($session,$class_roll,$catagory){
	  
	  $session=substr($session,0,4);  // take session as first year of the session(ex: 2012-2013 , session is 2012)
	  return $id=$session.$catagory.$class_roll;
	  

	}*/


  public  function roll_generate_honours($session,$subject,$prefix){

      $id_table_subject=$prefix.$subject;
      
      //echo $id_table_subject;
      
      
	 $results= DB::select("select last_digit_used from id_roll where session='$session' and dept_name='$id_table_subject'");
	  //convert 1 as 001 for 3 digit roll
	  foreach($results as $result){ $digit=str_pad($result->last_digit_used+1,'3','0',STR_PAD_LEFT); break; }
	    
	  $results= DB::select("select dept_code from departments where dept_name='$subject'");
	  foreach($results as $result){ $dept_code=$result->dept_code; break; }

	  $session=substr($session,0,4); 
	  //$dept_code=substr($dept_code,0,2); // take first two digit of the department code
	  
	  $class_roll=$session.'2'.$dept_code.$digit;

	  
	  return $class_roll;
    
  }	
  
  public function degnewstudent(){
		$title = 'Easy CollegeMate - Newstudent Information Header';
		$breadcrumb = 'admin.newstudent.index:New Student Management';
		return view('BackEnd.admin.new_student.degform', compact('title', 'breadcrumb'));	  
  }
  
  public function degSubmit(Request $request){
  	$this->validate($request, [
    		'student_name' => 'required',
    		'session' => 'required',
    		'photo' => 'required|mimes:jpeg,jpg,png',
    		'father_name' => 'required',
    		'mother_name' => 'required',
    		'gender' => 'required',
    		'ssc_roll' => 'required|numeric',
    		'faculty' => 'required',
    		'subject' => 'required',
    		'current_level' => 'required',
    		'gender' => 'required',
    		'religion' => 'required',
    		'faculty' => 'required',
    		'admission_roll' => 'required|numeric'

    	]);

  	$temp_entry_time = date('Y-m-d G:i:s');
  	$entry_time = date('Y-m-d G:i:s', strtotime($temp_entry_time));
  	$filename ='';
	if($request->file('photo')!= ""){
		$logo = $request->file('photo');
		$folder = public_path('upload/college/degree/'.$request->session);
		create_dir($folder);
		$filename = rand(1, 99999999999) .'.jpg';
		$upload_path = $folder.'/'.$filename;
		$db_path = $upload_path;

		Image::make($logo->getRealPath())->save($upload_path);
	}
    $insCode = INS_CODE;
	$admission_roll = $request->get('admission_roll');
	$session = $request->get('session');	
    $subject = $request->get('subject');
    DB::table('deg_admitted_student')->insert(
       array(
            'session'=>$request->get('session'),
			'admission_roll'=>$request->get('admission_roll'),
			'entry_time'=>$entry_time,
			'name'=>$request->get('student_name'),		
			'faculty'=>$request->get('faculty'),
			'subject'=>$request->get('subject'),
			'father_name'=>$request->get('father_name'),
			'mother_name'=>$request->get('mother_name'),
			'birth_date'=>$request->get('birth_date'),
			'gender'=>$request->get('gender'),
			'contact_no'=>$request->get('student_mobile'),
			'photo'=>$filename,
			'religion'=>$request->get('religion'),
			'ssc_institute'=>$request->get('ssc_institution'),
			'ssc_roll'=>$request->get('ssc_roll'),
			'ssc_pass_year'=>$request->get('ssc_passing_year'),
			'ssc_gpa'=>$request->get('ssc_gpa'),
			'ssc_board'=>$request->get('ssc_board'),
			'hsc_institute'=>$request->get('hsc_institution'),
			'hsc_roll'=>$request->get('hsc_roll'),
			'hsc_pass_year'=>$request->get('hsc_passing_year'),
			'hsc_gpa'=>$request->get('hsc_gpa'),
			'hsc_board'=>$request->get('hsc_board'),
			'blood_group'=>$request->get('blood_group')
			)
        );	
		
		  $prefix='degree_';
		  $catagory="1"; // for degree
		  $class_roll= $this->roll_generate_degree($session,$subject);
		  $id=$class_roll; //$this->id_generate_hsc($session,$class_roll,$catagory);
          $ref_id=DB::select("SELECT * from deg_admitted_student WHERE session='$session' AND admission_roll= $admission_roll");
		  $st_ref_id = $ref_id[0]->auto_id;	 		  
		  DB::table('student_info_degree')->insert(
			   array('id'=>$id, 'name'=>$request->get('student_name'), 'class_roll'=>$class_roll , 'session' =>$session, 'groups'=>$subject, 'current_level' =>$request->get('current_level'),'father_name'=> $request->get('father_name'),'mother_name'=>$request->get('mother_name'),'birth_date'=>$request->get('birth_date') ,'gender'=>$request->get('gender') ,'contact_no'=> $request->get('student_mobile'),'religion'=>$request->get('religion'),'blood_group'=>$request->get('blood_group'),'image'=>$filename ,'refference_id'=> $st_ref_id,'admission_roll'=> $admission_roll)
				);
		$results =DB::select("  SELECT  * ,payslipheaders.id as sliptypeid FROM payslipheaders 
		 INNER join 
		 payslipgenerators On payslipheaders.id = payslipgenerators.payslipheader_id
		 WHERE pro_group='Degree'  and type = 'formfillup'  and start_date <= CURDATE() and end_date >= CURDATE()");		
		foreach($results as $result){
					DB::table('payment_info')->insert(
					   array('name'=>$request->get('student_name'), 'admission_name'=>'Degree' , 'roll' =>$id, 'pro_group' => $subject,'admission_session'=> $session,'slip_name'=>$result->title,'slip_type'=>$result->sliptypeid,'total_amount'=>$result->fees,'status'=>'Pending','date_start'=>$result->start_date, 'date_end'=>$result->end_date, 'father_name'=>$request->get('father_name'), 'institute_code'=>$insCode)
						); 
					// DB::connection('rajbill')->table('payment_info')->insert(
					//    array('name'=>$request->get('student_name'), 'admission_name'=>'Degree' , 'roll' =>$id, 'pro_group' => $subject,'admission_session'=> $session,'slip_name'=>$result->title,'slip_type'=>$result->sliptypeid,'total_amount'=>$result->fees,'status'=>'Pending','date_start'=>$result->start_date, 'date_end'=>$result->end_date, 'father_name'=>$request->get('father_name'), 'institute_code'=>$insCode)
					// 	); 			
		}
			DB::update("update id_roll set last_digit_used=last_digit_used+1 where session='$session' and dept_name='degree_{$subject}'");	
			$date=date('Y-m-d');
			DB::update("update deg_admitted_student set payment_status='dbbl',paid_date='$date' where auto_id='$st_ref_id'");
			
			return Redirect::route('admin.newstudent.degnewstudent')->with('stuId', $id )->with('stuRoll', $class_roll );
	}
  
	 public function roll_generate_degree($session,$groups){
	   if($groups=='B.A')  // in id_roll table, groups of hsc and degree are separated by prefix hsc_ and degree_ in 'dept_name' code.
		$cat="1";
		else if($groups=='B.S.S')
		  $cat="2";
		else if($groups=='B.S.C')
		  $cat="3";
		else if($groups=='B.B.S')
		  $cat="4";
		$groups='degree_'.$groups;
		
	  $results= DB::select("select last_digit_used from id_roll where session='$session' and dept_name='$groups'");
	  //convert 1 as 001 for 3 digit roll
	  foreach($results as $result){ $digit=str_pad($result->last_digit_used+1,'3','0',STR_PAD_LEFT); break; }
	  $session=substr($session,0,4);
	  $class_roll=$session.$cat.$digit;
	  
	  return $class_roll; 
	}
}
