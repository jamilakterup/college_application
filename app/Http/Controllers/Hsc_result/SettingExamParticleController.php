<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\Xmparticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Ecm;
use Esm;

class SettingExamParticleController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Exam Particles';
		$breadcrumb = 'hsc_result.examparticle.index:Exam Particle|Dashboard';
		$xmparticles = Xmparticle::orderBy('id')->paginate(Ecm::paginate());
		
		return view('BackEnd.hsc_result.examparticle.index')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withXmparticles($xmparticles);

	}



	public function create() {

		$title = 'Easy CollegeMate - Add Exam Particles';
		$breadcrumb = 'hsc_result.examparticle.index:Exam Particle|Add Exam Particle';

		return view('BackEnd.hsc_result.examparticle.create')
					->withTitle($title)
					->withBreadcrumb($breadcrumb);

	}



	public function store(Request $request) {

		$data = $request->all();
		$validation = Xmparticle::validate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;

		//Insert Xmparticle
		$xmparticle = new Xmparticle;
		$xmparticle->name = $request->get('name');
		$xmparticle->short_name = $request->get('short_name');
		$xmparticle->total = $request->get('total');
		$xmparticle->pass = $request->get('pass');
		$xmparticle->save();

		//Page
		$page = ceil(Xmparticle::count()/Esm::paginate());
		$id = $xmparticle->id;

		$message = 'You have successfully created a new exam particle';
		return Redirect::route('hsc_result.examparticle.index', ['page' => $page])
						->with('success',$message)
						->withId($id);	

	}



	public function show($id) {

		$exist = Xmparticle::whereId($id)->count();

		if($exist == 0) :
			$error_message = 'There is no exam with this id';
			return Redirect::route('setting.examparticle.index')->with('error',$error_message);
		endif;	

		$xmparticle = Xmparticle::find($id);

		$title = 'Easy CollegeMate - Exam Particle - ' . $xmparticle->name;
		$breadcrumb = 'hsc_result.examparticle.index:Exam Particle|Exam Particle - ' . $xmparticle->name;

		return view('hsc_result.examparticle.show')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withXmparticle($xmparticle);

	}



	public function edit($id) {

		$title = 'Easy CollegeMate - Edit Exam Particle';
		$breadcrumb = 'hsc_result.examparticle.index:Exam Particle|Edit Exam Particle';
		$xmparticle = Xmparticle::find($id);

		return view('BackEnd.hsc_result.examparticle.edit')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withXmparticle($xmparticle);

	}



	public function update(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';			
			return Redirect::back()->with('error',$error_message);
		endif;	

		$data = $request->all();
		$validation = Xmparticle::updateValidate($data);

		if($validation->fails()) :
			return Redirect::back()->withInput()->withErrors($validation);
		endif;

		$xmparticle = Xmparticle::find($id);
		$xmparticle->name = $request->get('name');
		$xmparticle->short_name = $request->get('short_name');
		$xmparticle->total = $request->get('total');
		$xmparticle->pass = $request->get('pass');
		$xmparticle->update();

		//Page
		$count = Xmparticle::where('id', '<=', $id)->count();
		$page = ceil($count/Ecm::paginate());

		$message = 'You have successfully updated the exam particle';
		return Redirect::route('hsc_result.examparticle.index', ['page' => $page])
						->with('info',$message)
						->withId($id);

	}



	public function destroy(Request $request, $id) {

		if($id !== $request->get('id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;

		$xmparticle = Xmparticle::find($id);
		$xmparticle->delete();

		$error_message = 'You have deleted the exam particle';
		return Redirect::back()->with('warning',$error_message);

	}
}
