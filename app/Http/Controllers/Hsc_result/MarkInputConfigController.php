<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\MarkInputConfig;
use DB;
use Ecm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class MarkInputConfigController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Mark Input Config';
		$breadcrumb = 'hsc_result.marks_input_config.index:Mark Input Config|Dashboard';
		$mark_configs = MarkInputConfig::orderBy('id')->paginate(Ecm::paginate());

		return view('BackEnd.hsc_result.markinputconfig.index')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->with('mark_configs',$mark_configs);

	}



	public function create() {

		$title = 'Easy CollegeMate - Add Mark Input Config';
		$breadcrumb = 'hsc_result.marks_input_config.index:Mark Input Config List|Add Mark Input Config';

		$exam_lists = create_option_array('exams','id', 'name', 'Exam');
		return view('BackEnd.hsc_result.markinputconfig.create')
					->withTitle($title)
					->with('exam_lists',$exam_lists)
					->withBreadcrumb($breadcrumb);		

	}



	public function store(Request $request) {

		$session = $request->get('session');
		$exam_id = $request->get('exam_id');
		$exp_date = $request->get('exp_date');
        $exam_year= $request->get('exam_year');

        $data = $request->all();
		
		$validation = MarkInputConfig::validate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;

		//Insert Exam
		$exam = new MarkInputConfig;
		$exam->exam_id = $request->get('exam_id');
                $exam->exam_year= $request->get('exam_year');
		$exam->session = $request->get('session');
		$exam->exp_date = $request->get('exp_date');
		$exam->save();

		//Page
		$page = ceil(MarkInputConfig::count()/Ecm::paginate());
		$id = $exam->id;

		$message = 'You have successfully created a new mark input config';
		return Redirect::route('hsc_result.marks_input_config.index', ['page' => $page])
						->with('success',$message)
						->withId($id);		

	}



	public function show($id) {
	

	}



	public function edit($id) {

		$title = 'Easy CollegeMate - Edit Mark Input Config';
		$breadcrumb = 'hsc_result.marks_input_config.index:Mark Input Config|Edit Mark Input Config';
		$exam_lists = create_option_array('exams','id', 'name', 'Exam');
		$exam = MarkInputConfig::find($id);

		return view('BackEnd.hsc_result.markinputconfig.edit')
					->withTitle($title)
					->with('exam_lists',$exam_lists)
					->withBreadcrumb($breadcrumb)
					->withExam($exam);

	}



	public function update(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';			
			return Redirect::back()->with('error',$error_message);
		endif;

		$data = $request->all();

		$validation = MarkInputConfig::updateValidate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;

		//Update Exam
		$exam = MarkInputConfig::find($id);
		$exam->exam_id = $request->get('exam_id');
		$exam->session = $request->get('session');
        $exam->exam_year= $request->get('exam_year');
		$exam->exp_date = $request->get('exp_date');
		$exam->update();

		//Page
		$count = MarkInputConfig::where('id', '<=', $id)->count();
		$page = ceil($count/Ecm::paginate());

		$message = 'You have successfully updated mark input config';
		return Redirect::route('hsc_result.marks_input_config.index', ['page' => $page])
						->with('info',$message)
						->withId($id);

	}



	public function destroy(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;

		$mark_input_conf = MarkInputConfig::find($id);
		$mark_input_conf->delete();					

		$error_message = 'You have deleted the mark input config';
		return Redirect::back()->with('warning',$error_message);

	}
}
