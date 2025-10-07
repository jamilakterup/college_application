<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\HscMeritList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

class HscMeritListController extends Controller
{
	public function index() {
		
		$title = 'Easy CollegeMate - College Management';
		$breadcrumb = 'student.hsc:HSC|Dashboard';
		$hscstudents = HscMeritList::where('admitted', 0)->paginate(Study::paginate());
		
		return view('BackEnd.student.hsc.meritlist.index', compact('title', 'breadcrumb', 'hscstudents'));
	}
	
	public function show() {
		
		$title = 'Easy CollegeMate - College Management';
		$breadcrumb = 'student:College Management|Dashboard';
		$hscstudents = HscMeritList::where('admitted', 0)->paginate(Study::paginate());
		
		
		
		return view('student.hsc.meritlist')
		->withTitle($title)
		->withBreadcrumb($breadcrumb)
		->withHscstudents($hscstudents);
		
	}
	
	public function meritlist(Request $request)
	{
		
		$title = 'Easy CollegeMate - College Management';
		$breadcrumb = 'student.hsc:HSC|HSC Merit List';
		$ssc_roll = $request->ssc_roll;
		$query = Study::searchHscMeritlistStudent($ssc_roll);
		
		$num_rows = $query->count();
		
		$hscstudents = $query->paginate(Study::paginate());
		
		return view('BackEnd.student.admission.hsc.meritlist.index', compact('title','ssc_roll', 'breadcrumb', 'hscstudents','num_rows'));
		
	}
	
	
	public function meritlistUpload()
	{
		
		$title = 'Easy CollegeMate - College Management';
		$breadcrumb = 'students.hsc.meritlist:HSC Merit List|Upload Merit List';
		$hscstudents = HscMeritList::where('admitted', 0)->paginate(Study::paginate());
		
		
		return view('BackEnd.student.admission.hsc.meritlist.upload')
		->withTitle($title)
		->withBreadcrumb($breadcrumb);						
		
	}
	
	
	public function meritListEdit($id)
	{
		
		
		$title = 'Easy CollegeMate - College Management';
		$breadcrumb = 'students.hsc.meritlist:HSC Merit List|Upload Merit List';
		$meritstudent = HscMeritList::find($id);
		$hsc_group_list= selective_multiple_study_group();		
		
		
		
		return view('BackEnd.student.admission.hsc.meritlist.edit', compact('title', 'breadcrumb', 'hsc_group_list', 'id', 'meritstudent'));	
		
		
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
						
						if($key=='si'):
							$si=$single_area;
						endif;
						if($key=='ssc_roll'):
							$ssc_roll=$single_area;
						endif;
						if($key=='rank'):
							$rank=$single_area;
						endif;
						
						if($key=='ssc_board'):
							$ssc_board=$single_area;
						endif;
						if($key=='passing_year'):
							$passing_year=$single_area;
						endif;
						if($key=='name'):
							$name=$single_area;
						endif;
						if($key=='gpa'):
							$gpa=$single_area;
						endif;
						if($key=='select_quota'):
							$select_quota=$single_area;
						endif;
						if($key=='session'):
							$session=$single_area;
						endif;
						if($key=='app_quota'):
							$app_quota=$single_area;
						endif;
						if($key=='ssc_group'):
							$ssc_group=$single_area;
						endif;
						
					endforeach; 
					
					DB::table('hsc_merit_list')->insert(
						array('si' =>$si,'ssc_roll'=>$ssc_roll,'ssc_board'=>$ssc_board,'session'=>$session,
						'passing_year'=>$passing_year,'name'=>$name,
						'gpa'=>$gpa,'select_quota'=>$select_quota,'app_quota'=>$app_quota,'ssc_group'=>$ssc_group));
						
						
					endforeach; 
					
					$message = 'You have successfully uploaded';
					return Redirect::route('students.hsc.meritlist')
					->with('success',$message);
				}
				
				
				
				
				
				$message = 'Format Not Match';
				return Redirect::route('students.hsc.meritlist')
				->with('success',$message);
				
				
			}
			
			
			
		}
		
		public function meritlistSearch() {
			
			$title = 'Easy CollegeMate - Students HSC';	
			$breadcrumb = 'students.hsc.meritlist:Merit List|Dashboard';		
			$ssc_roll = Study::filterInput('ssc_roll', $request->get('ssc_roll'));	
			
			$hscstudents = Study::searchHscMeritlistStudent( $ssc_roll);
			
			
			return view('student.hsc.meritlist')
			->withTitle($title)
			->withBreadcrumb($breadcrumb)
			->withHscstudents($hscstudents);
			
		}
		
		public function meritlistFormatDownload() {
			
			return Response::download(public_path().'/csv/hsc_meritlist_format.csv');
			
		}
		
		
		public function meritlistSingleDelete(Request $request) {
			
			$title = 'Easy CollegeMate - Students HSC';	
			$breadcrumb = 'students.hsc.meritlist:Merit List|Dashboard';
			
			
			
			$id=Study::filterInput('id', $request->get('id'));	
			
			$meritStudent = HscMeritList::find($id); 
			
			$meritStudent->delete();
			
			$message = 'You have successfully Deleted';
			
			$hscstudents = HscMeritList::where('admitted', 0)->paginate(Study::paginate());
			return Redirect::route('students.hsc.meritlist')
			->with('warning',$message);
			
			
		}
		
		public function meritListAlldelete(Request $request) {
			
			$title = 'Easy CollegeMate - Students HSC';	
			$breadcrumb = 'students.hsc.meritlist:Merit List|Dashboard';
			
			
			
			$id=Study::filterInput('id', $request->get('id'));	
			
			HscMeritList::truncate(); 	
			
			$message = 'You have successfully Deleted Merit List';
			
			$hscstudents = HscMeritList::where('admitted', 0)->paginate(Study::paginate());
			return Redirect::route('students.hsc.meritlist')
			->with('warning',$message);
			
			
		}
		
		
		public function meritListEditDone(Request $request) {
			
			$id=$request->get('id');
			$ssc_roll=$request->get('ssc_roll');		
			$rank=$request->get('rank');
			$ssc_board=$request->get('ssc_board');
			$ssc_group=$request->get('ssc_group');
			$passing_year=$request->get('passing_year');
			$name=$request->get('name');
			$gpa=$request->get('gpa');
			$select_quota=$request->get('select_quota');
			$father_name=$request->get('father_name');
			$mother_name=$request->get('mother_name');
			
			$meritstudent = HscMeritList::find($id);
			
			$meritstudent->ssc_roll=$ssc_roll;
			$meritstudent->rank=$rank;
			$meritstudent->ssc_board=$ssc_board;
			$meritstudent->ssc_group=$ssc_group;
			$meritstudent->passing_year=$passing_year;
			$meritstudent->name=$name;
			$meritstudent->gpa=$gpa;
			$meritstudent->select_quota=$select_quota;
			$meritstudent->m_name=$mother_name;
			$meritstudent->f_name=$father_name;
			$meritstudent->save();
			
			
			
			$title = 'Easy CollegeMate - Students HSC';	
			$breadcrumb = 'students.hsc.meritlist:Merit List|Dashboard';
			
			
			
			
			
			$message = 'You have successfully Edited';
			
			$hscstudents = HscMeritList::where('admitted', 0)->paginate(Study::paginate());
			return Redirect::route('students.hsc.meritlist')
			->with('id', $id)
			->with('success',$message);
			
			
		}
		
		
		public function search() {
			
			$title = 'Easy CollegeMate - Students HSC';
			$breadcrumb = 'student:College Management|Dashboard';	
			
			//Search Material
			$id = Study::filterInput('id', $request->get('id'));
			$ssc_roll = Study::filterInput('ssc_roll', $request->get('ssc_roll'));
			$groups = Study::filterInput('groups', $request->get('groups'));
			$current_level = Study::filterInput('current_level', $request->get('current_level'));
			$session = Study::filterInput('session', $request->get('session'));
			
			
			
			
			$hscstudents = Study::searchHscStudent($id, $ssc_roll, $groups, $current_level, $session);
			
			
			$group_lists = ['' => 'Select Group','science' => 'Science','arts' => 'Humanities','commerce' => 'Business Studies'];
			
			$current_level_lists = ['' => 'Study Level','HSC 1st year' => 'HSC 1st year','HSC 2nd year' => 'HSC 2nd year'];
			
			$session_lists = ['' => 'Select Session','2011-2012' => '2011-2012','2012-2013' => '2012-2013','2013-2014' => '2013-2014','2015-2016' => '2015-2016','2016-2017' => '2016-2017','2017-2018' => '2017-2018','2018-2019' => '2018-2019'];
			
			return view('student.hsc.index')
			->withTitle($title)
			->withBreadcrumb($breadcrumb)
			->withHscstudents($hscstudents)
			->withCurrent_level_lists($current_level_lists)	
			->withSession_lists($session_lists)										
			->withGroup_lists($group_lists);
			
		}
	}
