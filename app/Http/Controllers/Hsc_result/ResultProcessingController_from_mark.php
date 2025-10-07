<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\ClassTestExam;
use App\Models\ClassTestMark;
use App\Models\Classe;
use App\Models\ClasseTestExam;
use App\Models\ConfigExamParticle;
use App\Models\Exam;
use App\Models\Group;
use App\Models\HscGpa;
use App\Models\HscRsltProcessing;
use App\Models\Mark;
use App\Models\StudentInfoHsc;
use App\Models\StudentSubInfo;
use App\Models\StudentSubMarkGp;
use App\Models\SubjectPartical;
use DB;
use Ecm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Mpdf\Mpdf;
use Session;

class ResultProcessingController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'hsc_result.process.index:Result Process|Dashboard';		
		$processed_result=HscRsltProcessing::orderBy('id')->paginate(20);

		return view('BackEnd.hsc_result.process.index', compact('title', 'processed_result','breadcrumb'));					

	}
	
	public function MeritListPdf($id)
	{
 
        $mpdf = new Mpdf(['format'=> 'A4-L']);		
		$mpdf->allow_charset_conversion=true;
		$mpdf->charset_in='UTF-8';
		$mpdf->WriteHTML(view('BackEnd.hsc_result.pdf.meritlist')->withId($id));
		$mpdf->Output();				
	}
	
	public function TabulationPdf($id, $ex_id)
	{
	set_time_limit(0);
			$exam=Exam::find($ex_id);
			if($exam->have_class_test >=0) 
				{
					$class_tests = DB::select("select * from class_test_assign where exam_id = $ex_id" );
					$mpdf = new Mpdf(['mode'=>'c', 'format'=> 'A4-L']);		
					$mpdf->allow_charset_conversion=true;
					$mpdf->charset_in='UTF-8';	    
					$mpdf->WriteHTML(view('BackEnd.hsc_result.pdf.classtest_tabulation')->withId($id)->with('class_tests',$class_tests));
					$mpdf->Output();
				}
				
				else{
					
					$mpdf = new Mpdf(['mode'=>'c', 'format'=> 'A4-L']);		
					$mpdf->allow_charset_conversion=true;
					$mpdf->charset_in='UTF-8';	    
					$mpdf->WriteHTML(view('BackEnd.hsc_result.pdf.tabulation')->withId($id));
					$mpdf->Output();					
				}
									
	}

	

	public function create() {

		$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'hsc_result.process.index:Result Process|Dashboard';		
		$classes = Classe::orderBy('id')->paginate(Ecm::paginate());
		$current_yr_lists = create_option_array('classes', 'id', 'name', 'Current Year');
		$group_lists = $group_lists = create_option_array('groups', 'id', 'name', 'Group');;
		$exam_lists = ['' => 'Select exam'];
		$subject_lists = ['' => 'Select Subject'] ;

		return view('BackEnd.hsc_result.process.create', compact('title', 'current_yr_lists','group_lists','exam_lists','subject_lists','breadcrumb'));	

	}

	public function store(Request $request) {
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 9600);
		ini_set('request_terminate_timeout' , 9600);
		ini_set('fastcgi_read_timeout' , 9600);

		if ($request->isMethod('post'))
		{

			$this->validate($request, [
				'session' => 'required',
				'exam_year' => 'required',
				'current_level' => 'required',
				'group_id' => 'required',
				'exam_id' => 'required',
			],[
				'exam_id.required' => 'This exam field is required.',
				'group_id.required' => 'This group field is required.',
			]);


		    $session = $request->get('session');		
			$group_id =  $request->get('group_id');
			$current_level = $request->get('current_level');
			$exam_id = $request->get('exam_id');
			$exam_year = $request->get('exam_year');

			$have_class_test  = Exam::where('id',$exam_id)->pluck('have_class_test')->first();
			
			if($have_class_test == 1)
			{
			$curr_level=Classe::find($current_level);
			$group_name=Group::find($group_id);	
			$exam_name=Exam::find($exam_id);
			$student_infos = StudentInfoHsc::where('session',$session)->where('current_level',$curr_level->name)->where('groups',$group_name->name)->orderBy('id')->get();
			
			if($student_infos->count()==0):
				$error_message =$session.' '.$curr_level->name.' '.$group_name->name.' Have No Student!';
				return Redirect::back()->withInput()->with('error',$error_message);
			endif;
		    foreach($student_infos as $student_info){

				$student_sub=StudentSubInfo::whereStudent_id($student_info->id)->whereCurrent_level($student_info->current_level)->get();
				
				if($student_sub[0]->sub1_id!=0)
				{					
					
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					
					$subject_particals = SubjectPartical::where('classe_id',$current_level)->where('group_id',$group_id)->where('subject_id',$student_sub[0]->sub1_id)->get();
					$sub_total_mark=0;
					$sub_total_convert =100;
					foreach ($subject_particals as  $subject_partical) {
						$sub_total_mark=$subject_partical->total;
						$sub_total_convert = $subject_partical->total_converted;
					}
					
					$class_tests = ClasseTestExam::where('exam_id',$exam_id)->get();
					foreach ($class_tests as  $class_test) {					
					$marks=ClassTestMark::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub1_id)->whereClass_test_id($class_test->class_test_id)->get();
					foreach ($marks as  $mark) {
						if(is_numeric($mark->mark)){
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub1_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert){
								$sub1_fail=1;
							}				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;	
							
							}	    	
					    else{
					    	$ab_sub1=1;	
							}							
						}

					 }
						
                    $subject_mark= (($total_obt_sub1/$subject_total)*$sub_total_convert);
					if($ab_sub1==0){
						if($sub1_fail!=1){
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub1_id;
							$insert->total_mark=$subject_mark;
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
							}
						else{
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub1_id;
							$insert->total_mark=$subject_mark;
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
							}
					}
					
				   else{
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub1_id;
						$insert->total_mark=$subject_mark;
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   } 
				}
				
				if($student_sub[0]->sub2_id!=0)
				{					
					
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					
					$subject_particals = SubjectPartical::where('classe_id',$current_level)->where('group_id',$group_id)->where('subject_id',$student_sub[0]->sub2_id)->get();
					$sub_total_mark=0;
					$sub_total_convert =100;
					foreach ($subject_particals as  $subject_partical) {
						$sub_total_mark=$subject_partical->total;
						$sub_total_convert = $subject_partical->total_converted;
					}
					
					$class_tests = ClasseTestExam::where('exam_id',$exam_id)->get();
					foreach ($class_tests as  $class_test) {					
					$marks=ClassTestMark::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub2_id)->whereClass_test_id($class_test->class_test_id)->get();
					foreach ($marks as  $mark) {
						if(is_numeric($mark->mark)){
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub2_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert){
								$sub1_fail=1;
							}				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;	
							
							}	    	
					    else{
					    	$ab_sub1=1;	
							}							
						}

					 }
						
                    $subject_mark= (($total_obt_sub1/$subject_total)*$sub_total_convert);
					if($ab_sub1==0){
						if($sub1_fail!=1){
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub2_id;
							$insert->total_mark=$subject_mark;
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
							}
						else{
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub2_id;
							$insert->total_mark=$subject_mark;
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
							}
					}
					
				   else{
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub2_id;
						$insert->total_mark=$subject_mark;
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   } 
				}

				if($student_sub[0]->sub3_id!=0)
				{					
					
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					
					$subject_particals = SubjectPartical::where('classe_id',$current_level)->where('group_id',$group_id)->where('subject_id',$student_sub[0]->sub3_id)->get();
					$sub_total_mark=0;
					$sub_total_convert =100;
					foreach ($subject_particals as  $subject_partical) {
						$sub_total_mark=$subject_partical->total;
						$sub_total_convert = $subject_partical->total_converted;
					}
					
					$class_tests = ClasseTestExam::where('exam_id',$exam_id)->get();
					foreach ($class_tests as  $class_test) {					
					$marks=ClassTestMark::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub3_id)->whereClass_test_id($class_test->class_test_id)->get();
					foreach ($marks as  $mark) {
						if(is_numeric($mark->mark)){
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub3_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert){
								$sub1_fail=1;
							}				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;	
							
							}	    	
					    else{
					    	$ab_sub1=1;	
							}							
						}

					 }
						
                    $subject_mark= (($total_obt_sub1/$subject_total)*$sub_total_convert);
					if($ab_sub1==0){
						if($sub1_fail!=1){
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub3_id;
							$insert->total_mark=$subject_mark;
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
							}
						else{
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub3_id;
							$insert->total_mark=$subject_mark;
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
							}
					}
					
				   else{
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub3_id;
						$insert->total_mark=$subject_mark;
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   } 
				}
				   
				if($student_sub[0]->sub4_id!=0)
				{					
					
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					
					$subject_particals = SubjectPartical::where('classe_id',$current_level)->where('group_id',$group_id)->where('subject_id',$student_sub[0]->sub4_id)->get();
					$sub_total_mark=0;
					$sub_total_convert =100;
					foreach ($subject_particals as  $subject_partical) {
						$sub_total_mark=$subject_partical->total;
						$sub_total_convert = $subject_partical->total_converted;
					}
					
					$class_tests = ClasseTestExam::where('exam_id',$exam_id)->get();
					foreach ($class_tests as  $class_test) {					
					$marks=ClassTestMark::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub4_id)->whereClass_test_id($class_test->class_test_id)->get();
					foreach ($marks as  $mark) {
						if(is_numeric($mark->mark)){
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub4_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert){
								$sub1_fail=1;
							}				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;	
							
							}	    	
					    else{
					    	$ab_sub1=1;	
							}							
						}

					 }
						
                    $subject_mark= (($total_obt_sub1/$subject_total)*$sub_total_convert);
					if($ab_sub1==0){
						if($sub1_fail!=1){
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub4_id;
							$insert->total_mark=$subject_mark;
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
							}
						else{
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub4_id;
							$insert->total_mark=$subject_mark;
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
							}
					}
					
				   else{
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub4_id;
						$insert->total_mark=$subject_mark;
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   } 
				}
				   
				if($student_sub[0]->sub5_id!=0)
				{					
					
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					
					$subject_particals = SubjectPartical::where('classe_id',$current_level)->where('group_id',$group_id)->where('subject_id',$student_sub[0]->sub5_id)->get();
					$sub_total_mark=0;
					$sub_total_convert =100;
					foreach ($subject_particals as  $subject_partical) {
						$sub_total_mark=$subject_partical->total;
						$sub_total_convert = $subject_partical->total_converted;
					}
					
					$class_tests = ClasseTestExam::where('exam_id',$exam_id)->get();
					foreach ($class_tests as  $class_test) {					
					$marks=ClassTestMark::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub5_id)->whereClass_test_id($class_test->class_test_id)->get();
					foreach ($marks as  $mark) {
						if(is_numeric($mark->mark)){
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub5_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert){
								$sub1_fail=1;
							}				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;	
							
							}	    	
					    else{
					    	$ab_sub1=1;	
							}							
						}

					 }
						
                    $subject_mark= (($total_obt_sub1/$subject_total)*$sub_total_convert);
					if($ab_sub1==0){
						if($sub1_fail!=1){
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub5_id;
							$insert->total_mark=$subject_mark;
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
							}
						else{
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub5_id;
							$insert->total_mark=$subject_mark;
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
							}
					}
					
				   else{
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub5_id;
						$insert->total_mark=$subject_mark;
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   } 
				}
				   
				if($student_sub[0]->sub6_id!=0)
				{					
					
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					
					$subject_particals = SubjectPartical::where('classe_id',$current_level)->where('group_id',$group_id)->where('subject_id',$student_sub[0]->sub6_id)->get();
					$sub_total_mark=0;
					$sub_total_convert =100;
					foreach ($subject_particals as  $subject_partical) {
						$sub_total_mark=$subject_partical->total;
						$sub_total_convert = $subject_partical->total_converted;
					}
					
					$class_tests = ClasseTestExam::where('exam_id',$exam_id)->get();
					foreach ($class_tests as  $class_test) {					
					$marks=ClassTestMark::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub6_id)->whereClass_test_id($class_test->class_test_id)->get();
					foreach ($marks as  $mark) {
						if(is_numeric($mark->mark)){
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub6_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert){
								$sub1_fail=1;
							}				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;	
							
							}	    	
					    else{
					    	$ab_sub1=1;	
							}							
						}

					 }
						
                    $subject_mark= (($total_obt_sub1/$subject_total)*$sub_total_convert);
					if($ab_sub1==0){
						if($sub1_fail!=1){
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub6_id;
							$insert->total_mark=$subject_mark;
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
							}
						else{
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub6_id;
							$insert->total_mark=$subject_mark;
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
							}
					}
					
				   else{
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub6_id;
						$insert->total_mark=$subject_mark;
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   } 
				}
				if($student_sub[0]->sub21_id!=0)
				{					
					
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					
					$subject_particals = SubjectPartical::where('classe_id',$current_level)->where('group_id',$group_id)->where('subject_id',$student_sub[0]->sub21_id)->get();
					$sub_total_mark=0;
					$sub_total_convert =100;
					foreach ($subject_particals as  $subject_partical) {
						$sub_total_mark=$subject_partical->total;
						$sub_total_convert = $subject_partical->total_converted;
					}
					
					$class_tests = ClasseTestExam::where('exam_id',$exam_id)->get();
					foreach ($class_tests as  $class_test) {					
					$marks=ClassTestMark::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub21_id)->whereClass_test_id($class_test->class_test_id)->get();
					foreach ($marks as  $mark) {
						if(is_numeric($mark->mark)){
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub21_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert){
								$sub1_fail=1;
							}				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;	
							
							}	    	
					    else{
					    	$ab_sub1=1;	
							}							
						}

					 }
						
                    $subject_mark= (($total_obt_sub1/$subject_total)*$sub_total_convert);
					if($ab_sub1==0){
						if($sub1_fail!=1){
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub21_id;
							$insert->total_mark=$subject_mark;
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
							}
						else{
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub21_id;
							$insert->total_mark=$subject_mark;
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
							}
					}
					
				   else{
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub21_id;
						$insert->total_mark=$subject_mark;
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   } 
				}				   
				if($student_sub[0]->sub22_id!=0)
				{					
					
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					
					$subject_particals = SubjectPartical::where('classe_id',$current_level)->where('group_id',$group_id)->where('subject_id',$student_sub[0]->sub22_id)->get();
					$sub_total_mark=0;
					$sub_total_convert =100;
					foreach ($subject_particals as  $subject_partical) {
						$sub_total_mark=$subject_partical->total;
						$sub_total_convert = $subject_partical->total_converted;
					}
					
					$class_tests = ClasseTestExam::where('exam_id',$exam_id)->get();
					foreach ($class_tests as  $class_test) {					
					$marks=ClassTestMark::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub22_id)->whereClass_test_id($class_test->class_test_id)->get();
					foreach ($marks as  $mark) {
						if(is_numeric($mark->mark)){
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub22_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert){
								$sub1_fail=1;
							}				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;	
							
							}	    	
					    else{
					    	$ab_sub1=1;	
							}							
						}

					 }
						
                    $subject_mark= (($total_obt_sub1/$subject_total)*$sub_total_convert);
					if($ab_sub1==0){
						if($sub1_fail!=1){
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub22_id;
							$insert->total_mark=$subject_mark;
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
							}
						else{
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub22_id;
							$insert->total_mark=$subject_mark;
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
							}
					}
					
				   else{
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub22_id;
						$insert->total_mark=$subject_mark;
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   } 
				}		
				if($student_sub[0]->sub23_id!=0)
				{					
					
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					
					$subject_particals = SubjectPartical::where('classe_id',$current_level)->where('group_id',$group_id)->where('subject_id',$student_sub[0]->sub23_id)->get();
					$sub_total_mark=0;
					$sub_total_convert =100;
					foreach ($subject_particals as  $subject_partical) {
						$sub_total_mark=$subject_partical->total;
						$sub_total_convert = $subject_partical->total_converted;
					}
					
					$class_tests = ClasseTestExam::where('exam_id',$exam_id)->get();
					foreach ($class_tests as  $class_test) {					
					$marks=ClassTestMark::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub23_id)->whereClass_test_id($class_test->class_test_id)->get();
					foreach ($marks as  $mark) {
						if(is_numeric($mark->mark)){
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub23_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert){
								$sub1_fail=1;
							}				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;	
							
							}	    	
					    else{
					    	$ab_sub1=1;	
							}							
						}

					 }
						
                    $subject_mark= (($total_obt_sub1/$subject_total)*$sub_total_convert);
					if($ab_sub1==0){
						if($sub1_fail!=1){
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub23_id;
							$insert->total_mark=$subject_mark;
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
							}
						else{
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub23_id;
							$insert->total_mark=$subject_mark;
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
							}
					}
					
				   else{
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub23_id;
						$insert->total_mark=$subject_mark;
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   } 
				}
				if($student_sub[0]->sub24_id!=0)
				{					
					
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					
					$subject_particals = SubjectPartical::where('classe_id',$current_level)->where('group_id',$group_id)->where('subject_id',$student_sub[0]->sub24_id)->get();
					$sub_total_mark=0;
					$sub_total_convert =100;
					foreach ($subject_particals as  $subject_partical) {
						$sub_total_mark=$subject_partical->total;
						$sub_total_convert = $subject_partical->total_converted;
					}
					
					$class_tests = ClasseTestExam::where('exam_id',$exam_id)->get();
					foreach ($class_tests as  $class_test) {					
					$marks=ClassTestMark::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub24_id)->whereClass_test_id($class_test->class_test_id)->get();
					foreach ($marks as  $mark) {
						if(is_numeric($mark->mark)){
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub24_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert){
								$sub1_fail=1;
							}				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;	
							
							}	    	
					    else{
					    	$ab_sub1=1;	
							}							
						}

					 }
						
                    $subject_mark= (($total_obt_sub1/$subject_total)*$sub_total_convert);
					if($ab_sub1==0){
						if($sub1_fail!=1){
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub24_id;
							$insert->total_mark=$subject_mark;
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
							}
						else{
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub24_id;
							$insert->total_mark=$subject_mark;
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
							}
					}
					
				   else{
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub24_id;
						$insert->total_mark=$subject_mark;
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   } 
				}
				
				if($student_sub[0]->sub25_id!=0)
				{					
					
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					
					$subject_particals = SubjectPartical::where('classe_id',$current_level)->where('group_id',$group_id)->where('subject_id',$student_sub[0]->sub25_id)->get();
					$sub_total_mark=0;
					$sub_total_convert =100;
					foreach ($subject_particals as  $subject_partical) {
						$sub_total_mark=$subject_partical->total;
						$sub_total_convert = $subject_partical->total_converted;
					}
					
					$class_tests = ClasseTestExam::where('exam_id',$exam_id)->get();
					foreach ($class_tests as  $class_test) {					
					$marks=ClassTestMark::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub25_id)->whereClass_test_id($class_test->class_test_id)->get();
					foreach ($marks as  $mark) {
						if(is_numeric($mark->mark)){
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub25_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert){
								$sub1_fail=1;
							}				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;	
							
							}	    	
					    else{
					    	$ab_sub1=1;	
							}							
						}

					 }
						
                    $subject_mark= (($total_obt_sub1/$subject_total)*$sub_total_convert);
					if($ab_sub1==0){
						if($sub1_fail!=1){
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub25_id;
							$insert->total_mark=$subject_mark;
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
							}
						else{
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub25_id;
							$insert->total_mark=$subject_mark;
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
							}
					}
					
				   else{
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub25_id;
						$insert->total_mark=$subject_mark;
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   } 
				}				


				if($student_sub[0]->sub26_id!=0)
				{					
					
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					
					$subject_particals = SubjectPartical::where('classe_id',$current_level)->where('group_id',$group_id)->where('subject_id',$student_sub[0]->sub26_id)->get();
					$sub_total_mark=0;
					$sub_total_convert =100;
					foreach ($subject_particals as  $subject_partical) {
						$sub_total_mark=$subject_partical->total;
						$sub_total_convert = $subject_partical->total_converted;
					}
					
					$class_tests = ClasseTestExam::where('exam_id',$exam_id)->get();
					foreach ($class_tests as  $class_test) {					
					$marks=ClassTestMark::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub26_id)->whereClass_test_id($class_test->class_test_id)->get();
					foreach ($marks as  $mark) {
						if(is_numeric($mark->mark)){
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub26_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert){
								$sub1_fail=1;
							}				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;	
							
							}	    	
					    else{
					    	$ab_sub1=1;	
							}							
						}

					 }
						
                    $subject_mark= (($total_obt_sub1/$subject_total)*$sub_total_convert);
					if($ab_sub1==0){
						if($sub1_fail!=1){
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub26_id;
							$insert->total_mark=$subject_mark;
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
							}
						else{
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub26_id;
							$insert->total_mark=$subject_mark;
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
							}
					}
					
				   else{
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub26_id;
						$insert->total_mark=$subject_mark;
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   } 
				}
				if($student_sub[0]->fourth_id!=0)
				{					
					
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					
					$subject_particals = SubjectPartical::where('classe_id',$current_level)->where('group_id',$group_id)->where('subject_id',$student_sub[0]->fourth_id)->get();
					$sub_total_mark=0;
					$sub_total_convert =100;
					foreach ($subject_particals as  $subject_partical) {
						$sub_total_mark=$subject_partical->total;
						$sub_total_convert = $subject_partical->total_converted;
					}
					
					$class_tests = ClasseTestExam::where('exam_id',$exam_id)->get();
					foreach ($class_tests as  $class_test) {					
					$marks=ClassTestMark::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->fourth_id)->whereClass_test_id($class_test->class_test_id)->get();
					foreach ($marks as  $mark) {
						if(is_numeric($mark->mark)){
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->fourth_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert){
								$sub1_fail=1;
							}				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;	
							
							}	    	
					    else{
					    	$ab_sub1=1;	
							}							
						}

					 }
						
                    $subject_mark= (($total_obt_sub1/$subject_total)*$sub_total_convert);
					if($ab_sub1==0){
						if($sub1_fail!=1){
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->fourth_id;
							$insert->total_mark=$subject_mark;
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=1;
							$insert->save();
							}
						else{
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->fourth_id;
							$insert->total_mark=$subject_mark;
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=1;
							$insert->save();
							}
					}
					
				   else{
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->fourth_id;
						$insert->total_mark=$subject_mark;
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=1;
						$insert->absent=1;
						$insert->save();
				   } 
				}				
				if($student_sub[0]->fourth2_id!=0)
				{					
					
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					
					$subject_particals = SubjectPartical::where('classe_id',$current_level)->where('group_id',$group_id)->where('subject_id',$student_sub[0]->fourth2_id)->get();
					$sub_total_mark=0;
					$sub_total_convert =100;
					foreach ($subject_particals as  $subject_partical) {
						$sub_total_mark=$subject_partical->total;
						$sub_total_convert = $subject_partical->total_converted;
					}
					
					$class_tests = ClasseTestExam::where('exam_id',$exam_id)->get();
					foreach ($class_tests as  $class_test) {					
					$marks=ClassTestMark::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->fourth2_id)->whereClass_test_id($class_test->class_test_id)->get();
					foreach ($marks as  $mark) {
						if(is_numeric($mark->mark)){
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->fourth2_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert){
								$sub1_fail=1;
							}				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;	
							
							}	    	
					    else{
					    	$ab_sub1=1;	
							}							
						}

					 }
						
                    $subject_mark= (($total_obt_sub1/$subject_total)*$sub_total_convert);
					if($ab_sub1==0){
						if($sub1_fail!=1){
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->fourth2_id;
							$insert->total_mark=$subject_mark;
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=1;
							$insert->save();
							}
						else{
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->fourth2_id;
							$insert->total_mark=$subject_mark;
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=1;
							$insert->save();
							}
					}
					
				   else{
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->fourth2_id;
						$insert->total_mark=$subject_mark;
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=1;
						$insert->absent=1;
						$insert->save();
				   } 
				}


$all_sub_mark=StudentSubMarkGp::whereStudent_id($student_info->id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->get();
				
				$cgpa=0;
				$fail=0;	
				$without_4th=0;
				$grand_cgpa=0;
				$forth_count = 0;
				foreach ($all_sub_mark as  $all) {
					if($all->fourth!=1){
						
						$cgpa+=$all->point;
						$without_4th+=$all->point;
						if($all->point==0){
							$fail=1;
						
						}
					}						
					else{
					    $forth_count++;
						if($all->point>2){
							$cgpa+=$all->point-2;
						}
					}
				}
				
				if($fail!=1){
					$no_sub=$all_sub_mark->count()-$forth_count;
					if($cgpa>($no_sub*5)){
						$grand_cgpa=5;
						}
				        else{
					$grand_cgpa=$cgpa/$no_sub;
				        }
					
					$without_4th_grand_cgpa=$without_4th/$no_sub;
					$grade=Ecm::grade($grand_cgpa);
					//$without_4th_cgpa=Ecm::grade($without_4th_grand_cgpa);
					$insert_gpa=new HscGpa;
					$insert_gpa->student_id=$student_info->id;
					$insert_gpa->session=$session;
					$insert_gpa->group_id=$group_id;
					$insert_gpa->exam_id=$exam_id;
					$insert_gpa->cgpa=$grand_cgpa;
					$insert_gpa->without_4th=$without_4th_grand_cgpa;					
					$insert_gpa->grade=$grade;
					$insert_gpa->save();
					}
				else{
					$insert_gpa=new HscGpa;
					$insert_gpa->student_id=$student_info->id;
					$insert_gpa->session=$session;
					$insert_gpa->group_id=$group_id;
					$insert_gpa->exam_id=$exam_id;
					$insert_gpa->cgpa=0;
					$insert_gpa->without_4th=0;					
					$insert_gpa->grade='F';
					$insert_gpa->save();
				}
 				
			}
				
		   

          		   
			}
			else
			{
				
			if($session == '') :
			$error_message = 'Select Session';
			return Redirect::back()->withInput()->with('error',$error_message);
			endif;

			if($exam_year == '') :
			$error_message = 'Select Exam Year';
			return Redirect::back()->withInput()->with('error',$error_message);
			endif;			
			if($current_level == '') :
				$error_message = 'Select Current Year';
				return Redirect::back()->withInput()->with('error',$error_message);
			endif;
			if($group_id == '') :
				$error_message = 'Select Group';
				return Redirect::back()->withInput()->with('error',$error_message);
			endif;
			if($exam_id == '') :
				$error_message = 'Select Exam';
				return Redirect::back()->withInput()->with('error',$error_message);
			endif;
			
			$curr_level=Classe::find($current_level);
			$group_name=Group::find($group_id);	
			$exam_name=Exam::find($exam_id);


			$check=HscRsltProcessing::whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->get();
			if($check->count()>0):
				$error_message =$check[0]->exam_year.' '.$check[0]->group->name.' '.$check[0]->exam->name.' Already Generated';
				return Redirect::back()->withInput()->with('error',$error_message);
			endif;
			$student_infos = StudentInfoHsc::whereCurrent_level($curr_level->name)->whereGroups($group_name->name)->orderBy('id')->get();
			
			if($student_infos->count()==0):
				$error_message =$exam_year.' '.$curr_level->name.' '.$group_name->name.' Have No Student!';
				return Redirect::back()->withInput()->with('error',$error_message);
			endif;
			
			
			//Checking Mark Input
			//******* It Should be on********
			foreach($student_infos as $student_info) :
try{
				$student_sub=StudentSubInfo::where('session',$session)->whereStudent_id($student_info->id)->whereCurrent_level($student_info->current_level)->get();

				if(count($student_sub) < 1) continue;
				
				
				//Sub 1 mark check
				if($student_sub[0]->sub1_id!=0):
					$exam_particle_count = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub1_id)
										->count();
					$mark_check=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub1_id)->count();
					if($exam_particle_count!=$mark_check):
						$error_message ='Class Roll '.$student_info->id.' have no marks in '.$student_sub[0]->sub1->name.'('.$student_sub[0]->sub1->code.')';
						return Redirect::back()->withInput()->with('error',$error_message);
					endif;
			    endif;
				//end

				//Sub 2 mark check
				if($student_sub[0]->sub2_id!=0):
					$exam_particle_count = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub2_id)
										->count();
					$mark_check=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub2_id)->count();
					if($exam_particle_count!=$mark_check):
						$error_message ='Class Roll '.$student_info->id.' have no marks in '.$student_sub[0]->sub2->name.'('.$student_sub[0]->sub2->code.')';
						return Redirect::back()->withInput()->with('error',$error_message);
					endif;
				endif;
				//end

				//Sub 3 mark check
				if($student_sub[0]->sub3_id!=0):
					$exam_particle_count = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub3_id)
										->count();
					$mark_check=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub3_id)->count();
					if($exam_particle_count!=$mark_check):
						$error_message ='Class Roll '.$student_info->id.' have no marks in '.$student_sub[0]->sub3->name.'('.$student_sub[0]->sub3->code.')';
						return Redirect::back()->withInput()->with('error',$error_message);
					endif;
				endif;
				//end

				//Sub 4 mark check
				if($student_sub[0]->sub4_id!=0):
					$exam_particle_count = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub4_id)
										->count();
					$mark_check=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub4_id)->count();
					if($exam_particle_count!=$mark_check):
						$error_message ='Class Roll '.$student_info->id.' have no marks in '.$student_sub[0]->sub4->name.'('.$student_sub[0]->sub4->code.')';
						return Redirect::back()->withInput()->with('error',$error_message);
					endif;
				endif;
				//end

				//Sub 5 mark check
				if($student_sub[0]->sub5_id!=0):
					$exam_particle_count = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub5_id)
										->count();
					$mark_check=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub5_id)->count();
					if($exam_particle_count!=$mark_check):
						$error_message ='Class Roll '.$student_info->id.' have no marks in '.$student_sub[0]->sub5->name.'('.$student_sub[0]->sub5->code.')';
						return Redirect::back()->withInput()->with('error',$error_message);
					endif;
				endif;
				//end	

				//Sub 6 mark check
				if($student_sub[0]->sub6_id!=0):
					$exam_particle_count = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub6_id)
										->count();
					$mark_check=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub6_id)->count();
					if($exam_particle_count!=$mark_check):
						$error_message ='Class Roll '.$student_info->id.' have no marks in '.$student_sub[0]->sub6->name.'('.$student_sub[0]->sub6->code.')';
						return Redirect::back()->withInput()->with('error',$error_message);
					endif;
				endif;
				//end	

				//Sub fourth mark check
				if($student_sub[0]->fourth_id!=0):
					$exam_particle_count = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->fourth_id)
										->count();
					$mark_check=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->fourth_id)->count();
					if($exam_particle_count!=$mark_check):
						$error_message ='Class Roll '.$student_info->id.' have no marks in '.$student_sub[0]->fourth->name.'('.$student_sub[0]->fourth->code.')';
						return Redirect::back()->withInput()->with('error',$error_message);
					endif;
				endif;
				//end		
				
				
				//Sub 7 mark check
				if($student_sub[0]->sub21_id!=0):
					$exam_particle_count = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub21_id)
										->count();
					$mark_check=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub21_id)->count();
					if($exam_particle_count!=$mark_check):
						$error_message ='Class Roll '.$student_info->id.' have no marks in '.$student_sub[0]->sub7->name.'('.$student_sub[0]->sub7->code.')';
						return Redirect::back()->withInput()->with('error',$error_message);
					endif;
				endif;
				//end				
				
				//Sub 8 mark check
				if($student_sub[0]->sub22_id!=0):
					$exam_particle_count = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub22_id)
										->count();
					$mark_check=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub22_id)->count();
					if($exam_particle_count!=$mark_check):
						$error_message ='Class Roll '.$student_info->id.' have no marks in '.$student_sub[0]->sub8->name.'('.$student_sub[0]->sub8->code.')';
						return Redirect::back()->withInput()->with('error',$error_message);
					endif;
				endif;
				//end				
				
				//Sub 9 mark check
				if($student_sub[0]->sub23_id!=0):
					$exam_particle_count = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub23_id)
										->count();
					$mark_check=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub23_id)->count();
					if($exam_particle_count!=$mark_check):
						$error_message ='Class Roll '.$student_info->id.' have no marks in '.$student_sub[0]->sub9->name.'('.$student_sub[0]->sub9->code.')';
						return Redirect::back()->withInput()->with('error',$error_message);
					endif;
				endif;
				//end				
				//Sub 10 mark check
				if($student_sub[0]->sub24_id!=0):
					$exam_particle_count = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub24_id)
										->count();
					$mark_check=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub24_id)->count();
					if($exam_particle_count!=$mark_check):
						$error_message ='Class Roll '.$student_info->id.' have no marks in '.$student_sub[0]->sub10->name.'('.$student_sub[0]->sub10->code.')';
						return Redirect::back()->withInput()->with('error',$error_message);
					endif;
				endif;
				//end

				//Sub 11 mark check
				if($student_sub[0]->sub25_id!=0):
					$exam_particle_count = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub25_id)
										->count();
					$mark_check=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub25_id)->count();
					if($exam_particle_count!=$mark_check):
						$error_message ='Class Roll '.$student_info->id.' have no marks in '.$student_sub[0]->sub11->name.'('.$student_sub[0]->sub11->code.')';
						return Redirect::back()->withInput()->with('error',$error_message);
					endif;
				endif;
				//end
				//Sub 12 mark check
				if($student_sub[0]->sub26_id!=0):
					$exam_particle_count = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub26_id)
										->count();
					$mark_check=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub26_id)->count();
					if($exam_particle_count!=$mark_check):
						$error_message ='Class Roll '.$student_info->id.' have no marks in '.$student_sub[0]->sub12->name.'('.$student_sub[0]->sub12->code.')';
						return Redirect::back()->withInput()->with('error',$error_message);
					endif;
				endif;
				//end
				
				//Sub fourth mark check
				if($student_sub[0]->fourth2_id!=0):
				
					$exam_particle_count = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->fourth2_id)
										->count();
										
					$mark_check=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->fourth2_id)->count();
					
					
					if($exam_particle_count!=$mark_check):
						$error_message ='Class Roll '.$student_info->id.' have no marks in '.$student_sub[0]->fourth2->name.'('.$student_sub[0]->fourth2->code.')';
						
						return Redirect::back()->withInput()->with('error',$error_message);
					endif;
				endif;
				
			
				//end
}
catch (Exception  $e) {
  //display custom message
  echo $student_info->id;
  return $e;
}				
		    endforeach;
		
		   //insert total mark & gpa of a subject
		    foreach($student_infos as $student_info) :

				$student_sub=StudentSubInfo::where('session',$session)->whereStudent_id($student_info->id)->whereCurrent_level($student_info->current_level)->get();
				
				if(count($student_sub) < 1) continue;
				//Sub 1 
				if($student_sub[0]->sub1_id!=0):					
					$marks=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub1_id)->get();
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					foreach ($marks as  $mark) :
						if(is_numeric($mark->mark)):
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub1_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert):
								$sub1_fail=1;
							endif;				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;			    	
					    else:
					    	$ab_sub1=1;					    	
						endif;
					endforeach;
					if($ab_sub1==0):
						if($sub1_fail!=1):
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;
							
							
							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub1_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
						else:
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub1_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
						endif;
				   else:
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->exam_year=$exam_year;
						$insert->subject_id=$student_sub[0]->sub1_id;
						$insert->total_mark=ceil($total_obt_sub1);
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   endif;			   
			    endif;
				
				//end
				//Sub 2 mark check
				if($student_sub[0]->sub2_id!=0):					
					$marks=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub2_id)->get();
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					foreach ($marks as  $mark) :
						if(is_numeric($mark->mark)):
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub2_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert):
								$sub1_fail=1;
							endif;				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;			    	
					    else:
					    	$ab_sub1=1;					    	
						endif;
					endforeach;
					if($ab_sub1==0):
						if($sub1_fail!=1):
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub2_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;
							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub2_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
						else:
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub2_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
						endif;
				   else:
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_year=$exam_year;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub2_id;
						$insert->total_mark=ceil($total_obt_sub1);
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   endif;			   
			    endif;
				//end

				//Sub 3 mark check
				if($student_sub[0]->sub3_id!=0):					
					$marks=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub3_id)->get();
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					foreach ($marks as  $mark) :
						if(is_numeric($mark->mark)):
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub3_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert):
								$sub1_fail=1;
							endif;				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;			    	
					    else:
					    	$ab_sub1=1;					    	
						endif;
					endforeach;
					if($ab_sub1==0):
						if($sub1_fail!=1):
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub3_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub3_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
						else:
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub3_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
						endif;
				   else:
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->exam_year=$exam_year;
						$insert->subject_id=$student_sub[0]->sub3_id;
						$insert->total_mark=ceil($total_obt_sub1);
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   endif;			   
			    endif;
				//end

				//Sub 4 mark check
				if($student_sub[0]->sub4_id!=0):					
					$marks=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub4_id)->get();
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					foreach ($marks as  $mark) :
						if(is_numeric($mark->mark)):
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub4_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert):
								$sub1_fail=1;
							endif;				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;			    	
					    else:
					    	$ab_sub1=1;					    	
						endif;
					endforeach;
					if($ab_sub1==0):
						if($sub1_fail!=1):
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub4_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub4_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
						else:
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->exam_year=$exam_year;
							$insert->subject_id=$student_sub[0]->sub4_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
						endif;
				   else:
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_year=$exam_year;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub4_id;
						$insert->total_mark=ceil($total_obt_sub1);
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   endif;			   
			    endif;
				//end

				//Sub 5 mark check
				if($student_sub[0]->sub5_id!=0):					
					$marks=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub5_id)->get();
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					foreach ($marks as  $mark) :
						if(is_numeric($mark->mark)):
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub5_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert):
								$sub1_fail=1;
							endif;				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;			    	
					    else:
					    	$ab_sub1=1;					    	
						endif;
					endforeach;
					if($ab_sub1==0):
						if($sub1_fail!=1):
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub5_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub5_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
						else:
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->exam_year=$exam_year;
							$insert->subject_id=$student_sub[0]->sub5_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
						endif;
				   else:
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->exam_year=$exam_year;
						$insert->subject_id=$student_sub[0]->sub5_id;
						$insert->total_mark=ceil($total_obt_sub1);
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   endif;			   
			    endif;
				//end	

				//Sub 6 mark check
				if($student_sub[0]->sub6_id!=0):					
					$marks=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub6_id)->get();
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					foreach ($marks as  $mark) :
						if(is_numeric($mark->mark)):
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub6_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert):
								$sub1_fail=1;
							endif;				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;			    	
					    else:
					    	$ab_sub1=1;					    	
						endif;
					endforeach;
					if($ab_sub1==0):
						if($sub1_fail!=1):
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub6_id)
								->first();
								// dd($subject_particle->total);
								// dd($subject_particle);
							// dd($total_obt_sub1.' '.$subject_particle->total_converted.' '.$subject_particle->total);
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;	
							// dd($total_obt_sub1);		
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->exam_year=$exam_year;
							$insert->subject_id=$student_sub[0]->sub6_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
						else:
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub6_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
						endif;
				   else:
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_year=$exam_year;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub6_id;
						$insert->total_mark=ceil($total_obt_sub1);
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   endif;			   
			    endif;
				//end	

				//Sub fourth mark check
				if($student_sub[0]->fourth_id!=0):					
					$marks=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->fourth_id)->get();
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					foreach ($marks as  $mark) :
						if(is_numeric($mark->mark)):
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->fourth_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert):
								$sub1_fail=1;
							endif;				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;			    	
					    else:
					    	$ab_sub1=1;					    	
						endif;
					endforeach;
					if($ab_sub1==0):
						if($sub1_fail!=1):
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->fourth_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=1;
							$insert->save();
						else:
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->fourth_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=1;
							$insert->save();
						endif;
				   else:
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->exam_year=$exam_year;
						$insert->subject_id=$student_sub[0]->fourth_id;
						$insert->total_mark=ceil($total_obt_sub1);
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=1;
						$insert->absent=1;
						$insert->save();
				   endif;			   
			    endif;
				//end
			
					
				//Sub 8 mark check
				if($student_sub[0]->sub21_id!=0):					
					$marks=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub21_id)->get();
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					foreach ($marks as  $mark) :
						if(is_numeric($mark->mark)):
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub21_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert):
								$sub1_fail=1;
							endif;				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;			    	
					    else:
					    	$ab_sub1=1;					    	
						endif;
					endforeach;
					if($ab_sub1==0):
						if($sub1_fail!=1):
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->exam_year=$exam_year;
							$insert->subject_id=$student_sub[0]->sub21_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
						else:
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub21_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
						endif;
				   else:
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_year=$exam_year;
						$insert->exam_id=$exam_id;
						$insert->subject_id=$student_sub[0]->sub21_id;
						$insert->total_mark=ceil($total_obt_sub1);
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   endif;			   
			    endif;
				//end
				//Sub 9 mark check
				if($student_sub[0]->sub22_id!=0):					
					$marks=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub22_id)->get();
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					foreach ($marks as  $mark) :
						if(is_numeric($mark->mark)):
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub22_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert):
								$sub1_fail=1;
							endif;				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;			    	
					    else:
					    	$ab_sub1=1;					    	
						endif;
					endforeach;
					if($ab_sub1==0):
						if($sub1_fail!=1):
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->exam_year=$exam_year;
							$insert->subject_id=$student_sub[0]->sub22_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
						else:
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->exam_year=$exam_year;
							$insert->subject_id=$student_sub[0]->sub22_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
						endif;
				   else:
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->exam_year=$exam_year;
						$insert->subject_id=$student_sub[0]->sub22_id;
						$insert->total_mark=ceil($total_obt_sub1);
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   endif;			   
			    endif;
				//end
				//Sub 10 mark check
				if($student_sub[0]->sub23_id!=0):					
					$marks=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub23_id)->get();
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					foreach ($marks as  $mark) :
						if(is_numeric($mark->mark)):
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub23_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert):
								$sub1_fail=1;
							endif;				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;			    	
					    else:
					    	$ab_sub1=1;					    	
						endif;
					endforeach;
					if($ab_sub1==0):
						if($sub1_fail!=1):
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->exam_year=$exam_year;
							$insert->subject_id=$student_sub[0]->sub23_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
						else:
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->exam_year=$exam_year;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub23_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
						endif;
				   else:
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->exam_year=$exam_year;
						$insert->subject_id=$student_sub[0]->sub23_id;
						$insert->total_mark=ceil($total_obt_sub1);
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   endif;			   
			    endif;
				//end
				//Sub 11 mark check
				if($student_sub[0]->sub24_id!=0):					
					$marks=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub24_id)->get();
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					foreach ($marks as  $mark) :
						if(is_numeric($mark->mark)):
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub24_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert):
								$sub1_fail=1;
							endif;				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;			    	
					    else:
					    	$ab_sub1=1;					    	
						endif;
					endforeach;
					if($ab_sub1==0):
						if($sub1_fail!=1):
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub24_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
						else:
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub24_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
						endif;
				   else:
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->exam_year=$exam_year;
						$insert->subject_id=$student_sub[0]->sub24_id;
						$insert->total_mark=ceil($total_obt_sub1);
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   endif;			   
			    endif;
				//end
				//Sub 12 mark check
				if($student_sub[0]->sub25_id!=0):					
					$marks=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub25_id)->get();
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					foreach ($marks as  $mark) :
						if(is_numeric($mark->mark)):
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub25_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert):
								$sub1_fail=1;
							endif;				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;			    	
					    else:
					    	$ab_sub1=1;					    	
						endif;
					endforeach;
					if($ab_sub1==0):
						if($sub1_fail!=1):
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_year=$exam_year;
							$insert->exam_id=$exam_id;
							$insert->subject_id=$student_sub[0]->sub25_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
						else:
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->exam_year=$exam_year;
							$insert->subject_id=$student_sub[0]->sub25_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
						endif;
				   else:
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->exam_year=$exam_year;
						$insert->subject_id=$student_sub[0]->sub25_id;
						$insert->total_mark=ceil($total_obt_sub1);
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   endif;			   
			    endif;
				//end
				//Sub 13 mark check
				if($student_sub[0]->sub26_id!=0):					
					$marks=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->sub26_id)->get();
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					foreach ($marks as  $mark) :
						if(is_numeric($mark->mark)):
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->sub26_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert):
								$sub1_fail=1;
							endif;				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;			    	
					    else:
					    	$ab_sub1=1;					    	
						endif;
					endforeach;
					if($ab_sub1==0):
						if($sub1_fail!=1):
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->exam_year=$exam_year;
							$insert->subject_id=$student_sub[0]->sub26_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=0;
							$insert->save();
						else:
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->exam_year=$exam_year;
							$insert->subject_id=$student_sub[0]->sub26_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=0;
							$insert->save();
						endif;
				   else:
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->exam_year=$exam_year;
						$insert->subject_id=$student_sub[0]->sub26_id;
						$insert->total_mark=ceil($total_obt_sub1);
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=0;
						$insert->absent=1;
						$insert->save();
				   endif;			   
			    endif;
				//end				
				
				//Sub fourth2 mark check
				if($student_sub[0]->fourth2_id!=0):					
					$marks=Mark::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($student_sub[0]->fourth2_id)->get();
					$total_obt_sub1=0;
					$subject_total=0;
					$ab_sub1=0;
					$sub1_fail=0;
					foreach ($marks as  $mark) :
						if(is_numeric($mark->mark)):
							$exam_particle = ConfigExamParticle::whereClasse_id($curr_level->id)
										->whereGroup_id($group_id)
										->whereSubject_id($student_sub[0]->fourth2_id)
										->whereXmparticle_id($mark->particle_id)
										->get();
							if($mark->converted_mark<$exam_particle[0]->pass_particle_convert):
								$sub1_fail=1;
							endif;				
							$total_obt_sub1+=$mark->converted_mark;
							$subject_total+=$exam_particle[0]->particle_convert;			    	
					    else:
					    	$ab_sub1=1;					    	
						endif;
					endforeach;
					if($ab_sub1==0):
						if($sub1_fail!=1):
							$g_and_p=Ecm::gradePoint($total_obt_sub1,$subject_total);
							$subject_particle = SubjectPartical::whereClasse_id($curr_level->id)
								->whereGroup_id($group_id)
								->whereSubject_id($student_sub[0]->sub1_id)
								->first();
							$total_obt_sub1 = ($total_obt_sub1*$subject_particle->total_converted)/$subject_particle->total;							
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->exam_year=$exam_year;
							$insert->subject_id=$student_sub[0]->fourth2_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade=$g_and_p['grade'];
							$insert->point=$g_and_p['point'];
							$insert->fourth=1;
							$insert->save();
						else:
							$insert=new StudentSubMarkGp;
							$insert->student_id=$student_info->id;
							$insert->session=$session;
							$insert->group_id=$group_id;
							$insert->exam_id=$exam_id;
							$insert->exam_year=$exam_year;
							$insert->subject_id=$student_sub[0]->fourth2_id;
							$insert->total_mark=ceil($total_obt_sub1);
							$insert->grade='F';
							$insert->point=0;
							$insert->fourth=1;
							$insert->save();
						endif;
				   else:
				   		$insert=new StudentSubMarkGp;
						$insert->student_id=$student_info->id;
						$insert->session=$session;
						$insert->group_id=$group_id;
						$insert->exam_id=$exam_id;
						$insert->exam_year=$exam_year;
						$insert->subject_id=$student_sub[0]->fourth2_id;
						$insert->total_mark=ceil($total_obt_sub1);
						$insert->grade='F';
						$insert->point=0;
						$insert->fourth=1;
						$insert->absent=1;
						$insert->save();
				   endif;			   
			    endif;
				//end				
				
			
				
				$all_sub_mark=StudentSubMarkGp::whereStudent_id($student_info->id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->get();
				
				$cgpa=0;
				$fail=0;	
				$without_4th=0;
				$grand_cgpa=0;
				$forth_count = 0;
				foreach ($all_sub_mark as  $all) :
					if($all->fourth!=1):
						
						$cgpa+=$all->point;
						$without_4th+=$all->point;
						if($all->point==0):
							$fail=1;
						endif;	
					else:
					    $forth_count++;
						if($all->point>2):
							$cgpa+=$all->point-2;
						endif;
					endif;
				endforeach;
				
				if($fail!=1):
					$no_sub=$all_sub_mark->count()-$forth_count;
					if($cgpa>($no_sub*5)):
						$grand_cgpa=5;
				        else:
					$grand_cgpa=$cgpa/$no_sub;
				        endif;
					
					$without_4th_grand_cgpa=$without_4th/$no_sub;
					$grade=Ecm::grade($grand_cgpa);
					//$without_4th_cgpa=Ecm::grade($without_4th_grand_cgpa);
					$insert_gpa=new HscGpa;
					$insert_gpa->student_id=$student_info->id;
					$insert_gpa->session=$session;
					$insert_gpa->exam_year=$exam_year;
					$insert_gpa->group_id=$group_id;
					$insert_gpa->exam_id=$exam_id;
					$insert_gpa->cgpa=$grand_cgpa;
					$insert_gpa->without_4th=$without_4th_grand_cgpa;					
					$insert_gpa->grade=$grade;
					$insert_gpa->save();
				else:
					$insert_gpa=new HscGpa;
					$insert_gpa->student_id=$student_info->id;
					$insert_gpa->session=$session;
					$insert_gpa->exam_year=$exam_year;
					$insert_gpa->group_id=$group_id;
					$insert_gpa->exam_id=$exam_id;
					$insert_gpa->cgpa=0;
					$insert_gpa->without_4th=0;					
					$insert_gpa->grade='F';
					$insert_gpa->save();
				endif;
				//**** Needed to comment**************
				//break;
		    endforeach;
			}	
			$insert_row=new HscRsltProcessing;
		    $insert_row->session=$session;
			$insert_row->exam_year=$exam_year;
			$insert_row->group_id=$group_id;
			$insert_row->classe_id=$current_level;
			$insert_row->exam_id=$exam_id;
			$insert_row->save();

		$message = 'You have successfully Processesd Result';
		return Redirect::back()
						->with('success',$message);
						
		       

		}

	}

	

	public function show($id) {



	}



	public function edit($id) {


	}



	public function update($id) {

		
		
	}

	public function getExam($id) {

	   $exam_list =ClasseExam::where('classe_id','=',$id)->get();
	   
	    $exam_arr=[];
	    foreach ($exam_list as  $value):
	    	$exam_arr[$value->exam_id]=$value->exam->name;
	    endforeach;	

	    return Response::json(['success' => true, 'exam_arr' => $exam_arr]);

	}

	public function getSubject($year,$id) {

	   $sub_list =ClasseSubject::whereClasse_id($year)->whereGroup_id($id)->get();
	   
	    $sub_arr=[];
	    foreach ($sub_list as  $value):
	    	$sub_arr[$value->subject_id]=$value->subject->name.'('.$value->subject->code.')';
	    endforeach;	

	    return Response::json(['success' => true, 'sub_arr' => $sub_arr]);

	}

	public function destroy(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;

		$result_pro=HscRsltProcessing::find($id);
		HscGpa::whereSession($result_pro->session)->whereGroup_id($result_pro->group_id)->whereExam_id($result_pro->exam_id)->delete();
		StudentSubMarkGp::whereSession($result_pro->session)->whereGroup_id($result_pro->group_id)->whereExam_id($result_pro->exam_id)->delete();
		HscRsltProcessing::find($id)->delete();
		$error_message = 'You have deleted the Processesd Result of '.$result_pro->session.' '.$result_pro->group->name.' of '.$result_pro->exam->name;
		return Redirect::back()->with('error',$error_message);

	}
}
