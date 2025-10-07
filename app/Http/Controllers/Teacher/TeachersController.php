<?php

namespace App\Http\Controllers\Teacher;

use DB;
use Ecm;
use Mpdf\Mpdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\TeacherPersonal;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;

class TeachersController extends Controller
{
    public function index() {
		return view('BackEnd.teacher.teacher.index');
	}

    public function datasource(Request $request){
        $records = TeacherPersonal::query();
  
        return DataTables::of($records)
            ->addColumn('actions','BackEnd.teacher.particles.action_buttons')
  
            ->editColumn('details', function($records){
                $html = "<button data-href='".route('teacher.teacherdetails', ['id'=> $records->id])."' data-action='create' onclick=\"getAjaxModalData(this, 'Teacher Details')\" class='btn btn-sm btn-info'>
                    <i class='fa fa-eye'></i>
                    Details
                </button>";
                return $html;
            })
            ->editColumn('pds', function($records){
                $html = "<a href='".route('teacher.teacherpds', ['id'=> $records->id])."' class='btn btn-sm btn-primary' target='__blank'><i class='fa fa-download'></i></a>";
                return $html;
            })
            ->editColumn('release', function($records){
                $html = "<button data-href='".route('teacher.releaseteacher',['id'=> $records->id])."' data-action='create' onclick=\"getAjaxModalData(this, 'Teacher Release')\" class='btn btn-sm btn-danger'>
                    <i class='fa fa-arrow-right'></i>
                </button>";
                return $html;
            })

            ->editColumn('joining_letter', function($records){
                $html = "<a target='__blank' href='".route('teacher.printJoinLetter',['id'=> $records->id])."' class='btn btn-sm btn-info'>
                    <i class='fa fa-print'></i>
                </a>";
                return $html;
            })
            ->editColumn('release_letter', function($records){
                $html = "<a target='__blank' href='".route('teacher.printReleaseLetter',['id'=> $records->id])."' class='btn btn-sm btn-info'>
                    <i class='fa fa-print'></i>
                </a>";
                return $html;
            })
            ->editColumn('edit', function($records){
                $html = "<button data-href='".route('teacher.editTeacher',['id'=> $records->id])."' onclick=\"dtRowLocation(this)\" class='btn btn-sm btn-warning'>
                    <i class='fa fa-pencil'></i>
                </button>";
                return $html;
            })
            ->editColumn('delete', function($records){
                $html = "<button data-href='".route('teacher.deleteTeacher',['id'=> $records->id])."' data-action='delete' onclick=\"deleteAjaxData(this, 'Delete This Teacher')\" class='btn btn-sm btn-danger'>
                    <i class='fa fa-trash'></i>
                </button>";
                return $html;
            })
            ->setRowAttr([
                'data-row-id' => function($records) {
                    return $records->id;
                },
            ])
            ->rawColumns(['status','details','pds', 'release','joining_letter','release_letter','edit','delete'])
            ->escapeColumns(['details','pds', 'release','joining_letter','release_letter','edit','delete'])
            ->make(true);
    }

    public function create(){
        return view('BackEnd.teacher.teacher.create');
    }

    public function store(Request $request){

        $this->validate($request, [
            'teacher_id' => 'required|numeric|unique:teacher_personal,id',
            'name' => 'required',
            'personal_mobile' => 'required|numeric',
            'department' => 'required',
            'position' => 'required',
            'blood_group' => 'required',
            'district' => 'required',
            'incoming_college' => 'required',
            'reference_no' => 'required',
            'joining_date' => 'required'
        ]);

        $id=htmlspecialchars($request->teacher_id);
        $name=htmlspecialchars($request->name);
        $blood_group=htmlspecialchars($request->blood_group);

        $personal_mobile=htmlspecialchars($request->personal_mobile);

        $department=htmlspecialchars($request->department);
        $position=htmlspecialchars($request->position);
        $district=htmlspecialchars($request->district);

        $incoming_college=htmlspecialchars($request->incoming_college);


        $reference_no=htmlspecialchars($request->reference_no);
        $join_date=htmlspecialchars($request->joining_date);
        $join_date=date('Y-m-d',strtotime($join_date));
        $join_time=date('H:i:s');



        $results=DB::select('select * from teacher_career WHERE id="'.$position.'"');
        foreach($results as $result)
        {
            $position_name=$result->name;
            break;
        }

        DB::table('teacher_personal')->insert(
            ['id' => $id, 'name' => $name, 'personal_mobile' => $personal_mobile, 'home_district' => $district, 'incoming_college' => $incoming_college, 'department' =>$department, 'position' => $position, 'reference_no' =>$reference_no,'join_date' =>$join_date ,'join_time'=>$join_time, 'blood_group' =>$blood_group ]
        );

        DB::table('teacher_education')->insert(
            ['id' => $id]
        );
        DB::table('teacher_employment')->insert(
            ['id' => $id]
        );

        DB::table('teacher_appointment')->insert(
            ['id' => $id]
        );
        DB::table('teacher_career')->insert(
            ['id' => $id]
        );
        $password = Hash::make($id);

        DB::table('users')->insert(
            ['username' => $id, 'password' => $password, 'role_name' => 'teacher', 'status' => 'active', 'user_type' => 'individual', 'individual_type' =>'teacher', 'tracking_id' => $id, 'email'=> $id]
        );

        //Page
		$page = ceil(DB::table('teacher_personal')->count()/Ecm::paginate());

        $message = 'Teacher is joined successfully';
		return Redirect::route('teacher.index', ['page' => $page])
						->with('success',$message)
						->withId($id);	
    }

    public function deleteTeacher(Request $request){

        DB::beginTransaction();
        try {
            $id=htmlspecialchars($request->id);
            DB::table('teacher_personal')->where('id', $id)->delete();
            DB::table('teacher_education')->where('id', $id)->delete();
            DB::table('teacher_employment')->where('id', $id)->delete();
            DB::table('teacher_appointment')->where('id', $id)->delete();
            DB::table('teacher_career')->where('id', $id)->delete();
            DB::table('users')->where('username', $id)->delete();

            return response()->json([
                'status' => 'warning',
                'message' => 'Teacher Deleted Successfully',
                'id' => $id,
                'table' => 'teacherDatatable'

            ],Response::HTTP_OK);
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return response()->json([
                'error' => $e->errorInfo[2]
            ],Response::HTTP_BAD_REQUEST);
        }
    
    }

    public function teacherdetails(Request $request){
        $id=$request->get('id');
        $details = TeacherPersonal::with('teacherEmployment', 'teacherEducation', 'teacherAppointment', 'teacherCareer')->find($id);
        $education = $details->teacherEducation;
        $employment = $details->teacherEmployment;
        $appointment = $details->teacherAppointment;
        $career = $details->teacherCareer;

        $html = view('BackEnd.teacher.teacher.particles.teacher_details', \compact('details', 'education','employment', 'appointment','career'))->render();

        return response()->json([
            'html' => $html,
            'static' => true,
        ],Response::HTTP_OK);

    }

    public function teacherpds(Request $request){
        $id=$request->get('id');
        $details = TeacherPersonal::with('teacherEmployment', 'teacherEducation', 'teacherAppointment', 'teacherCareer')->find($id);
        $education = $details->teacherEducation;
        $employment = $details->teacherEmployment;
        $appointment = $details->teacherAppointment;
        $career = $details->teacherCareer;

        $res = $this->getTeacherEduArray($details);
        $trainings = $res['trainings'];
        $qualifications = $res['qualifications'];

        return view('BackEnd.teacher.teacher.particles.pds_teacher', \compact('details', 'education','employment', 'appointment','career', 'trainings', 'qualifications'));
    }

    public function releaseteacher(Request $request){
        $relinfo = $request->all();
        $teacher = TeacherPersonal::with('teacherEmployment', 'teacherEducation', 'teacherAppointment', 'teacherCareer')->find($request->id);
        $html = view('BackEnd.teacher.teacher.particles.release_teacher', \compact('relinfo', 'teacher'))->render();
        return response()->json([
            'html' => $html,
            'static' => true,
        ],Response::HTTP_OK);
    }

    public function releaseteacherStore(Request $request){
        $this->validate($request, [
            'release_ref' => 'required',
            'outgoing_college'=> 'required',
            'release_date'=> 'required|date',
            'release_time' => 'required'
        ]);

        $reference_no = $request->release_ref;
        $outgoing_college = $request->outgoing_college;
        $release_date = $request->release_date;
        $release_time = $request->release_time;
        $release_date=date('Y-m-d',strtotime($release_date));
        $release_time=date('H:i:s',strtotime($release_time));
        $teacher_id = $request->teacher_id;

        $teacher = TeacherPersonal::find($teacher_id);
        $teacher->release_reference_no = $reference_no;
        $teacher->release_date = $release_date;
        $teacher->release_time = $release_time;
        $teacher->outgoing_college = $outgoing_college;
        $teacher->save();

        return response()->json([
            'status' => 'success',
            'message' => "Teacher Released Successfully for *<b>$teacher->id :$teacher->name</b>",
            'id' => $teacher->id,
            'table' => 'teacherDatatable'
        ],Response::HTTP_OK);
    }

    public function printReleaseLetter(Request $request){
        $id=$request->get('id');
        $teacher = TeacherPersonal::with('teacherEmployment', 'teacherEducation', 'teacherAppointment', 'teacherCareer')->find($id);
        
        $html = view('BackEnd.teacher.teacher.pdf.print_release_letter', \compact('teacher'));

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 14,'times']);
        \addMpdfPageSetup($mpdf);
        $mpdf->writeHTML($html);
        $filename = $teacher->release_reference_no."_release_letter.pdf";
        $mpdf->Output($filename, 'I');
    }

    public function printJoinLetter(Request $request){
        $id=$request->get('id');
        $teacher = TeacherPersonal::with('teacherEmployment', 'teacherEducation', 'teacherAppointment', 'teacherCareer')->find($id);

        $html = view('BackEnd.teacher.teacher.pdf.print_join_letter', \compact('teacher'));
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 14,'times']);
        \addMpdfPageSetup($mpdf);
        $mpdf->writeHTML($html);
        $filename = $teacher->release_reference_no."_release_letter.pdf";
        $mpdf->Output($filename, 'I');
    }

    public function editTeacher(Request $request){
        $id=$request->get('id');
        $teacher = TeacherPersonal::with('teacherEmployment', 'teacherEducation', 'teacherAppointment', 'teacherCareer')->find($id);

        $res = $this->getTeacherEduArray($teacher);
        $trainings = $res['trainings'];
        $qualifications = $res['qualifications'];

        return view('BackEnd.teacher.teacher.edit_teacher', \compact('teacher', 'qualifications', 'trainings'));
    }

    public function getTeacherEduArray($teacher){
        $teacherEdu = $teacher->teacherEducation;

        // qualification
        $edu_levels = explode(',', $teacherEdu->education_level);
        $exam_titles = explode(',', $teacherEdu->exam_titles);
        $major_groups = explode(',', $teacherEdu->major_group);
        $institute_names = explode(',', $teacherEdu->institute_name);
        $results = explode(',', $teacherEdu->result);
        $marks = explode(',', $teacherEdu->marks);
        $passing_years = explode(',', $teacherEdu->passing_year);
        $durations = explode(',', $teacherEdu->duration);
        $achievements = explode(',', $teacherEdu->achievement);
        $qualifications = [];

        if(count($edu_levels) < 1)
            $edu_levels = ['' => 'Select'];

        foreach ($edu_levels as $i => $level){
            $qualifications[$i] = [
                'edu_level' => $level,
                'exam_title' => $exam_titles[$i] ?? null,
                'group' => $major_groups[$i] ?? null,
                'institute_name' => $institute_names[$i] ?? null,
                'result' => $results[$i] ?? null,
                'marks' => $marks[$i] ?? null,
                'passing_year' => $passing_years[$i] ?? null,
                'duration' => $durations[$i] ?? null,
                'achieve' => $achievements[$i] ?? null,
            ];
        }

        // training data
        $batch_no = explode(',', $teacherEdu->batch_no);
        $training_title = explode(',', $teacherEdu->training_title);
        $training_topics = explode(',', $teacherEdu->training_topics);
        $training_institute = explode(',', $teacherEdu->training_institute);
        $training_country = explode(',', $teacherEdu->training_country);
        $training_location = explode(',', $teacherEdu->training_location);
        $training_year = explode(',', $teacherEdu->training_year);
        $training_from = explode(',', $teacherEdu->training_from);
        $training_to = explode(',', $teacherEdu->training_to);
        $training_period = explode(',', $teacherEdu->training_period);
        $trainings = [];
        if(count($training_title) < 1)
            $trainings = ['' => 'Select'];

        foreach($training_title as $i => $title){
            $trainings[$i] = [
                'batch_no' => $batch_no[$i] ?? null,
                'training_title' => $training_title[$i] ?? null,
                'training_topics' => $training_topics[$i] ?? null,
                'training_institute' => $training_institute[$i] ?? null,
                'training_country' => $training_country[$i] ?? null,
                'training_location' => $training_location[$i] ?? null,
                'training_year' => $training_year[$i] ?? null,
                'training_from' => $training_from[$i] ?? null,
                'training_to' => $training_to[$i] ?? null,
                'training_period' => $training_period[$i] ?? null,
            ];
        }

        return ['qualifications' => $qualifications, 'trainings'=> $trainings];
    }

    public function editTeacherPersonal(Request $request){

        $perInfo=$request->all();

        if($request->get('personal_edit')=='true'){
            
            $id=$request->get('id');
            $teacher = TeacherPersonal::find($id);

            $destination = 'college/teacher/';

            $fileName = \fileUpdate($teacher->image, $request->image, $destination);

            $name=htmlspecialchars($request->get('name'));
            $father_name=htmlspecialchars($request->get('father_name'));
            $mother_name=htmlspecialchars($request->get('mother_name'));
            $birth_date=htmlspecialchars($request->get('birth_date'));
            $birth_date=date('Y-m-d',strtotime($birth_date));
            $gender=htmlspecialchars($request->get('gender'));
            $marital_status=htmlspecialchars($request->get('marital_status'));
            $nationality=htmlspecialchars($request->get('nationality'));
            $religion=htmlspecialchars($request->get('religion'));
            $present_address=htmlspecialchars($request->get('present_address'));
            $permanent_address=htmlspecialchars($request->get('permanent_address'));
            $home_district=htmlspecialchars($request->get('home_district'));
            $phone_office=htmlspecialchars($request->get('phone_office'));
            $phone_home=htmlspecialchars($request->get('phone_home'));
            $personal_mobile=htmlspecialchars($request->get('personal_mobile'));
            $email=htmlspecialchars($request->get('email'));
            $alternate_email=htmlspecialchars($request->get('alternate_email'));
            $spouse_name=htmlspecialchars($request->get('spouse_name'));
            $relation=htmlspecialchars($request->get('relation'));
            $spouse_mobile=htmlspecialchars($request->get('spouse_mobile'));
            $spouse_phone=htmlspecialchars($request->get('spouse_phone'));

            DB::table('teacher_personal')
                    ->where('id', $id)  
                    ->limit(1)  // optional - to ensure only one record is updated.
                    ->update(array('name'=>$name,'father_name'=>$father_name,'mother_Name'=>$mother_name,'birth_date'=>$birth_date, 'gender'=>$gender,'marital_status'=>$marital_status,'nationality'=>$nationality,'religion'=>$religion, 'present_address'=>$present_address,'permanent_address'=>$permanent_address,'home_district'=>$home_district,'phone_office'=>$phone_office,'phone_home'=>$phone_home,'personal_mobile'=>$personal_mobile,'email'=>$email,'alternate_email'=>$alternate_email,
                    'image' => $fileName,
                    'spouse_name'=>$spouse_name,
                    'relation'=>$relation,
                    'spouse_mobile'=>$spouse_mobile,
                    'spouse_phone'=>$spouse_phone
                )); 
            
            \session()->flash('tab', 1);
            \session()->flash('isUpdated', true);

            return \redirect()->back()->with('info', 'Teacher Personal Info Updated Successfully');
        }
        
    }

    public function ajaximageTeacher(Request $request){
        $id = $request->get('id');

        $logo = $request->file('photoimg');
        $filename = rand(1, 999999999) . $logo->getClientOriginalName();
        $upload_path = public_path('upload/college/' . $filename);
        $db_path = 'upload/college/' . $filename;

		Image::make($logo->getRealPath())->save($upload_path);
        DB::table('teacher_personal')
            ->where('id', $id)  
            ->limit(1)  // optional - to ensure only one record is updated.
            ->update(array('image'=>$filename));
	
		echo '<img src="'. URL::to('/') .'/upload/college/' . $filename . '"/>';
    }

   public function editTeachereducationinput(Request $request)
   {
        $id = $request->get('id');
        // qualification
        $edu_level = implode(',',filter_empty_array($request->get('edu_level')));
        $exam_title = implode(',',filter_empty_array($request->get('exam_title')));
        $group = implode(',',filter_empty_array($request->get('group')));
        $institute_name = implode(',',filter_empty_array($request->get('institute_name')));
        $result = implode(',',filter_empty_array($request->get('result')));
        $marks = implode(',',filter_empty_array($request->get('marks')));
        $passing_year = implode(',',filter_empty_array($request->get('passing_year')));
        $duration = implode(',',filter_empty_array($request->get('duration')));
        $achieve = implode(',',filter_empty_array($request->get('achieve')));

        $batch_no=implode(',',filter_empty_array($request->get('batch_no')));
        $training_title=implode(',',filter_empty_array($request->get('training_title')));
        $training_topics=implode(',',filter_empty_array($request->get('training_topics')));
        $training_institute=implode(',',filter_empty_array($request->get('training_institute')));
        $training_country=implode(',',filter_empty_array($request->get('training_country')));
        $training_location=implode(',',filter_empty_array($request->get('training_location')));
        $training_year=implode(',',filter_empty_array($request->get('training_year')));
        $training_from=implode(',',filter_empty_array($request->get('training_from')));
        $training_to=implode(',',filter_empty_array($request->get('training_to')));
        $training_period=implode(',',filter_empty_array($request->get('training_period')));



        $regular=htmlspecialchars($request->get('regular'));
        $regular_date=htmlspecialchars($request->get('regular_date'));
        $regular_date=date('Y-m-d',strtotime($regular_date));

        $gazette_date=htmlspecialchars($request->get('gazette_date'));
        $gazette_date=date('Y-m-d',strtotime($gazette_date));

        $status=htmlspecialchars($request->get('status'));
        $permanent_date=htmlspecialchars($request->get('permanent_date'));
        $permanent_date=date('Y-m-d',strtotime($permanent_date));                                
        $paper_pass=htmlspecialchars($request->get('paper_pass'));
        $finalpass_date=htmlspecialchars($request->get('finalpass_date'));
        $finalpass_date=date('Y-m-d',strtotime($finalpass_date));
        $award_date=htmlspecialchars($request->get('award_date'));
        $award_date=date('Y-m-d',strtotime($award_date));

        $prof_certificate=htmlspecialchars($request->get('prof_certificate'));
        $prof_institute=htmlspecialchars($request->get('prof_institute'));
        $prof_location=htmlspecialchars($request->get('prof_location'));
        $prof_from=htmlspecialchars($request->get('prof_from'));
        $prof_from=date('Y-m-d',strtotime($prof_from));
        $prof_to=htmlspecialchars($request->get('prof_to'));
        $prof_to=date('Y-m-d',strtotime($prof_to));

        DB::table('teacher_education')
                ->where('id', $id)  
                ->limit(1)  // optional - to ensure only one record is updated.
                ->update(array('education_level'=>$edu_level,'exam_title'=>$exam_title,'major_group'=>$group,'institute_name'=>$institute_name
                    ,'result'=>$result,'marks'=>$marks,'passing_year'=>$passing_year,'duration'=>$duration,'achievement'=>$achieve
                    ,'batch_no'=>$batch_no,'training_title'=>$training_title,'training_topics'=>$training_topics,'training_institute'=>$training_institute
                    ,'training_country'=>$training_country,'training_location'=>$training_location,'training_year'=>$training_year
                    ,'training_from'=>$training_from,'training_to'=>$training_to,'training_period'=>$training_period,'regular'=>$regular
                    ,'regular_date'=>$regular_date,'gazette_date'=>$gazette_date,'status'=>$status,'permanent_date'=>$permanent_date
                    ,'paper_pass'=>$paper_pass,'finalpass_date'=>$finalpass_date,'award_date'=>$award_date
                    ,'prof_certificate'=>$prof_certificate,'prof_institute'=>$prof_institute,'prof_location'=>$prof_location
                    ,'prof_from'=>$prof_from,'prof_to'=>$prof_to));

        \session()->flash('tab', 2);
        \session()->flash('isUpdated', true);
        return \redirect()->back()->with('info', 'Teacher Education Info Updated Successfully');
   }

    public function editTeacheremploymentinput(Request $request){
        $id= $request->get('id');
        $employer_name=htmlspecialchars($request->get('employer_name'));
        $employer_district=htmlspecialchars($request->get('employer_district'));
        $employer_thana=htmlspecialchars($request->get('employer_thana'));
        $nature_position=htmlspecialchars($request->get('nature_position'));
        $held_position=htmlspecialchars($request->get('held_position'));
        $original_position=htmlspecialchars($request->get('original_position'));
        $dept_name=htmlspecialchars($request->get('dept_name'));
        $office=htmlspecialchars($request->get('office'));
        $responsibility=htmlspecialchars($request->get('responsibility'));
        $payment_scale=htmlspecialchars($request->get('payment_scale'));
        $present_salary=htmlspecialchars($request->get('present_salary'));
        $joining_date=htmlspecialchars($request->get('joining_date'));
        $joining_date=date('Y-m-d',strtotime($joining_date));
        $ending_date=htmlspecialchars($request->get('ending_date'));
        $ending_date=date('Y-m-d',strtotime($ending_date));
        $from_date=htmlspecialchars($request->get('from_date'));
        $from_date=date('Y-m-d',strtotime($from_date));
        $to_date=htmlspecialchars($request->get('to_date'));
        $to_date=date('Y-m-d',strtotime($to_date));
        $to_continue=htmlspecialchars($request->get('to_continue'));
        $service_area=htmlspecialchars($request->get('service_area'));
        
        DB::table('teacher_employment')
                ->where('id', $id)  
                ->limit(1)  // optional - to ensure only one record is updated.
                ->update(array(
                    'employer_name'=>$employer_name,
                    'employer_district'=>$employer_district,
                    'employer_thana'=>$employer_thana,
                    'nature_position'=>$nature_position,
                    'held_position'=>$held_position,
                    'office'=>$office,
                    'responsibility'=>$responsibility,
                    'payment_scale'=>$payment_scale,
                    'present_salary'=>$present_salary,
                    'to_continue'=>$to_continue,
                    'service_area'=>$service_area

                    )); 

        DB::table('teacher_personal')
                ->where('id', $id)  
                ->limit(1)  // optional - to ensure only one record is updated.
                ->update(array(
                    'position'=>$original_position,
                    'department'=>$dept_name,
                    'join_date'=>$from_date,
                    'release_date'=>$to_date
                    ));
        
        \session()->flash('tab', 3);
        \session()->flash('isUpdated', true);

        return \redirect()->back()->with('info', 'Employment information is updated successfully');
    }

 public function editTeacherappointmentinput(Request $request){
    $id=$request->get('id');
    $appointment_type=htmlspecialchars($request->get('appointment_type'));
    $bcs_no=htmlspecialchars($request->get('bcs_no'));
    $bcs_position=htmlspecialchars($request->get('bcs_position'));
    $bcs_go_no=htmlspecialchars($request->get('bcs_go_no'));
    $bcs_appointment_date=htmlspecialchars($request->get('bcs_appointment_date'));
    $bcs_appointment_date=date('Y-m-d',strtotime($bcs_appointment_date));
    $institute_name=htmlspecialchars($request->get('institute_name'));
    $bcs_joining_date=htmlspecialchars($request->get('bcs_joining_date'));
    $bcs_joining_date=date('Y-m-d',strtotime($bcs_joining_date));
    $bcs_ending_date=htmlspecialchars($request->get('bcs_ending_date'));
    $bcs_ending_date=date('Y-m-d',strtotime($bcs_ending_date));
    $bcs_job_field=htmlspecialchars($request->get('bcs_job_field'));
    $psc_no=htmlspecialchars($request->get('psc_no'));
    $psc_position=htmlspecialchars($request->get('psc_position'));
    $psc_go_no=htmlspecialchars($request->get('psc_go_no'));
    $psc_appointment_date=htmlspecialchars($request->get('psc_appointment_date'));
    $psc_appointment_date=date('Y-m-d',strtotime($psc_appointment_date));
    $psc_joining_date=htmlspecialchars($request->get('psc_joining_date'));
    $psc_joining_date=date('Y-m-d',strtotime($psc_joining_date));
    $private_service=htmlspecialchars($request->get('private_service'));

    $additional_go_no=htmlspecialchars($request->get('additional_go_no'));
    $absorption_date=htmlspecialchars($request->get('absorption_date'));
    $absorption_date=date('Y-m-d',strtotime($absorption_date));

    $effective_service=htmlspecialchars($request->get('effective_service'));
    $assistant_prof_go_no=htmlspecialchars($request->get('assistant_prof_go_no'));
    $assistant_prof_go_date=htmlspecialchars($request->get('assistant_prof_go_date'));
    $assistant_prof_go_date=date('Y-m-d',strtotime($assistant_prof_go_date));
    $assistant_prof_joining_date=htmlspecialchars($request->get('assistant_prof_joining_date'));
    $assistant_prof_joining_date=date('Y-m-d',strtotime($assistant_prof_joining_date));

    $associate_prof_go_no=htmlspecialchars($request->get('associate_prof_go_no'));
    $associate_prof_go_date=htmlspecialchars($request->get('associate_prof_go_date'));
    $associate_prof_go_date=date('Y-m-d',strtotime($associate_prof_go_date));
    $associate_prof_joining_date=htmlspecialchars($request->get('associate_prof_joining_date'));
    $associate_prof_joining_date=date('Y-m-d',strtotime($associate_prof_joining_date));

    $prof_go_no=htmlspecialchars($request->get('prof_go_no'));
    $prof_go_date=htmlspecialchars($request->get('prof_go_date'));
    $prof_go_date=date('Y-m-d',strtotime($prof_go_date));
    $prof_joining_date=htmlspecialchars($request->get('prof_joining_date'));
    $prof_joining_date=date('Y-m-d',strtotime($prof_joining_date));

    DB::table('teacher_appointment')
            ->where('id', $id)  
            ->limit(1)  // optional - to ensure only one record is updated.
            ->update(array(
				'appointment_type'=>$appointment_type,'bcs_no'=>$bcs_no,'bcs_position'=>$bcs_position,'bcs_go_no'=>$bcs_go_no,
				'bcs_appointment_date'=>$bcs_appointment_date,'institute_name'=>$institute_name,'bcs_joining_date'=>$bcs_joining_date,
				'bcs_ending_date'=>$bcs_ending_date,'bcs_job_field'=>$bcs_job_field,'psc_no'=>$psc_no,'psc_position'=>$psc_position,
				'psc_go_no'=>$psc_go_no,'psc_appointment_date'=>$psc_appointment_date,'psc_joining_date'=>$psc_joining_date,
				'private_service'=>$private_service,'additional_go_no'=>$additional_go_no,'absorption_date'=>$absorption_date,
				'effective_service'=>$effective_service,'assistant_prof_go_no'=>$assistant_prof_go_no,'assistant_prof_go_date'=>$assistant_prof_go_date,
				'assistant_prof_joining_date'=>$assistant_prof_joining_date,'associate_prof_go_no'=>$associate_prof_go_no,'associate_prof_go_date'=>$associate_prof_go_date,
				'associate_prof_joining_date'=>$associate_prof_joining_date,'prof_go_no'=>$prof_go_no,'prof_go_date'=>$prof_go_date,
				'prof_joining_date'=>$prof_joining_date

            	));

    \session()->flash('tab', 4);
    \session()->flash('isUpdated', true);

    return \redirect()->back()->with('info', 'Appointment is updated successfully');

 }  

    public function editTeachercareerinput(Request $request){
        $id=$request->get('id');
        $career_from=htmlspecialchars($request->get('career_from'));
        $career_from=date('Y-m-d',strtotime($career_from));
        $career_to=htmlspecialchars($request->get('career_to'));
        $career_to=date('Y-m-d',strtotime($career_to));
        $career_description=htmlspecialchars($request->get('career_description'));
        $special_memo=htmlspecialchars($request->get('special_memo'));
        $activity_memo=htmlspecialchars($request->get('activity_memo'));
        $language_name1=htmlspecialchars($request->get('language_name1'));
        $read_skill1=htmlspecialchars($request->get('read_skill1'));
        $write_skill1=htmlspecialchars($request->get('write_skill1'));
        $speak_skill1=htmlspecialchars($request->get('speak_skill1'));
        
        $language_name2=htmlspecialchars($request->get('language_name2'));
        $read_skill2=htmlspecialchars($request->get('read_skill2'));
        $write_skill2=htmlspecialchars($request->get('write_skill2'));
        $speak_skill2=htmlspecialchars($request->get('speak_skill2'));
        
        $language_name3=htmlspecialchars($request->get('language_name3'));
        $read_skill3=htmlspecialchars($request->get('read_skill3'));
        $write_skill3=htmlspecialchars($request->get('write_skill3'));
        $speak_skill3=htmlspecialchars($request->get('speak_skill3'));
        
        
        $emrgnc_name=htmlspecialchars($request->get('emrgnc_name'));
        $emrgnc_position_held=htmlspecialchars($request->get('emrgnc_position_held'));
        $emrgnc_address=htmlspecialchars($request->get('emrgnc_address'));
        $emrgnc_mobile=htmlspecialchars($request->get('emrgnc_mobile'));
        $emrgnc_email=htmlspecialchars($request->get('emrgnc_email'));
        $relation=htmlspecialchars($request->get('relation'));
        
        DB::table('teacher_career')
        ->where('id', $id)  
        ->limit(1)  // optional - to ensure only one record is updated.
        ->update(array(
            'career_from'=>$career_from,'career_to'=>$career_to,'career_description'=>$career_description,
            'special_memo'=>$special_memo,'activity_memo'=>$activity_memo,'language_name1'=>$language_name1,'read_skill1'=>$read_skill1,'write_skill1'=>$write_skill1,
            'speak_skill1'=>$speak_skill1,'language_name2'=>$language_name2,'read_skill2'=>$read_skill2,'write_skill2'=>$write_skill2,'speak_skill2'=>$speak_skill2
            ,'language_name3'=>$language_name3,'read_skill3'=>$read_skill3,'write_skill3'=>$write_skill3,'speak_skill3'=>$speak_skill3
            ,'emrgnc_name'=>$emrgnc_name,'emrgnc_position_held'=>$emrgnc_position_held,'emrgnc_address'=>$emrgnc_address
            ,'emrgnc_mobile'=>$emrgnc_mobile,'emrgnc_email'=>$emrgnc_email,'relation'=>$relation
        ));

        \session()->flash('tab', 5);
        \session()->flash('isUpdated', true);
        
        return \redirect()->back()->with('info', 'Career is updated successfully');
    }
}
