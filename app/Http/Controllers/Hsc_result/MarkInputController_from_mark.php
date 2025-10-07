<?php

namespace App\Http\Controllers\Hsc_result;

use DB;
use Ecm;
use Esm;
use Auth;
use App\Models\Mark;
use App\Models\Group;
use App\Models\Classe;
use App\Models\Subject;
use App\Models\ClassExam;
use App\Models\ClassTest;
use App\Models\ClassSubject;
use Illuminate\Http\Request;
use App\Models\ClassTestExam;
use App\Models\ClassTestMark;
use App\Models\StudentInfoHsc;
use App\Models\MarkInputConfig;
use App\Models\ConfigExamParticle;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Redirect;

class MarkInputController extends Controller
{
    public function index() {
        $userId = Auth::id();
		$result_group = Group::join('user_group_assign','groups.id','=','user_group_assign.group_id')->where('user_group_assign.user_id',$userId)->selectRaw('name, groups.id')->orderBy('groups.id')->pluck('name', 'id');
		//return $result_group;
		$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'hsc_result.mark_input.index:Mark Input|Dashboard';		
		$classes = Classe::orderBy('id')->paginate(Ecm::paginate());
		$current_yr_lists = create_option_array('classes', 'id', 'name', 'Current Year');
		$group_lists = create_option_array('groups', 'id', 'name', 'Group');
		$exam_lists = ['' => 'Select exam'] /*+ Exam::orderBy('id')->pluck('name', 'id')*/;
		$exam_tests = ['' => 'Select class test'] /*+ Exam::orderBy('id')->pluck('name', 'id')*/;
		$subject_lists = ['' => 'Select Subject'] ;
		return view('BackEnd.hsc_result.mark_input.index')
					->withTitle($title)
					->with('current_yr_lists',$current_yr_lists)
					->with('group_lists',$group_lists)
					->with('exam_lists',$exam_lists)
					->with('exam_tests',$exam_tests)
					->with('subject_lists',$subject_lists)	
					->withBreadcrumb($breadcrumb);					

	}

	public function csv() {

		$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'hsc_result.mark_input.index:Mark Input|Dashboard';		
		$classes = Classe::orderBy('id')->paginate(Ecm::paginate());
		$session_lists = options_by_array(StudentInfoHsc::groupBy('session')->pluck('session', 'session')->toArray(), 'Session');
		$current_yr_lists = create_option_array('classes', 'id', 'name', 'Current Year');
		$group_lists = create_option_array('groups', 'id', 'name', 'Group');
		$exam_lists = create_option_array('exams', 'id', 'name', 'Exam');
		$subject_lists = selective_multiple_hsc_subject();
		return view('BackEnd.hsc_result.mark_input.csv', compact('title', 'session_lists','current_yr_lists','group_lists','exam_lists','subject_lists','breadcrumb'));					

	}



	public function create() {

		

	}

	public function store(Request $request) {

		if ($request->isMethod('post'))
		{

			$this->validate($request, [
				'session' => 'required',
				'exam_year' => 'required',
				'current_level' => 'required',
				'group_id' => 'required',
				'exam_id' => 'required',
				'exam_test_id' => 'required',
				'subject_id' => 'required'
			],[
				'exam_id.required' => 'This exam field is required.',
				'subject_id.required' => 'This subject field is required.',
				'exam_test_id.required' => 'This exam test field is required.',
			]);


		    $session = $request->get('session');		
			$group_id =  $request->get('group_id');
			$current_level = $request->get('current_level');
			$exam_id = $request->get('exam_id');
			$subject_id = $request->get('subject_id');
            $exam_test_id =$request->get('exam_test_id');
			$exam_year = $request->get('exam_year');
			
			$curr_level=Classe::find($current_level);
			$group_name=Group::find($group_id);

			$config_exam_particles = ConfigExamParticle::where('classe_id',$curr_level->id)
									->where('group_id',$group_id)
									->where('subject_id',$subject_id)
									->get();

			
			//$student_infos = StudentInfoHsc::where('session',$session)->where('current_level',$curr_level->name)->where('groups',$group_name->name)->orderBy('id')->get();
			$student_infos = StudentInfoHsc::where('current_level',$curr_level->name)->where('groups',$group_name->name)->orderBy('id')->get();
			
			$student_info_ids = [];

			foreach($student_infos as $student_info) :			
				$field_name = 'info-' . $student_info->id;
				if($student_info->id == $request->get($field_name)) :
					$student_info_ids[] = $student_info->id;
				endif;
			endforeach;
			foreach ($student_info_ids as  $value) :
					$student_info=StudentInfoHsc::find($value);
				// Turn On After Testing
				foreach($config_exam_particles as $config_exam_particle) :
							$field_name = $config_exam_particle->xmparticle->short_name . '-' . $config_exam_particle->xmparticle->id . '-' . $student_info->id;
							$field_value = trim($request->get($field_name));
						
							if($field_value != '') :
								if(is_numeric($field_value)) :
									$total = $config_exam_particle->total;
									if($field_value > $total) :
										$error_message = $config_exam_particle->xmparticle->name . ' field value should not to be greater than ' . $total;
										return Redirect::back()->withInput()->with('error',$error_message);
									endif;	

									if($field_value < 0) :
										$error_message = $config_exam_particle->xmparticle->name . ' field value should not be negative';
										return Redirect::back()->withInput()->with('error',$error_message);
									endif;	
								else :
									if($field_value=='A' || $field_value=='Absent' ):
									else:
									    $error_message = $config_exam_particle->xmparticle->name . 'field should be Numeric or A or Absent';
										return Redirect::back()->withInput()->with('error',$error_message);
									endif;											
								endif;	
							else :
								$error_message = $config_exam_particle->xmparticle->name . ' field is required';
								return Redirect::back()->withInput()->with('error',$error_message);
							endif;	
				endforeach;
		    endforeach;		   
		 
		    foreach($student_info_ids as $student_info) :		
				foreach($config_exam_particles as $config_exam_particle) :
						$field_name = $config_exam_particle->xmparticle->short_name . '-' . $config_exam_particle->xmparticle->id . '-' . $student_info;
							$this_particle_mark = trim($request->get($field_name));
							
					if($exam_test_id == 0):	
						//$exist_mark=Mark::where('student_id',$student_info)->where('session',$session)->where('group_id',$group_id)->where('exam_id',$exam_id)->where('subject_id',$subject_id)->where('particle_id',$config_exam_particle->xmparticle_id)->get();
						
						$exist_mark=Mark::where('student_id',$student_info)->where('exam_year',$exam_year)->where('group_id',$group_id)->where('exam_id',$exam_id)->where('subject_id',$subject_id)->where('particle_id',$config_exam_particle->xmparticle_id)->get();
						
						if($exist_mark->count()!=0):
							$update_row=Mark::find($exist_mark[0]->id);
							$update_row->mark = $this_particle_mark;
							if( $this_particle_mark=='A' ||  $this_particle_mark=='Absent'):
								$update_row->converted_mark ='A';
						    else:
							$update_row->converted_mark =($this_particle_mark*$config_exam_particle->per_centage)/100;
							endif;	
											
							$update_row->save();
							
							
						else:					
							$mark = new Mark;
							$mark->student_id = $student_info;
							$mark->session = $session;
							$mark->exam_year = $exam_year;
							$mark->group_id = $group_id;
							$mark->exam_id = $exam_id;
							$mark->subject_id = $subject_id;
							$mark->particle_id = $config_exam_particle->xmparticle_id;					
							$mark->mark = $this_particle_mark;
							if( $this_particle_mark=='A' ||  $this_particle_mark=='Absent'):
								$mark->converted_mark ='A';
						    else:
							$mark->converted_mark =($this_particle_mark*$config_exam_particle->per_centage)/100;
							endif;					
							$mark->save();
						endif;
                    else:
						$exist_mark=ClassTestMark::where('student_id',$student_info)->where('session',$session)->where('group_id',$group_id)->where('exam_id',$exam_id)->whereClass_test_id($exam_test_id)->where('subject_id',$subject_id)->where('particle_id',$config_exam_particle->xmparticle_id)->get();
						if($exist_mark->count()!=0):
							$update_row=ClassTestMark::find($exist_mark[0]->id);
							$update_row->mark = $this_particle_mark;
							if( $this_particle_mark=='A' ||  $this_particle_mark=='Absent'):
								$update_row->converted_mark ='A';
						    else:
							$update_row->converted_mark =($this_particle_mark*$config_exam_particle->per_centage)/100;
							endif;	
											
							$update_row->save();
							
							
						else:	
							$total = $config_exam_particle->total;						
							$mark = new ClassTestMark;
							$mark->student_id = $student_info;
							$mark->session = $session;
							$mark->group_id = $group_id;
							$mark->exam_id = $exam_id;
							$mark->class_test_id = $exam_test_id;
							$mark->subject_id = $subject_id;
							$mark->particle_id = $config_exam_particle->xmparticle_id;$mark->total_mark = $total;					
							$mark->mark = $this_particle_mark;
							if( $this_particle_mark=='A' ||  $this_particle_mark=='Absent'):
								$mark->converted_mark ='A';
						    else:
							$mark->converted_mark =($this_particle_mark*$config_exam_particle->per_centage)/100;
							endif;					
							$mark->save();
                        endif;							
					endif;	
				endforeach;
		    endforeach;

		$message = 'You have successfully inserted exam mark';
		return Redirect::route('hsc_result.mark_input.index')
						->with('success',$message);	       

		}

	}

	public function csvUpload(Request $request)
	{
		if ($request->isMethod('post')):

			$session = $request->get('session');		
			$group_id =  $request->get('group');
			$current_level = $request->get('current_year');
			$exam_id = $request->get('exam_id');
			$subject_id = $request->get('subject_id');
			$exam_year = $request->get('exam_year');
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

			if($subject_id == '') :
				$error_message = 'Select Subject';
				return Redirect::back()->withInput()->with('error',$error_message);
			endif;

			$chk_exam=ClassExam::whereExam_id($exam_id)->whereClasse_id($current_level)->count();
			if($chk_exam == 0) :
				$error_message = 'Exam Not Assign';
				return Redirect::back()->withInput()->with('error',$error_message);
			endif;

			$curr_level=Classe::find($current_level);
			$group_name=Group::find($group_id);
	                $sub_name=Subject::find($subject_id);
			$config_exam_particles = ConfigExamParticle::whereClasse_id($curr_level->id)
									->whereGroup_id($group_id)
									->whereSubject_id($subject_id)
									->orderby('xmparticle_id')
									->get();
						
		    if($request->hasFile('csv_file')) :

				$file = $request->file('csv_file');
				$extension = $file->getClientOriginalExtension();
				
				if(strtolower($extension) == 'csv') :
					ini_set("auto_detect_line_endings", true);
					$tmp_file = $file->getRealPath();
					$handle = fopen($tmp_file, 'r');
					$handle2 = fopen($tmp_file, 'r');
					$csv_rolls = [];
					$row = 1;
					$csv_format=$config_exam_particles->count()+1;
				   
					//return $handle;
					while(($fileop = fgetcsv($handle, 1000,",")) !== FALSE ) :
						if($row != 1) :
							/*if($row ==3)
								return $fileop ;*/
							if(count($fileop) == $csv_format) :						

								$student_id = $fileop[0];
								if($csv_format==2):
									$written_par = $fileop[1];
								elseif($csv_format==3):
									$written_par = $fileop[1];
									$mcq_par = $fileop[2];
								elseif($csv_format==4):
									$written_par = $fileop[1];
									$mcq_par = $fileop[2];
									$practical_par = $fileop[3];			
								endif;

								$student_count=StudentInfoHsc::whereId($student_id)->count();
							
								if($student_count==0):
									$error_message = $student_id.' Class Roll Could not found';
								// 	return Redirect::back()->with('error',$error_message);
								    continue;
								endif;

								//ADD
								$sub1_chk = DB::table('student_subject_info')
								    ->where('current_level', '=', $curr_level->name)		   
								    //->where('group_id','=',$group_id)
								    ->where('student_id','=',$student_id)
								    //->where('session','=',$session)
								  	->where('sub1_id', '=', $subject_id);		  	
		   
	
								$sub2_chk = DB::table('student_subject_info')
								    ->where('current_level', '=', $curr_level->name)		    
								   // ->where('group_id','=',$group_id)
								    ->where('student_id','=',$student_id)
								    //->where('session','=',$session)
								  	->where('sub2_id', '=', $subject_id);
						  
						   
								$sub3_chk = DB::table('student_subject_info')
								   ->where('current_level', '=', $curr_level->name)		 
								   //->where('group_id','=',$group_id)
								   ->where('student_id','=',$student_id)
								   //->where('session','=',$session)
								  ->where('sub3_id', '=', $subject_id);
						  
								$sub4_chk = DB::table('student_subject_info')
								    ->where('current_level', '=', $curr_level->name)		  
								    //->where('group_id','=',$group_id)
								    ->where('student_id','=',$student_id)
								    //->where('session','=',$session)
								  	->where('sub4_id', '=', $subject_id);
						   
								$sub5_chk = DB::table('student_subject_info')
								    ->where('current_level', '=', $curr_level->name)		   
								    //->where('group_id','=',$group_id)
								    ->where('student_id','=',$student_id)
								    //->where('session','=',$session)
								  	->where('sub5_id', '=', $subject_id);
						          
								$sub6_chk = DB::table('student_subject_info')
								    ->where('current_level', '=', $curr_level->name)		 
								    //->where('group_id','=',$group_id)
								    ->where('student_id','=',$student_id)
								    //->where('session','=',$session)
								  	->where('sub6_id', '=', $subject_id);
						  
								$fourth_chk = DB::table('student_subject_info')
								    ->where('current_level', '=', $curr_level->name)		 
								    //->where('group_id','=',$group_id)
								    ->where('student_id','=',$student_id)
								    //->where('session','=',$session)
								  	->where('fourth_id', '=', $subject_id);	
		
		
								$student_info = $sub1_chk->union($sub2_chk)->union($sub3_chk)->union($sub4_chk)->union($sub5_chk)->union($sub6_chk)->union($fourth_chk)->orderby('student_id')->get();
								//return $student_info;
								if(count($student_info)==0):
									$error_message = $student_id.' Class Roll Could Have Subject '.$sub_name->name;
									//return Redirect::back()->with('error',$error_message);
								
								//     DB::table('student_subject_info')
								//     ->where('current_level', '=', $curr_level->name)		 
								//     ->where('student_id','=',$student_id)
								// //   	->update(['sub6_id'=> $subject_id]);
								//         ->update(['fourth_id'=> $subject_id]);
								  	
								endif;
								$j=1;
								$temp=0;
								foreach($config_exam_particles as $config_exam_particle) :
									if($j==1):
										$temp=$written_par;
									elseif($j==2):
										$temp=$mcq_par;
									elseif($j==3):
										$temp=$practical_par;										
									endif;	
									if($temp != '') :
										if(is_numeric($temp)) :
											$total = $config_exam_particle->total;
											if($temp > $total) :
												$error_message = $config_exam_particle->xmparticle->name . ' Stude value should not to be greater than ' . $total.' for '.$student_id;
												return Redirect::back()->withInput()->with('error',$error_message);
											endif;	

											if($temp < 0) :
												$error_message = $config_exam_particle->xmparticle->name . ' field value should not be negative for '.$student_id;
												return Redirect::back()->withInput()->with('error',$error_message);
											endif;	
										else :
											if($temp=='A' || $temp=='Absent' ):
											else:
											    $error_message = $config_exam_particle->xmparticle->name . 'field should be Numeric or A or Absent for '.$student_id;
												return Redirect::back()->withInput()->with('error',$error_message);
											endif;											
										endif;	
									else :
										$error_message = $config_exam_particle->xmparticle->name . ' field is required for '.$student_id;
										return Redirect::back()->withInput()->with('error',$error_message);
									endif;	
									$j++;
								endforeach;							
										
							else:
								$error_message = 'Exam Particle not Match';
									return Redirect::back()->withInput()->with('error',$error_message);
							endif;
						endif;
						$row++;	
					endwhile;
					
					$row = 1;
					$f_id=0;
					
					while(($fileop2 = fgetcsv($handle2, 1000,",")) !== FALSE ) :
						if($row != 1) :
							if(count($fileop2) == $csv_format) :								
								
								$student_id = $fileop2[0];
								if($csv_format==2):
									$written_par = $fileop2[1];
								elseif($csv_format==3):
									$written_par = $fileop2[1];
									$mcq_par = $fileop2[2];
								elseif($csv_format==4):
								        $written_par = $fileop2[1];
									$mcq_par = $fileop2[2];
									$practical_par = $fileop2[3];			
								endif;
								$k=1;
								$this_particle_mark=0;
								foreach($config_exam_particles as $config_exam_particle) :	
									if($k==1):
										$this_particle_mark=$written_par;
									elseif($k==2):
										$this_particle_mark=$mcq_par;
									elseif($k==3):
										$this_particle_mark=$practical_par;										
									endif;
									$student_count=StudentInfoHsc::whereId($student_id)->count();
							
    								if($student_count==0):
    									$error_message = $student_id.' Class Roll Could not found';
    								// 	return Redirect::back()->with('error',$error_message);
    								    continue;
    								endif;
    								
									//$exist_mark=Mark::whereStudent_id($student_id)->whereSession($session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($subject_id)->whereParticle_id($config_exam_particle->xmparticle_id)->get();
									
									$exist_mark=Mark::whereStudent_id($student_id)->whereExam_year($exam_year)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($subject_id)->whereParticle_id($config_exam_particle->xmparticle_id)->get();
									
									if($exist_mark->count()!=0):
										$update_row=Mark::find($exist_mark[0]->id);
										$update_row->mark = $this_particle_mark;
										if( $this_particle_mark=='A' ||  $this_particle_mark=='Absent'):
											$update_row->converted_mark ='A';
									    else:
										$update_row->converted_mark =($this_particle_mark*$config_exam_particle->per_centage)/100;
										endif;	
														
										$update_row->save();
									else:	
									    
									    $student_info=StudentInfoHsc::where('id',$student_id)->first();
									    
									    $grp = Group::where('name',$student_info->groups)->first();
									    
										$mark = new Mark;
										$mark->student_id = $student_id;
										$mark->session = $session;
										$mark->exam_year = $exam_year;
										$mark->group_id = $grp->id;
										$mark->exam_id = $exam_id;
										$mark->subject_id = $subject_id;
										$mark->particle_id = $config_exam_particle->xmparticle_id;					
										$mark->mark = $this_particle_mark;
										if( $this_particle_mark=='A' ||  $this_particle_mark=='Absent'):
											$mark->converted_mark ='A';
									    else:
										$mark->converted_mark =($this_particle_mark*$config_exam_particle->per_centage)/100;
										endif;					
										$mark->save();
									endif;
									$k++;	
								endforeach;	
								
							endif;
						endif;
						$row++;	
						
					endwhile;

					fclose($handle);
					fclose($handle2);				

				else :

					$error_message = 'Only CSV file type is acceptable';
					return Redirect::back()->withInput()->with('error',$error_message);

				endif;
			
			else :

				$error_message = 'You have not selected any file';
				return Redirect::back()->withInput()->with('error',$error_message);	

			endif;
			$message = 'You have successfully Uploaded CSV File';
			return Redirect::back()
						->withInput()
						->with('success',$message);		
	    endif;
	
		
	}



	public function marklist(Request $request) {

		$title = 'Easy CollegeMate - HSC Result';
		$breadcrumb = 'hsc_result.mark_input.index:Mark Input|Dashboard';

		$this->validate($request,[
			'session' => 'required',
			'group' => 'required',
			'current_year' => 'required',
			'exam_id' => 'required',
			'subject_id' => 'required',
			'exam_test' => 'required',
			'exam_year' => 'required',
		],[
			'exam_id.required' => 'The Exam field is required.',
			'subject_id.required' => 'The Subject field is required.'
		]);
			
	
		$session = Ecm::filterInput('session', $request->get('session'));		
		$group = Ecm::filterInput('group', $request->get('group'));
		$current_level = Ecm::filterInput('current_year', $request->get('current_year'));
		$exam_id = Ecm::filterInput('exam_id', $request->get('exam_id'));
		$subject_id = Ecm::filterInput('subject_id', $request->get('subject_id'));
        $exam_test_id = Ecm::filterInput('exam_test',$request->get('exam_test'));
		$exam_year = Ecm::filterInput('exam_year',$request->get('exam_year'));

		$chk_exam=ClassExam::where('exam_id',$exam_id)->where('classe_id',$current_level)->count();
		if($chk_exam == 0) :
			$error_message = 'Exam Not Assign';
			return Redirect::back()->withInput()->with('error',$error_message);
		endif;

		//$check_exp_dates = MarkInputConfig::where('session',$session)->where('exam_id',$exam_id)->get();
		$check_exp_dates = MarkInputConfig::where('exam_year',$exam_year)->where('exam_id',$exam_id)->get();
		$have_exam = count($check_exp_dates);
		if($have_exam >0){
		foreach($check_exp_dates as $check_exp_date){
			$exm_exp_date = $check_exp_date->exp_date;
		}
		}
		else{
		    $error_message = 'No exam mark input date set';
			return Redirect::back()->withInput()->with('error',$error_message);
		}
        $is_exam_controller = 0;
		$userId = Auth::id();

		if (Auth::user()->can('hsc_result.process')) {
			$is_exam_controller = 1 ;
		}
		
		
		
		$current_date = date('Y-m-d');
		if($exm_exp_date < $current_date && $is_exam_controller == 0){
		    $error_message = 'This Exam mark input time has been expired ';
			return Redirect::back()->withInput()->with('error',$error_message);			
		}


		$grp=Group::find($group);
		if($grp->name=='Science'):
			$append=1;
		elseif($grp->name=='Humanities'):	
			$append=2;
		elseif($grp->name=='Business Studies'):	
			$append=3;
		endif;	

		$session_arr=explode('-', $session);
		$like_vr=$session_arr[0].$append;

		$curr_level=Classe::find($current_level);

		//$student_info = StudentInfoHsc::where('session',$session)->where('current_level',$curr_level->name)->where('groups',$grp->name)->orderBy('id')->paginate(50);
		$student_info = StudentInfoHsc::where('current_level',$curr_level->name)->where('session',$session)->where('groups',$grp->name)->orderBy('id')->get();
		$student_info_ids = StudentInfoHsc::where('current_level',$curr_level->name)->where('session',$session)->where('groups',$grp->name)->pluck('id');
		$config_exam_particles = ConfigExamParticle::where('classe_id',$curr_level->id)
													->where('group_id',$group)
													->where('subject_id',$subject_id)
													->get();				
	            

		$sub1_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)
		    ->where('group_id','=',$group)
		    // ->where('session','=',$session)
			->whereIn('student_id',$student_info_ids)
		  	->where('sub1_id', '=', $subject_id);
		   
	
		$sub2_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		    
		    ->where('group_id','=',$group)
		    // ->where('session','=',$session)
			->whereIn('student_id',$student_info_ids)
		  	->where('sub2_id', '=', $subject_id);
		  
		   
		$sub3_chk = DB::table('student_subject_info')
		   ->where('current_level', '=', $curr_level->name)		 
		   ->where('group_id','=',$group)
		   // ->where('session','=',$session)
		   ->whereIn('student_id',$student_info_ids)
		  ->where('sub3_id', '=', $subject_id);
		  
		$sub4_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		  
		    ->where('group_id','=',$group)
		    // ->where('session','=',$session)
			->whereIn('student_id',$student_info_ids)
		  	->where('sub4_id', '=', $subject_id);
		   
		$sub5_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		   
		    ->where('group_id','=',$group)
		    // ->where('session','=',$session)
			->whereIn('student_id',$student_info_ids)
		  	->where('sub5_id', '=', $subject_id);
		          
		$sub6_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    // ->where('session','=',$session)
			->whereIn('student_id',$student_info_ids)
		  	->where('sub6_id', '=', $subject_id);
		  
		$sub7_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    // ->where('session','=',$session)
			->whereIn('student_id',$student_info_ids)
		  	->where('sub21_id', '=', $subject_id);

		$sub8_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    // ->where('session','=',$session)
			->whereIn('student_id',$student_info_ids)
		  	->where('sub22_id', '=', $subject_id);
		$sub9_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    // ->where('session','=',$session)
			->whereIn('student_id',$student_info_ids)
		  	->where('sub23_id', '=', $subject_id);
		$sub10_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    // ->where('session','=',$session)
			->whereIn('student_id',$student_info_ids)
		  	->where('sub24_id', '=', $subject_id);
		$sub11_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    // ->where('session','=',$session)
			->whereIn('student_id',$student_info_ids)
		  	->where('sub25_id', '=', $subject_id);
		$sub12_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    // ->where('session','=',$session)
			->whereIn('student_id',$student_info_ids)
		  	->where('sub26_id', '=', $subject_id);
		$fourth_chk2 = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    // ->where('session','=',$session)
			->whereIn('student_id',$student_info_ids)
		  	->where('fourth2_id', '=', $subject_id);
		
			
			
			
			
		$fourth_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    // ->where('session','=',$session)
			->whereIn('student_id',$student_info_ids)
		  	->where('fourth_id', '=', $subject_id);

		$student_info = $sub1_chk->union($sub2_chk)->union($sub3_chk)->union($sub4_chk)->union($sub5_chk)->union($sub6_chk)->union($fourth_chk)->union($sub7_chk)->union($sub8_chk)->union($sub9_chk)->union($sub10_chk)->union($sub11_chk)->union($sub12_chk)->union($fourth_chk2)->orderby('student_id')->paginate(50);
		
		return view('BackEnd.hsc_result.mark_input.list',compact('title','session','exam_year','group','current_level','curr_level','student_info','exam_id','subject_id','exam_test_id','config_exam_particles','breadcrumb'));
		

	}



	public function show($id) {



	}



	public function edit($id) {


	}



	public function update($id) {

		
		
	}

	public function getExam($id) {

	   $exam_list =ClassExam::where('classe_id','=',$id)->get();
	   
	    $exam_arr=[];
	    foreach ($exam_list as  $value):
	    	$exam_arr[$value->exam_id]=$value->exam->name;
	    endforeach;	

	    return response()->json(['success' => true, 'exam_arr' => $exam_arr]);

	}

	public function getSubject($year,$id) {
        $userId = Auth::id();
		$sub_list = ClassSubject::join('user_sub_assign','class_subject.subject_id','=','user_sub_assign.subject_id')->where('user_sub_assign.user_id',$userId)->where('classe_id',$year)->where('group_id',$id)->get();
		//return $result_subject;
	   //$sub_list =ClasseSubject::where('classe_id',$year)->where('group_id',$id)->get();
	   
	    $sub_arr=[];
	    foreach ($sub_list as  $value):
	    	$sub_arr[$value->subject_id]=$value->subject->name.'('.$value->subject->code.')';
	    endforeach;	

	    return response()->json(['success' => true, 'sub_arr' => $sub_arr]);

	}

	
	public function getClasstest($examid) {

	   $classtest_list =ClassTestExam::where('exam_id',$examid)->get();
	   
	    $classtest_arr=[];
	    foreach ($classtest_list as  $value):
		     $classtest_names =ClassTest::whereId($value->class_test_id)->get();
			 foreach ($classtest_names as  $classtest_name):
				$classtest_arr[$classtest_name->id]=$classtest_name->name;
			endforeach;
	    endforeach;	
        if(count($classtest_arr)<1){$classtest_arr[0]='None';}
	    return response()->json(['success' => true, 'sub_arr' => $classtest_arr]);

	}	
	
	
	public function destroy($id) {

		
		
	}

public function MarkPdf($session,$group,$current_level,$exam_id,$subject_id,$exam_test_id,$exam_year) {
	
	    $grp=Group::find($group);
		$curr_level=Classe::find($current_level);

		

		$config_exam_particles = ConfigExamParticle::where('classe_id',$curr_level->id)
													->where('group_id',$group)
													->where('subject_id',$subject_id)
													->get();				
	            

		$sub1_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		   
		    ->where('group_id','=',$group)
		    //->where('session','=',$session)
		  	->where('sub1_id', '=', $subject_id);		  	
		   
	
		$sub2_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		    
		    ->where('group_id','=',$group)
		    //->where('session','=',$session)
		  	->where('sub2_id', '=', $subject_id);
		  
		   
		$sub3_chk = DB::table('student_subject_info')
		   ->where('current_level', '=', $curr_level->name)		 
		   ->where('group_id','=',$group)
		   //->where('session','=',$session)
		  ->where('sub3_id', '=', $subject_id);
		  
		$sub4_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		  
		    ->where('group_id','=',$group)
		    //->where('session','=',$session)
		  	->where('sub4_id', '=', $subject_id);
		   
		$sub5_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		   
		    ->where('group_id','=',$group)
		    //->where('session','=',$session)
		  	->where('sub5_id', '=', $subject_id);
		          
		$sub6_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    //->where('session','=',$session)
		  	->where('sub6_id', '=', $subject_id);
		  
		$sub7_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    //->where('session','=',$session)
		  	->where('sub21_id', '=', $subject_id);

		$sub8_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    //->where('session','=',$session)
		  	->where('sub22_id', '=', $subject_id);
		$sub9_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    //->where('session','=',$session)
		  	->where('sub23_id', '=', $subject_id);
		$sub10_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    //->where('session','=',$session)
		  	->where('sub24_id', '=', $subject_id);
		$sub11_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    //->where('session','=',$session)
		  	->where('sub25_id', '=', $subject_id);
		$sub12_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    //->where('session','=',$session)
		  	->where('sub26_id', '=', $subject_id);
		$fourth_chk2 = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    //->where('session','=',$session)
		  	->where('fourth2_id', '=', $subject_id);
		
			
			
			
			
		$fourth_chk = DB::table('student_subject_info')
		    ->where('current_level', '=', $curr_level->name)		 
		    ->where('group_id','=',$group)
		    //->where('session','=',$session)
		  	->where('fourth_id', '=', $subject_id);

		$student_info = $sub1_chk->union($sub2_chk)->union($sub3_chk)->union($sub4_chk)->union($sub5_chk)->union($sub6_chk)->union($fourth_chk)->union($sub7_chk)->union($sub8_chk)->union($sub9_chk)->union($sub10_chk)->union($sub11_chk)->union($sub12_chk)->union($fourth_chk2)->orderby('student_id')->get(); 		
	       require app_path().'/libs/mpdf/third_party/mpdf60/mpdf.php'; 
 
        $mpdf = new mPDF();	
        $mpdf->allow_charset_conversion=true;
		$mpdf->charset_in='UTF-8';	    
		$mpdf->WriteHTML(view('pdf.marksdownload')
					->withSession($session)
					->withExam_year($exam_year)
					->withGroup($group)
					->withCurrent_level($current_level)
					->withCurr_level($curr_level)					
					->withStudent_info($student_info)					
					->withExam_id($exam_id)	
					->withSubject_id($subject_id)
					->withExam_test_id($exam_test_id)
					->withConfig_exam_particles($config_exam_particles)
		
		);
		$mpdf->Output();		
		
	}
}
