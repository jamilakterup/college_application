<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use App\Models\Exam;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\StudentInfoHsc;
use App\Models\AdmitCardPublish;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('frontend.index');
    }

    public function student_instruction(Request $request){
        $type = $request->type;
        $course = $request->course;

        if($course == 'hsc' && $type == 'admission'){
            $template = config('settings.hsc_adm_instruction');
        }

        return response()->json([
            'status' => 202,
            'modal' => 'modal-lg',
            'html' => $template

        ],Response::HTTP_OK);
    }

    public function getAdmitCard(Request $request){
        if($request->isMethod('post')){
            $this->validate($request, [
                'student_id' => 'required',
                'level' => 'required',
                'exam' => 'required',
            ]);
            $student_id = request()->get('student_id');
            $current_level = request()->get('level');
            $exam_id = request()->get('exam');
            $exam_name=Exam::find($exam_id);
            $curr_level=Classe::find($current_level);
            $request->flash();
            $publish = AdmitCardPublish::where('level', $current_level)->where('exam_id', $exam_id)->where('open', 1)->first();
            if(\is_null($publish) ){
                $error_message = 'Admit Card not Published Yet!';
                return Redirect::back()->withInput()->with('error',$error_message);
            }

            $students = StudentInfoHsc::where('current_level',$curr_level->name)->where('id', $student_id)->get();
            if(\count($students) < 1)
                return \redirect()->back()->with('error', 'No Student Found');
            $student = $students->first();
            if($publish->session != $student->session)
                return \redirect()->back()->with('error', 'Admit Card not Published Yet!');
            
            return view('frontend.pages.admit_card.search', compact('student', 'publish'));
        }
        return view('frontend.pages.admit_card.index');
    }

    public function downloadAdmitCard(Request $request){
        return AdmitCardPublish::downloadAdmitCard();
    }
}
