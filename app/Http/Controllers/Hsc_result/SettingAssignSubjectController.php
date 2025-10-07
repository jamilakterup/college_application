<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\Group;
use App\Models\ClassSubject;
use App\Models\Classe;
use App\Models\Subject;
use App\Models\StudentInfoHsc;
use App\Models\HscAdmittedStudent;
use App\Models\StudentSubInfo;
use Ecm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use DB;

class SettingAssignSubjectController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Subject Assign';
		$breadcrumb = 'hsc_result.assign_subject.index:Class Subject|Dashboard';
		$classes = Classe::orderBy('id')->paginate(Ecm::paginate());

		return view('BackEnd.hsc_result.assign_subject.index')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withClasses($classes);

	}



	public function edit($class_id,$department_id) {

		$title = 'Easy CollegeMate - Assign Subject';
		$breadcrumb = 'hsc_result.assign_subject.index:Class Subject|Assign Class Subject';
		$subjects = Subject::orderBy('id')->get();

		return view('BackEnd.hsc_result.assign_subject.edit',compact('title','breadcrumb','class_id','department_id','subjects'));

	}



	public function update(Request $request, $class_id,$department_id) {

		if($class_id !== $request->get('class_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;		

		if($department_id !== $request->get('department_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		$class_has_department = ClassGroup::where('classe_id',$class_id)->where('group_id',$department_id)->count();

		if($class_has_department == 0) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		//Delete & Insert = Update ClassSubject
		ClassSubject::where('classe_id',$class_id)->where('group_id',$department_id)->delete();

		$subjects_id = [];
		$subjects = Subject::get();
		if($subjects->count() > 0) :
			foreach($subjects as $subject) :
				$sub_id = $subject->id;

				if($request->get('subject-' . $sub_id) == $sub_id) :
					$subjects_id[] = $sub_id;
				endif;	
			endforeach;

			if(count($subjects_id) > 0) :
				foreach($subjects_id as $subject_id) :
					$data_array = ['classe_id' => $class_id, 'group_id' => $department_id, 'subject_id' => $subject_id];
					ClassSubject::create($data_array);
				endforeach;	
			endif;	
		endif;

		$count = Classe::where('id', '<=', $class_id)->count();
		$page = ceil($count/Ecm::paginate());			

		$message = 'You have successfully updated the class subject';		
		return Redirect::route('hsc_result.assign_subject.index', ['page' => $page])
						->with('info',$message)
						->withId($class_id)
						->with('department_id',$department_id);			

	}



	public function destroy(Request $request, $class_id,$department_id) {

		if($class_id !== $request->get('class_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;		

		if($department_id !== $request->get('department_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		$class_has_department = ClassGroup::where('classe_id',$class_id)->where('group_id',$department_id)->count();

		if($class_has_department == 0) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		ClassSubject::where('classe_id',$class_id)->where('group_id',$department_id)->delete();

		$error_message = 'You have unassigned all subject';
		return Redirect::back()->with('warning',$error_message);				

	}

	public function assignSubject() {

		$student_infos=TempTable::all();
	
		foreach ($student_infos as $info) {
			//$admitted_info=HscAdmittedStudent::whereAuto_id($info->refference_id)->get();
			$selective=$info->selective;
			$optional=$info->optional;
			$selective=explode('-', $selective);
			$sub1=101;
			$sub2=107;
			$sub3=275;
			$sub4=$selective[0];
			$sub5=$selective[1];
			$sub6=$selective[2];
			$sub7=$optional;

			$sub1_id=Subject::whereCode($sub1)->pluck('id');			
			$sub2_id=Subject::whereCode($sub2)->pluck('id');
			$sub3_id=Subject::whereCode($sub3)->pluck('id');
			$sub4_id=Subject::whereCode($sub4-1)->pluck('id');
			if($sub4_id==''):
				return $info->id." Subject 4 code not Found";
			endif;
			$sub5_id=Subject::whereCode($sub5-1)->pluck('id');
			if($sub5_id==''):
				return $info->id." Subject 5 code not Found";
			endif;
			$sub6_id=Subject::whereCode($sub6-1)->pluck('id');
			if($sub6_id==''):
				return $info->id." Subject 6 code not Found";
			endif;
			$sub7_id=Subject::whereCode($sub7-1)->pluck('id');
			if($sub7_id==''):
				return $info->id." Fourth Subject code not Found";
			endif;
			

			
			//return $sub7[0];
			$insert_row=new StudentSubInfo;
			$insert_row->student_id=$info->student_id;
			$insert_row->current_level='HSC 1st Year';
			$insert_row->sub1_id=$sub1_id;
			$insert_row->sub2_id=$sub2_id;
			$insert_row->sub3_id=$sub3_id;
			$insert_row->sub4_id=$sub4_id;
			$insert_row->sub5_id=$sub5_id;
			$insert_row->sub6_id=$sub6_id;
			$insert_row->fourth_id=$sub7_id;
			$insert_row->save();

			$sub1_id2=Subject::whereCode($sub1+1)->pluck('id');			
			$sub2_id2=Subject::whereCode($sub2+1)->pluck('id');
			$sub3_id2=Subject::whereCode($sub3)->pluck('id');
			$sub4_id2=Subject::whereCode($sub4)->pluck('id');
			if($sub4_id==''):
				return "Subject 4 code not Found";
			endif;
			$sub5_id2=Subject::whereCode($sub5)->pluck('id');
			if($sub5_id==''):
				return "Subject 5 code not Found";
			endif;
			$sub6_id2=Subject::whereCode($sub6)->pluck('id');
			if($sub6_id==''):
				return "Subject 6 code not Found";
			endif;
			$sub7_id2=Subject::whereCode($sub7)->pluck('id');
			if($sub7_id==''):
				return "Fourth Subject code not Found";
			endif;
			

			$insert_row2=new StudentSubInfo;
			$insert_row2->student_id=$info->student_id;
			$insert_row2->current_level='HSC 2nd Year';
			$insert_row2->sub1_id=$sub1_id2;
			$insert_row2->sub2_id=$sub2_id2;
			$insert_row2->sub3_id=$sub3_id2;
			$insert_row2->sub4_id=$sub4_id2;
			$insert_row2->sub5_id=$sub5_id2;
			$insert_row2->sub6_id=$sub6_id2;
			$insert_row2->fourth_id=$sub7_id2;
			$insert_row2->save();
			

		}

	}

	public function student_subject_assign(){
		return view('BackEnd.hsc_result.subject_info.student_sub_assign');
	}

	public function assignSubject_from_hsc_admit(Request $request)
	{
	    try {
	        $this->validate($request, [
	            'session' => 'required',
	            'group' => 'required'
	        ]);

	        $student_infos = StudentInfoHsc::whereSession($request->session)
	            ->whereGroups($request->group)
	            ->get();

	        if ($student_infos->isEmpty()) {
	            return redirect()->back()->with('error', 'No Students Found!');
	        }

	        $group = Group::whereName($request->group)->firstOrFail();
	        $successCount = 0;
	        $errorCount = 0;

	        DB::beginTransaction();
	        
	        foreach ($student_infos as $info) {
	            try {
	                $admitted_info = HscAdmittedStudent::whereAuto_id($info->refference_id)->first();
	                if (!$admitted_info) {
	                    $errorCount++;
	                    continue;
	                }

	                $selective = explode(',', $admitted_info->selective);
	                $optional = $admitted_info->optional;

	                $baseSubjects = [
	                    'sub1' => 101,
	                    'sub2' => 107,
	                    'sub3' => 275,
	                    'sub4' => explode('-', $selective[0])[0] ?? null,
	                    'sub5' => explode('-', $selective[1])[0] ?? null,
	                    'sub6' => explode('-', $selective[2])[0] ?? null,
	                    'sub7' => explode('-', $optional)[0] ?? null
	                ];

	                // Validate all subject codes exist before processing
	                foreach ($baseSubjects as $key => $code) {
	                    if (!$code || !Subject::whereCode($code)->exists()) {
	                        throw new \Exception("Invalid subject code $code for $key");
	                    }
	                }

	                foreach (['HSC 1st Year', 'HSC 2nd Year'] as $level) {
	                    $subjects = $baseSubjects;
	                    
	                    if ($level === 'HSC 2nd Year') {
	                        array_walk($subjects, function(&$code) {
	                            if ($code !== 275) { // sub3 stays the same
	                                $code = intval($code) + 1;
	                            }
	                        });
	                    }

	                    // Get all subject IDs in a single query
	                    $subjectIds = Subject::whereIn('code', array_values($subjects))
	                        ->pluck('id', 'code')
	                        ->toArray();

	                    // Map subject codes to their IDs
	                    $mappedIds = array_map(function($code) use ($subjectIds) {
	                        return $subjectIds[$code] ?? null;
	                    }, $subjects);

	                    StudentSubInfo::updateOrCreate(
	                        [
	                            'student_id' => $info->id,
	                            'current_level' => $level
	                        ],
	                        [
	                            'session' => $request->session,
	                            'group_id' => $group->id,
	                            'sub1_id' => $mappedIds['sub1'],
	                            'sub2_id' => $mappedIds['sub2'],
	                            'sub3_id' => $mappedIds['sub3'],
	                            'sub4_id' => $mappedIds['sub4'],
	                            'sub5_id' => $mappedIds['sub5'],
	                            'sub6_id' => $mappedIds['sub6'],
	                            'fourth_id' => $mappedIds['sub7']
	                        ]
	                    );
	                }
	                
	                $successCount++;

	            } catch (\Exception $e) {
	            		\Log::error("Error processing student ID {$info->id}: " . $e->getMessage());
	                $errorCount++;
	                continue;
	            }
	        }

	        if ($errorCount === count($student_infos)) {
	            DB::rollBack();
	            return redirect()->back()->with('error', 'Failed to process any students. Please check the logs.');
	        }

	        DB::commit();
	        
	        $message = "Successfully processed $successCount students.";
	        if ($errorCount > 0) {
	            $message .= " Failed to process $errorCount students. Check logs for details.";
	        }

	        return redirect()->back()->with('success', $message);

	    } catch (\Exception $e) {
	        DB::rollBack();
	        return redirect()->back()->with('error', 'An unexpected error occurred. Please try again.');
	    }
	}
	
	public function assignSubject_from_hsc_admit_all() {


		// $student_infos=StudentInfoHsc::whereSession('2018-2019')->whereGroups('Science')->where('id','2018000001')->get();
		$student_infos=StudentInfoHsc::whereSession('2021-2022')->get();
		foreach ($student_infos as $info) {
		    	try {
			$admitted_info=HscAdmittedStudent::whereAuto_id($info->refference_id)->get();
			$selective=$admitted_info[0]->selective;
			$optional=$admitted_info[0]->optional;
			$selective=explode(',', $selective);
			$sub1=101;
			$sub2=107;
			$sub3=275;
		
			$sub4=explode('-', $selective[0]);
			$sub5=explode('-', $selective[1]);
			$sub6=explode('-', $selective[2]);
			$sub7=explode('-', $optional);


			$sub1_id=Subject::whereCode($sub1)->pluck('id');			
			$sub2_id=Subject::whereCode($sub2)->pluck('id');
			$sub3_id=Subject::whereCode($sub3)->pluck('id');
			$sub4_id=Subject::whereCode($sub4[0])->pluck('id');
			
			$sub1_id2=Subject::whereCode($sub1+1)->pluck('id');			
			$sub2_id2=Subject::whereCode($sub2+1)->pluck('id');
			$sub3_id2=Subject::whereCode($sub3)->pluck('id');
			$sub4_id2=Subject::whereCode($sub4[0]+1)->pluck('id');			
			
		
			
			if($sub4_id==''):
				return $info->id." Subject 4 code not Found";
			endif;
			$sub5_id=Subject::whereCode($sub5[0])->pluck('id');
			if($sub5_id==''):
				return $info->id."Subject 5 code not Found";
			endif;
			$sub6_id=Subject::whereCode($sub6[0])->pluck('id');
			if($sub6_id==''):
				return "Subject 6 code not Found";
			endif;
			$sub7_id=Subject::whereCode($sub7[0])->pluck('id');
			if($sub7_id==''):
				return "Fourth Subject code not Found";
			endif;


			if($sub4_id==''):
				return "Subject 4 code not Found";
			endif;
			$sub5_id2=Subject::whereCode($sub5[0]+1)->pluck('id');
			if($sub5_id==''):
				return "Subject 5 code not Found";
			endif;
			$sub6_id2=Subject::whereCode($sub6[0]+1)->pluck('id');
			if($sub6_id==''):
				return "Subject 6 code not Found";
			endif;
			$sub7_id2=Subject::whereCode($sub7[0]+1)->pluck('id');
			if($sub7_id==''):
				return "Fourth Subject code not Found";
			endif;			
            
			$groups =DB::table('groups')->where('name',$info->groups)->first();

			//return $sub7[0];
			$insert_row=new StudentSubInfo;
			$insert_row->student_id=$info->id;
			$insert_row->session=$info->session;
			$insert_row->group_id=$groups->id;
			$insert_row->current_level='HSC 1st Year';
			$insert_row->sub1_id=$sub1_id;
			$insert_row->sub2_id=$sub2_id;
			$insert_row->sub3_id=$sub3_id;
			$insert_row->sub4_id=$sub4_id;
			$insert_row->sub5_id=$sub5_id;
			$insert_row->sub6_id=$sub6_id;
			$insert_row->fourth_id=$sub7_id;
		
			$insert_row->sub21_id=$sub1_id2;
			$insert_row->sub22_id=$sub2_id2;
			$insert_row->sub23_id=$sub3_id2;
			$insert_row->sub24_id=$sub4_id2;
			$insert_row->sub25_id=$sub5_id2;
			$insert_row->sub26_id=$sub6_id2;
			$insert_row->fourth2_id=$sub7_id2;		
			
			
			$insert_row->save();
	
			$sub1_id2=Subject::whereCode($sub1+1)->pluck('id');			
			$sub2_id2=Subject::whereCode($sub2+1)->pluck('id');
			$sub3_id2=Subject::whereCode($sub3)->pluck('id');
			$sub4_id2=Subject::whereCode($sub4[0]+1)->pluck('id');
			if($sub4_id==''):
				return "Subject 4 code not Found";
			endif;
			$sub5_id2=Subject::whereCode($sub5[0]+1)->pluck('id');
			if($sub5_id==''):
				return "Subject 5 code not Found";
			endif;
			$sub6_id2=Subject::whereCode($sub6[0]+1)->pluck('id');
			if($sub6_id==''):
				return "Subject 6 code not Found";
			endif;
			$sub7_id2=Subject::whereCode($sub7[0]+1)->pluck('id');
			if($sub7_id==''):
				return "Fourth Subject code not Found";
			endif;
			

			$insert_row2=new StudentSubInfo;
			$insert_row2->student_id=$info->id;
			$insert_row2->session=$info->session;
			$insert_row2->group_id=$groups->id;
			$insert_row2->current_level='HSC 2nd Year';
			$insert_row2->sub1_id=$sub1_id;
			$insert_row2->sub2_id=$sub2_id;
			$insert_row2->sub3_id=$sub3_id;
			$insert_row2->sub4_id=$sub4_id;
			$insert_row2->sub5_id=$sub5_id;
			$insert_row2->sub6_id=$sub6_id;
			$insert_row2->fourth_id=$sub7_id;
			
			$insert_row2->sub21_id=$sub1_id2;
			$insert_row2->sub22_id=$sub2_id2;
			$insert_row2->sub23_id=$sub3_id2;
			$insert_row2->sub24_id=$sub4_id2;
			$insert_row2->sub25_id=$sub5_id2;
			$insert_row2->sub26_id=$sub6_id2;
			$insert_row2->fourth2_id=$sub7_id2;			
			
			
			$insert_row2->save();
			
            }
            
            //catch exception
            catch(Exception $e) {
              print_r($info->id);
              echo '<br />';
            }
		}

return 'Ok';
	}

	public function assign_hsc_subject_from_marks(Request $request){
		$exam_lists = ['' => 'Select exam'] ;
		$current_yr_lists = create_option_array('classes', 'id', 'name', 'Current Year');
		return view('BackEnd.hsc_result.subject_info.student_sub_assign_from_mark', compact('exam_lists','current_yr_lists'));
	}

	public function assign_hsc_subject_from_marks_exe(Request $request) {

		$session = $request->session;
		$exam_id = $request->exam_id;
		$exam_year = $request->exam_year;
		$current_year = $request->current_year;
		$current_level = Classe::where('id', $current_year)->pluck('name')->first();
 
	 	$student_infos= DB::select("SELECT student_id,group_id,session  FROM `marks` WHERE `session` = '$session'  AND `exam_id` = $exam_id and exam_year=$exam_year group by student_id");

		foreach ($student_infos as $info) {
		    	try {
		$exam_subjects= DB::select("SELECT student_id,subject_id  FROM `marks` WHERE `session` = '$session'  AND `exam_id` = $exam_id and exam_year=$exam_year  and  student_id=$info->student_id group by subject_id");
		
		    // StudentSubInfo::where("student_id",$info->student_id)->where("current_level",$current_level)->get();
		    StudentSubInfo::where("student_id",$info->student_id)->delete();
			
			$insert_row2=new StudentSubInfo;
			$insert_row2->student_id=$info->student_id;
			$insert_row2->session=$info->session;
			$insert_row2->group_id=$info->group_id;
			$insert_row2->current_level=$current_level;
			
			$row=1;
    			foreach ($exam_subjects as $exam_subject)
    			{
    			    if($row==1)
    			    {
    		        	$insert_row2->sub1_id=$exam_subject->subject_id;
    			    }
    			    else if($row==2)
    			    {
    		        	$insert_row2->sub2_id=$exam_subject->subject_id;
    			    }
    			    else if($row==3)
    			    {
    		        	$insert_row2->sub3_id=$exam_subject->subject_id;
    			    }
    			    else if($row==4)
    			    {
    		        	$insert_row2->sub4_id=$exam_subject->subject_id;
    			    }
    			    else if($row==5)
    			    {
    		        	$insert_row2->sub5_id=$exam_subject->subject_id;
    			    }
    			    else if($row==6)
    			    {
    		        	$insert_row2->sub6_id=$exam_subject->subject_id;
    			    }
    			    else if($row==7)
    			    {
    		        	$insert_row2->fourth_id=$exam_subject->subject_id;
    			    }
    			    
    			    $row++;
    			}
			
			$insert_row2->save();
			
            }
            
            //catch exception
            catch(Exception $e) {
              print_r($info->student_id.$e);
              echo '<br />';
            }
		}

        return redirect()->back()->with('success', 'Student Subject Successfully Assigned!');
	}
}
