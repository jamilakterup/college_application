<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TeacherPersonal;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class TeacherIdCardController extends Controller
{
    public function idcard(){
        return view('BackEnd.teacher.idcard.index');
    }

    public function generateidcard(Request $request){
        $id=htmlspecialchars($request->get('teacher_id'));
        $category=htmlspecialchars($request->get('category'));
        $print_id=htmlspecialchars($request->get('print_id'));

        if(!$category && empty($id)) { 
            $error_message = 'You must have to select a Department';
            return Redirect::back()->with('error', $error_message); 
        }else if($id<=0 && !empty($id)){
            $error_message = 'Invalid Teacher ID';
            return Redirect::back()->with('error', $error_message); 
        }

        $teachers = TeacherPersonal::with('teacherEmployment', 'teacherEducation', 'teacherAppointment', 'teacherCareer')
        ->where(function ($q) use ($category, $id) {
            if ($id) {
                $q->where('id', $id);
            }
            if ($category) {
                $q->where('department', $category);
            }
        })
        ->get();

        $results = \collect();
        foreach($teachers as $teacher) {
            $results->push([
                'teacher_id' => $teacher->id,
                'name' => $teacher->name,
                'image' => asset($teacher->image) ?? null,
                'department' => $teacher->department ?? null,
                'designation' => $teacher->position ?? null,
                'phone_office' => $teacher->phone_office ?? null,
                'personal_mobile' => $teacher->personal_mobile ?? null,
                'blood_group' => $teacher->blood_group ?? null,
                'nid_no' => $teacher->nid_no ?? null,
                'spouse_name' => $teacher->spouse_name ?? null,
                'relation' => $teacher->relation ?? null,
                'spouse_mobile' => $teacher->spouse_mobile ?? null,
                'spouse_phone' => $teacher->spouse_phone ?? null,
                'current_level' => $teacher->current_level ?? null,
                'address' => $teacher->permanent_address ?? null,
            ]);
        }

        if(count($results)==0){
            $error_message = 'No data found to generate ID card.';
            return Redirect::back()->with('error', $error_message); 
        }

        return view('BackEnd.teacher.idcard.generate', \compact('id','results','category','print_id'));
    }
}
