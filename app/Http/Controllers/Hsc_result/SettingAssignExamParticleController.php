<?php

namespace App\Http\Controllers\Hsc_result;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\ClassSubject;
use App\Models\Classe;
use App\Models\ConfigExamParticle;
use App\Models\SubjectParticle;
use App\Models\Xmparticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Ecm;

class SettingAssignExamParticleController extends Controller
{
    public function index() {

		$title = 'Easy CollegeMate - Assign Exam Particle';
		$breadcrumb = 'hsc_result.assign_exam_particle.index:Subject Exam Particle|Dashboard';
		$classes = Classe::orderBy('id')->paginate(Ecm::paginate());

		return view('BackEnd.hsc_result.assign_exam_particle.index')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->withClasses($classes);
					
	}



	public function edit($class_id,$department_id,$subject_id) {

		$title = 'Easy CollegeMate - Assign Exam Particle';
		$breadcrumb = 'hsc_result.assign_exam_particle.index:Subject Exam Particle|Assign Exam Particle';		
		$xmparticles = Xmparticle::orderBy('id')->get();		
        $exam_subjects = SubjectParticle::where('classe_id',$class_id)->where('group_id',$department_id)->where('subject_id',$subject_id)->get();
	
		return view('BackEnd.hsc_result.assign_exam_particle.edit')
					->withTitle($title)
					->withBreadcrumb($breadcrumb)
					->with('class_id',$class_id)
					->with('department_id',$department_id)
					->with('subject_id',$subject_id)					
					->withXmparticles($xmparticles)
					->with('exam_subjects',$exam_subjects);		

	}



	public function update(Request $request, $class_id,$department_id,$subject_id) {
		
		if($class_id !== $request->get('class_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;		

		if($department_id !== $request->get('department_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		if($subject_id !== $request->get('subject_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;				
		$class_has_department = ClassGroup::where('classe_id',$class_id)->where('group_id',$department_id)->count();

		if($class_has_department == 0) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		$class_has_subject = ClassSubject::where('classe_id',$class_id)->where('subject_id',$subject_id)->count();

		if($class_has_subject == 0) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	
		if($request->get('total_number')=='') :
			$error_message = 'Please Enter Total Subject Marks';
			return Redirect::back()->with('error',$error_message);
		endif;
		if($request->get('total_pass')=='') :
			$error_message = 'Please Enter Total Pass Marks';
			return Redirect::back()->with('error',$error_message);
		endif;
		if($request->get('total_converted')=='') :
			$error_message = 'Please Enter Conversion Marks';
			return Redirect::back()->with('error',$error_message);
		endif;
		$total_number = $request->get('total_number');
		$total_pass = $request->get('total_pass');
		$total_converted = $request->get('total_converted');
		$xmparticles_id = [];
		$xmparticles = Xmparticle::get();
		if($xmparticles->count() > 0) :
			foreach($xmparticles as $xmparticle) :
				$xmparticle_id = $xmparticle->id;

				if($request->get('examparticle-' . $xmparticle_id) == $xmparticle_id) :
					
					if(ctype_digit($request->get('total-' . $xmparticle_id)) == false) :
						$error_message = 'The total value must be an integer';
						return Redirect::back()->with('error',$error_message);
					endif;	

					if($request->get('total-' . $xmparticle_id) <= 0) :
						$error_message = 'The total must be at least 1.';
						return Redirect::back()->with('error',$error_message);
					endif;						

					if(ctype_digit($request->get('pass-' . $xmparticle_id)) == false) :
						$error_message = 'The pass value must be an integer';
						return Redirect::back()->with('error',$error_message);
					endif;	

						

					$xmparticles_id[] = $xmparticle_id;

				endif;	
			endforeach;


			//Delete & Insert = Update ConfigExamParticle
			ConfigExamParticle::where('classe_id',$class_id)->where('group_id',$department_id)->where('subject_id',$subject_id)->delete();			

			SubjectParticle::where('classe_id',$class_id)->where('group_id',$department_id)->where('subject_id',$subject_id)->delete();			
			
			if(count($xmparticles_id) > 0) :
				foreach($xmparticles_id as $xmparticle_id) :
					$total = 'total-' . $xmparticle_id;
					$pass = 'pass-' . $xmparticle_id;
					$pert = 'per-' . $xmparticle_id;
					$per= $request->get($pert);
					$total= $request->get($total);

					$par_total=($total*$per)/100;
					//return $par_total;
					$data_array = ['classe_id' => $class_id, 'group_id' => $department_id, 'subject_id' => $subject_id, 'xmparticle_id' => $xmparticle_id, 'total' => $total, 'pass' => $request->get($pass),'per_centage' =>$request->get($pert),'particle_convert' => $par_total,'pass_particle_convert'=>($request->get($pass)*$per)/100];
					ConfigExamParticle::create($data_array);
				endforeach;	
			endif;
            $data_array2 = ['classe_id' => $class_id, 'group_id' => $department_id, 'subject_id' => $subject_id,'total'=>$total_number, 'total_pass'=>$total_pass, 'total_converted'=>$total_converted];	
			SubjectParticle::create($data_array2);			
		endif;

		$message = 'You have successfully updated exam particle of the class subject';		
		return Redirect::route('hsc_result.assign_exam_particle.index')
						->with('info',$message)
						->withId($class_id)
						->with('department_id',$department_id)
						->with('subject_id',$subject_id);

	}



	public function destroy(Request $request, $class_id,$department_id,$subject_id) {

		if($class_id !== $request->get('class_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;		

		if($department_id !== $request->get('department_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		if($subject_id !== $request->get('subject_id')) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;			

		$class_has_department = ClassGroup::where('classe_id',$class_id)->where('group_id',$department_id)->count();

		if($class_has_department == 0) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;	

		$class_has_subject = ClassSubject::where('classe_id',$class_id)->where('subject_id',$subject_id)->count();

		if($class_has_subject == 0) :
			$error_message = 'Something went wrong! Please try again';
			return Redirect::back()->with('error',$error_message);
		endif;		

		ConfigExamParticle::where('classe_id',$class_id)->where('department_id',$department_id)->where('subject_id',$subject_id)->delete();

		$error_message = 'You have unassigned all exam particle from the subject';
		return Redirect::back()->with('warning',$error_message);			

	}
}
