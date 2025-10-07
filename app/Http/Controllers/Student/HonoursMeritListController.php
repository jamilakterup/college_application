<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\HonsMeritList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;

class HonoursMeritListController extends Controller
{
	public function index() {
		
		$title = 'Easy CollegeMate - College Management';
		$breadcrumb = 'student.honours:Honours|Dashboard';
		$hons_merit_students = HonsMeritList::where('delete_status',0)->paginate(Study::paginate());
		
		
		
		return view('student.honours.meritlist')
		->withTitle($title)
		->withBreadcrumb($breadcrumb)
		->withHons_merit_students($hons_merit_students);
		
	}
	
	public function show() {
		
		$title = 'Easy CollegeMate - College Management';
		$breadcrumb = 'student:College Management|Dashboard';
		$hons_merit_students = HonsMeritList::where('delete_status',0)->paginate(Study::paginate());
		
		
		
		return view('student.honours.meritlist')
		->withTitle($title)
		->withBreadcrumb($breadcrumb)
		->withHons_merit_students($hons_merit_students);
		
		
	}
	
	public function meritlist(Request $request)
	{
		
		$admission_roll = $request->get('admission_roll');
		if ($request->isMethod('post'))
		{
			$query = Study::searchHonsMeritlistStudent( $admission_roll);
		}
		else
		{
			$query = Study::searchHonsMeritlistStudent( $admission_roll)->where('delete_status', 0);
		}
		
		$num_rows = $query->count();
		$hons_merit_students = $query->paginate(Study::paginate());
		$title = 'Easy CollegeMate - College Management';
		$breadcrumb = 'students.honours:Honours|Honours Merit List';
		
		
		return view('BackEnd.student.admission.honours.meritlist.index', compact('title', 'breadcrumb', 'hons_merit_students', 'num_rows', 'admission_roll'));								
		
	}
	
	
	public function meritlistUpload()
	{
		
		$title = 'Easy CollegeMate - College Management';
		$breadcrumb = 'students.honours.meritlist:Honours Merit List|Upload Merit List';
		$hons_merit_students = HonsMeritList::where('delete_status', 0)->paginate(Study::paginate());
		
		
		return 	view('BackEnd.student.admission.honours.meritlist.upload', compact('title','breadcrumb', 'hons_merit_students'));					
		
	}
	
	
	public function meritListEdit($id)
	{
		
		
		$title = 'Easy CollegeMate - College Management';
		$breadcrumb = 'students.honours.meritlist:Honours Merit List| Merit List';
		$meritstudent = HonsMeritList::find($id);
		$hsc_group_list= selective_multiple_study_group();		
		
		
		return view('BackEnd.student.admission.honours.meritlist.edit', compact('title', 'breadcrumb', 'hsc_group_list','id','meritstudent'));	
		
		
	}
	
	
	public function postUpload(Request $request)
	{
		
		$this->validate($request, [
			'material_csv' => 'required'
		]);
		
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
						if($key=='birth_date'):
							$birth_date=$single_area;
							
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
						
					endforeach; 
					
					DB::table('hons_merit_list')->insert(
						array('admission_roll' =>$admission_roll,'birth_date'=>$birth_date,'name'=>$name,
						'father_name'=>$father_name,'mother_name'=>$mother_name,
						'faculty'=>$faculty,'subject'=>$subject,'merit_pos'=>$merit_pos,'merit_status'=>$merit_status, 'admission_status'=>$admission_status));
						
						
					endforeach;
					
					$message = 'You have successfully uploaded';
					return Redirect::route('students.honours.meritlist')
					->with('success',$message);
				}                
				
				
				$message = 'Format Not Match';
				return Redirect::route('students.honours.meritlist')
				->with('warning',$message);
				
			}						
			
		}
		
		
		public function meritlistFormatDownload() {
			
			return response()->download(public_path().'/csv/hons_merit.csv');
			
		}
		
		
		public function meritlistSingleDelete(Request $request) {
			
			$title = 'Easy CollegeMate - Students HSC';	
			$breadcrumb = 'students.honours.meritlist:Merit List|Dashboard';
			
			
			
			$id=Study::filterInput('id', $request->get('id'));	
			
			$meritStudent = HonsMeritList::find($id); 
			
			// $meritStudent->delete_status=1;
			$meritStudent->delete();
			
			$message = 'You have successfully Deleted';
			
			//$hscstudents = HscMeritList::where('delete_status', 0)->paginate(Study::paginate());
			return Redirect::route('students.honours.meritlist')
			->with('warning',$message);
			
			
		}
		
		public function meritListAlldelete() {
			$title = 'Easy CollegeMate - Students HSC';	
			$breadcrumb = 'students.honours.meritlist:Merit List|Dashboard';
			
			
			
			$id=Study::filterInput('id', $request->get('id'));	
			
			HonsMeritList::truncate(); 	
			
			$message = 'You have successfully Deleted Merit List';
			
			$hscstudents = HonsMeritList::where('admitted', 0)->paginate(Study::paginate());
			return Redirect::route('students.honours.meritlist')
			->with('warning',$message);
			
			
		}
		
		
		public function meritListEditDone(Request $request) {
			$id =$request->get('id');
			$admission_roll=$request->get('admission_roll');
			$father_name=$request->get('father_name');		
			$name=$request->get('name');
			$mother_name=$request->get('mother_name');
			$faculty=$request->get('faculty');
			$gpa=$request->get('gpa');
			$select_quota=$request->get('select_quota');
			$hsc_group=$request->get('hsc_group');
			
			$meritstudent = HonsMeritList::find($id);
			
			$meritstudent->admission_roll=$admission_roll;
			$meritstudent->father_name=$father_name;
			$meritstudent->name=$name;
			$meritstudent->mother_name=$mother_name;
			$meritstudent->faculty=$faculty;
			$meritstudent->gpa=$gpa;
			$meritstudent->select_quota=$select_quota;
			$meritstudent->hsc_group=$hsc_group;
			$meritstudent->save();
			
			
			
			$title = 'Easy CollegeMate - Students HSC';	
			$breadcrumb = 'student.honours.meritlist:Merit List|Dashboard';
			
			
			
			
			
			$message = 'You have successfully Edited';
			
			$hscstudents = HonsMeritList::where('delete_status', 0)->paginate(Study::paginate());
			return Redirect::route('students.honours.meritlist')
			->withId($id)
			->with('info',$message);
			
			
		}
	}
