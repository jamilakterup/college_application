<?php

namespace App\Http\Controllers\Student;

use DB;
use Session;
use Mpdf\Mpdf;
use App\Libs\Study;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Redirect;

class IDCardController extends Controller
{
  public function index()
  {

    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'students.idcard:Idcard Management|Dashboard';
    return view('BackEnd.student.idcard.index');
  }


  public function categoryDetails(Request $request)
  {

    if ($request->ajax()) {
      $category = $request->get('category');
      return view('BackEnd.student.idcard.category_details_select')
        ->withCategory($category);
    }
  }


  public function depSelectFaculty(Request $request)
  {

    if ($request->ajax()) {
      $faculty = $request->get('subject_selection');
      return view('BackEnd.student.idcard.dept_select_as_faculty')
        ->withFaculty($faculty);
    }
  }


  public function idCardGenerate(Request $request)
  {
    ini_set('memory_limit', '-1');
    set_time_limit(0);
    ini_set('max_execution_time', 9600);
    ini_set('request_terminate_timeout', 9600);
    ini_set('fastcgi_read_timeout', 9600);

    $id = $request->get('student_id');
    $category = $request->get('category');
    $level = $request->get('level');
    $faculty = $request->get('faculty');
    $dept = $request->get('dept');
    $session = $request->get('session');
    $type = $request->get('type');

    $faculty_key = 'faculty_name';
    $dept_key = 'dept_name';

    if (!$category) {
      $error_message = 'You must have to select a category';
      return Redirect::back()->with('error', $error_message);
    }

    if ($category == 'hsc') {
      $table = 'student_info_hsc';
      $faculty_key = 'groups';
      $dept_key = 'groups';
    }
    if ($category == 'degree') {
      $table = 'student_info_degree';
      $faculty_key = 'groups';
      $dept_key = 'groups';
    }
    if ($category == 'honours')
      $table = 'student_info_hons';
    if ($category == 'masters')
      $table = 'student_info_masters';


    $q = DB::table($table)->orderBy('id', 'asc');
    if ($level) {
      $q->where('current_level', $level);
    }
    if ($faculty) {
      $q->where($faculty_key, $faculty);
    }
    if ($dept) {
      $q->where($dept_key, $dept);
    }
    if ($session) {
      $q->where('session', $session);
    }
    if ($id) {
      $q->where('id', $id);
    }

    if (Schema::hasColumn($table, 'groups')) {
      $q->select('*', 'groups as faculty_name', 'groups as dept_name');
    }

    $student_info = $q->get();

    if (count($student_info) < 1) {
      $error_message = 'Student Not Found';
      return Redirect::back()->with('error', $error_message);
    }

    return view('BackEnd.student.idcard.generate_id_card', compact('student_info', 'type', 'category'));
  }


  public function idCardGenerateMulti()
  {
    if (Session::has('id'))
      $id = Session::get('id');
    else
      $id = '';

    if (Session::has('category'))
      $category = Session::get('category');
    else
      $category = '';

    if (Session::has('group'))
      $group = Session::get('group');
    else
      $group = '';

    if (Session::has('level'))
      $level = Session::get('level');
    else
      $level = '';

    if (Session::has('faculty'))
      $faculty = Session::get('faculty');
    else
      $faculty = '';

    if (Session::has('dept'))
      $dept = Session::get('dept');
    else
      $dept = '';

    if (Session::has('session'))
      $session = Session::get('session');
    else
      $session = '';

    if ($category == 'honours') {
      // return $this->idCardGenerateMultiHons();
    }

    if ($category == 'degree') {
      $faculty = DB::table('faculties')->where('faculty_name', $group)->get();
      $group = $faculty[0]->short_code;
    }

    $querystring = '';
    if (!$category) {
      $error_message = 'You must have to select a category';
      return Redirect::back()->withError_message($error_message);
    } else {
      if ($category == 'hsc' || $category == 'degree') {
        if ($category == 'hsc')
          $table = 'student_info_hsc';
        else
          $table = 'student_info_degree';
      } else if ($category == 'honours' || $category == 'masters')
        if ($category == 'honours')
          $table = 'student_info_hons';
        else
          $table = 'student_info_masters';

      if ($category == 'hsc' || $category == 'degree') {
        $querystring = "select * from {$table} where id>0 and";

        if ($group)
          $querystring = $querystring . " groups='$group' and";
        if ($level)
          $querystring = $querystring . " current_level='$level' and";
        if ($session)
          $querystring = $querystring . " session='$session' and";
      } else if ($category == 'honours' || $category == 'masters') {
        $querystring = "select * from {$table} where id>0 and";

        if ($faculty)
          $querystring = $querystring . " faculty_name='$faculty' and";
        if ($dept)
          $querystring = $querystring . " dept_name='$dept' and";
        if ($level)
          $querystring = $querystring . " current_level='$level' and";
        if ($session)
          $querystring = $querystring . " session='$session' and";
      }

      $querystring = substr($querystring, 0, -3);

      // return $querystring;
      $id_query = DB::select($querystring);
      // $id_query = substr($id_query, 0, -3);
      $query_results = $id_query;
      // $id_query = substr($id_query, 0, -3); // elliminating extra and at the end of the query
      // $query_results=$database->get_all_by_sql($id_query);
      if (count($query_results) == 0)
        echo "<h3 align='center'><font color='red'>No data found to generate ID card.</font></h3>";
?>
      <center><br />

        <?php if (count($query_results) > 0) {

          $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10, 'lato']);
          $mpdf->ignore_invalid_utf8 = true;
          if ($category == 'honours')
            $pagecount = $mpdf->SetSourceFile(app_path() . '/Libs/Frame_hons.pdf');
          else
            $pagecount = $mpdf->SetSourceFile(app_path() . '/Libs/Frame.pdf');

          $tplId = $mpdf->ImportPage($pagecount);
          $actualsize = $mpdf->SetPageTemplate($tplId);
          $mpdf->AddPage();

          $i = 0;
          // dd($query_results);
          foreach ($query_results as $key => $val):
            $nXAxis  = 10.5;
            $nYAxis  = 48.9; // for full name
            $idXAxis = 50.5;
            $idYAxis = 69.1; // for id/roll
            $rnXAxis = 26.9;
            $rnYAxis = 69.1; // for session 
            $mXAxis  = 50.5;
            $mYAxis  = 77.7;   // for Class/Current Level
            $fnXAxis = 26.9;
            $fnYAxis = 64.8;   // for subject
            $adXAxis = 50.5;
            $adYAxis = 73.3;  // for Blood Group
            $imXAxis = 29.7;
            $imYAxis = 25.2;  // for image  
            /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
            $faXAxis = 26.9;
            $faYAxis = 56.1;  // For Father Name
            $maXAxis = 26.9;
            $maYAxis = 60.1;  // For Mother Name
            $dobXAxis = 26.9;
            $dobYAxis = 73.5;   // For Birthday
            $mobXAxis = 26.9;
            $mobYAxis = 77.7;   // For Contact Number

            $j = ($i % 9);
            if ($j == 0) {
              $nXA  = $nXAxis;
              $nYA  = $nYAxis; // for full name
              $idXA = $idXAxis;
              $idYA = $idYAxis; // for id/roll
              $rnXA = $rnXAxis;
              $rnYA = $rnYAxis; // for session 
              $mXA  = $mXAxis;
              $mYA  = $mYAxis;   // for Class/Current Level
              $fnXA = $fnXAxis;
              $fnYA = $fnYAxis;   // for subject
              $adXA =  $adXAxis;
              $adYA = $adYAxis;  // for Blood Group
              $imXA = $imXAxis;
              $imYA = $imYAxis;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis;
              $faYA = $faYAxis;  // For Father Name
              $maXA = $maXAxis;
              $maYA = $maYAxis;  // For Mother Name
              $dobXA = $dobXAxis;
              $dobYA = $dobYAxis;   // For Birthday
              $mobXA = $mobXAxis;
              $mobYA = $mobYAxis;   // For Contact Number
            }
            if ($j == 1) {
              $nXA  = $nXAxis + 69;
              $nYA  = $nYAxis; // for full name
              $idXA = $idXAxis + 69;
              $idYA = $idYAxis; // for id/roll
              $rnXA = $rnXAxis + 69;
              $rnYA = $rnYAxis; // for session 
              $mXA  = $mXAxis + 69;
              $mYA  = $mYAxis;   // for Class/Current Level
              $fnXA = $fnXAxis + 69;
              $fnYA = $fnYAxis;   // for subject
              $adXA =  $adXAxis + 69;
              $adYA = $adYAxis;  // for Blood Group
              $imXA = $imXAxis + 69;
              $imYA = $imYAxis;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis + 69;
              $faYA = $faYAxis;  // For Father Name
              $maXA = $maXAxis + 69;
              $maYA = $maYAxis;  // For Mother Name
              $dobXA = $dobXAxis + 69;
              $dobYA = $dobYAxis;   // For Birthday
              $mobXA = $mobXAxis + 69;
              $mobYA = $mobYAxis;   // For Contact Number
            }
            if ($j == 2) {
              $nXA  = $nXAxis + 136;
              $nYA  = $nYAxis; // for full name
              $idXA = $idXAxis + 136;
              $idYA = $idYAxis; // for id/roll
              $rnXA = $rnXAxis + 136;
              $rnYA = $rnYAxis; // for session 
              $mXA  = $mXAxis + 136;
              $mYA  = $mYAxis;   // for Class/Current Level
              $fnXA = $fnXAxis + 136;
              $fnYA = $fnYAxis;   // for subject
              $adXA =  $adXAxis + 136;
              $adYA = $adYAxis;  // for Blood Group
              $imXA = $imXAxis + 136;
              $imYA = $imYAxis;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis + 136;
              $faYA = $faYAxis;  // For Father Name
              $maXA = $maXAxis + 136;
              $maYA = $maYAxis;  // For Mother Name
              $dobXA = $dobXAxis + 136;
              $dobYA = $dobYAxis;   // For Birthday
              $mobXA = $mobXAxis + 136;
              $mobYA = $mobYAxis;   // For Contact Number
            }
            if ($j == 3) {
              $nXA  = $nXAxis;
              $nYA  = $nYAxis + 96.6; // for full name
              $idXA = $idXAxis;
              $idYA = $idYAxis + 96.6; // for id/roll
              $rnXA = $rnXAxis;
              $rnYA = $rnYAxis + 96.6; // for session 
              $mXA  = $mXAxis;
              $mYA  = $mYAxis + 96.5;   // for Class/Current Level
              $fnXA = $fnXAxis;
              $fnYA = $fnYAxis + 96.6;   // for subject
              $adXA =  $adXAxis;
              $adYA = $adYAxis + 96.6;  // for Blood Group
              $imXA = $imXAxis;
              $imYA = $imYAxis + 96.4;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis;
              $faYA = $faYAxis + 96.6;  // For Father Name
              $maXA = $maXAxis;
              $maYA = $maYAxis + 96.6;  // For Mother Name
              $dobXA = $dobXAxis;
              $dobYA = $dobYAxis + 96.6;   // For Birthday
              $mobXA = $mobXAxis;
              $mobYA = $mobYAxis + 96.6;   // For Contact Number
            }
            if ($j == 4) {
              $nXA  = $nXAxis + 69;
              $nYA  = $nYAxis + 96.6; // for full name
              $idXA = $idXAxis + 69;
              $idYA = $idYAxis + 96.6; // for id/roll
              $rnXA = $rnXAxis + 69;
              $rnYA = $rnYAxis + 96.6; // for session 
              $mXA  = $mXAxis + 69;
              $mYA  = $mYAxis + 96.6;   // for Class/Current Level
              $fnXA = $fnXAxis + 69;
              $fnYA = $fnYAxis + 96.6;   // for subject
              $adXA =  $adXAxis + 69;
              $adYA = $adYAxis + 96.6;  // for Blood Group
              $imXA = $imXAxis + 69;
              $imYA = $imYAxis + 96.6;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis + 69;
              $faYA = $faYAxis + 96.6;  // For Father Name
              $maXA = $maXAxis + 69;
              $maYA = $maYAxis + 96.6;  // For Mother Name
              $dobXA = $dobXAxis + 69;
              $dobYA = $dobYAxis + 96.6;   // For Birthday
              $mobXA = $mobXAxis + 69;
              $mobYA = $mobYAxis + 96.6;   // For Contact Number
            }

            if ($j == 5) {
              $nXA  = $nXAxis + 136;
              $nYA  = $nYAxis + 96.6; // for full name
              $idXA = $idXAxis + 136;
              $idYA = $idYAxis + 96.6; // for id/roll
              $rnXA = $rnXAxis + 136;
              $rnYA = $rnYAxis + 96.6; // for session 
              $mXA  = $mXAxis + 136;
              $mYA  = $mYAxis + 96.6;   // for Class/Current Level
              $fnXA = $fnXAxis + 136;
              $fnYA = $fnYAxis + 96.6;   // for subject
              $adXA =  $adXAxis + 136;
              $adYA = $adYAxis + 96.6;  // for Blood Group
              $imXA = $imXAxis + 136;
              $imYA = $imYAxis + 96.6;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis + 136;
              $faYA = $faYAxis + 96.6;  // For Father Name
              $maXA = $maXAxis + 136;
              $maYA = $maYAxis + 96.6;  // For Mother Name
              $dobXA = $dobXAxis + 136;
              $dobYA = $dobYAxis + 96.6;   // For Birthday
              $mobXA = $mobXAxis + 136;
              $mobYA = $mobYAxis + 96.6;   // For Contact Number
            }
            if ($j == 6) {
              $nXA  = $nXAxis;
              $nYA  = $nYAxis + 192.7; // for full name
              $idXA = $idXAxis;
              $idYA = $idYAxis + 192.2; // for id/roll
              $rnXA = $rnXAxis;
              $rnYA = $rnYAxis + 192.2; // for session 
              $mXA  = $mXAxis;
              $mYA  = $mYAxis + 192.2;   // for Class/Current Level
              $fnXA = $fnXAxis;
              $fnYA = $fnYAxis + 192.2;   // for subject
              $adXA =  $adXAxis;
              $adYA = $adYAxis + 192.2;  // for Blood Group
              $imXA = $imXAxis;
              $imYA = $imYAxis + 192.4;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis;
              $faYA = $faYAxis + 192.2;  // For Father Name
              $maXA = $maXAxis;
              $maYA = $maYAxis + 192.2;  // For Mother Name
              $dobXA = $dobXAxis;
              $dobYA = $dobYAxis + 192.2;   // For Birthday
              $mobXA = $mobXAxis;
              $mobYA = $mobYAxis + 192.2;   // For Contact Number
            }
            if ($j == 7) {
              $nXA  = $nXAxis + 69;
              $nYA  = $nYAxis + 192.2; // for full name
              $idXA = $idXAxis + 69;
              $idYA = $idYAxis + 192.2; // for id/roll
              $rnXA = $rnXAxis + 69;
              $rnYA = $rnYAxis + 192.2; // for session 
              $mXA  = $mXAxis + 69;
              $mYA  = $mYAxis + 192.2;   // for Class/Current Level
              $fnXA = $fnXAxis + 69;
              $fnYA = $fnYAxis + 192.2;   // for subject
              $adXA =  $adXAxis + 69;
              $adYA = $adYAxis + 192.2;  // for Blood Group
              $imXA = $imXAxis + 69;
              $imYA = $imYAxis + 192.4;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis + 69;
              $faYA = $faYAxis + 192.2;  // For Father Name
              $maXA = $maXAxis + 69;
              $maYA = $maYAxis + 192.3;  // For Mother Name
              $dobXA = $dobXAxis + 69;
              $dobYA = $dobYAxis + 192.2;   // For Birthday
              $mobXA = $mobXAxis + 69;
              $mobYA = $mobYAxis + 192.2;   // For Contact Number
            }
            if ($j == 8) {
              $nXA  = $nXAxis + 136;
              $nYA  = $nYAxis + 192.2; // for full name
              $idXA = $idXAxis + 136;
              $idYA = $idYAxis + 192.2; // for id/roll
              $rnXA = $rnXAxis + 136;
              $rnYA = $rnYAxis + 192.2; // for session 
              $mXA  = $mXAxis + 136;
              $mYA  = $mYAxis + 192.2;   // for Class/Current Level
              $fnXA = $fnXAxis + 136;
              $fnYA = $fnYAxis + 192.2;   // for subject
              $adXA =  $adXAxis + 136;
              $adYA = $adYAxis + 192.2;  // for Blood Group
              $imXA = $imXAxis + 136;
              $imYA = $imYAxis + 192.2;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis + 136;
              $faYA = $faYAxis + 192.2;  // For Father Name
              $maXA = $maXAxis + 136;
              $maYA = $maYAxis + 192.2;  // For Mother Name
              $dobXA = $dobXAxis + 136;
              $dobYA = $dobYAxis + 192.2;   // For Birthday
              $mobXA = $mobXAxis + 136;
              $mobYA = $mobYAxis + 192.2;   // For Contact Number
            }

            $value = $val->current_level;
            $value = explode(' ', $value)[0];

            if ($val->current_level == 'Masters 2nd Year') $value = 'Masters Final';
            if ($val->current_level == 'Masters 1st Year') $value = 'Masters Previous';

            // if($value == 'Degree'){
            //    $faculty = DB::table('faculties')->where('short_code', $val->groups)->get();
            //    $short_code_faculty = $faculty[0]->short_code.' '.$value;
            //    $value = $short_code_faculty;
            // }


            if ($category == 'honours') {
              $blood_groups = DB::table('hons_admitted_student')->where('auto_id', $val->refference_id)->first();
              $blood_group = $blood_groups->blood_group;
              $img =  url('/') . '/upload/college/honours/' . $val->session . '/' . $val->image;
            } elseif ($category == 'hsc') {
              $blood_groups = DB::table('hsc_admitted_students')->where('auto_id', $val->refference_id)->first();
              $blood_group = $blood_groups->blood_group;
              $img = url('/') . '/upload/college/hsc/' . $val->session . '/' . $val->image;
            } elseif ($category == 'masters') {
              $blood_group = $val->blood_group;
              $img = url('/') . '/upload/college/masters/' . $val->session . '/' . $val->image;
            } elseif ($category == 'degree') {
              $blood_group = $val->blood_group;

              $img = url('/') . '/upload/college/degree/' . $val->session . '/' . $val->image;
            } else {
              $blood_group = $val->blood_group;
              $img = url('/') . '/upload/college/' . $val->session . '/' . $val->image;
            }
            // print($val->id);

            $mpdf->SetFont('Lato', 'B', 8);
            $mpdf->SetTextColor(194, 8, 8);
            if (strlen($val->name) <= 11)
              $mpdf->WriteText($nXA + 19, $nYA, strtoupper($val->name));
            else if (strlen($val->name) <= 14)
              $mpdf->WriteText($nXA + 15, $nYA, strtoupper($val->name));
            else if (strlen($val->name) <= 15)
              $mpdf->WriteText($nXA + 14, $nYA, strtoupper($val->name));
            else if (strlen($val->name) < 18)
              $mpdf->WriteText($nXA + 12, $nYA, strtoupper($val->name));
            else if (strlen($val->name) <= 20)
              $mpdf->WriteText($nXA + 12, $nYA, strtoupper($val->name));
            else if (strlen($val->name) <= 23)
              $mpdf->WriteText($nXA + 7, $nYA, strtoupper($val->name));
            else if (strlen($val->name) <= 24)
              $mpdf->WriteText($nXA + 6, $nYA, strtoupper($val->name));
            else if (strlen($val->name) < 25)
              $mpdf->WriteText($nXA + 5, $nYA, strtoupper($val->name));
            else if (strlen($val->name) <= 26)
              $mpdf->WriteText($nXA + 6, $nYA, strtoupper($val->name));
            else if (strlen($val->name) > 26) {
              $temp_name = $val->name;
              $temp_name = explode(' ', $temp_name);
              $first_name = '';
              $last_name = '';

              for ($k = 0; $k < count($temp_name); $k++) {
                if ($k < 3) {
                  if ($k == 0)
                    $first_name = $temp_name[$k];
                  else
                    $first_name = $first_name . ' ' . $temp_name[$k];
                } else {
                  if ($k == 3)
                    $last_name = $temp_name[$k];
                  else
                    $last_name = $last_name . ' ' . $temp_name[$k];
                }
              }

              if (strlen($first_name) < 11)
                $mpdf->WriteText($nXA + 16, $nYA, strtoupper($first_name));
              else if (strlen($first_name) < 13)
                $mpdf->WriteText($nXA + 15, $nYA, strtoupper($first_name));
              else if (strlen($first_name) < 15)
                $mpdf->WriteText($nXA + 13, $nYA, strtoupper($first_name));
              else if (strlen($first_name) < 18)
                $mpdf->WriteText($nXA + 11, $nYA, strtoupper($first_name));
              else if (strlen($first_name) < 22)
                $mpdf->WriteText($nXA + 9, $nYA, strtoupper($first_name));
              else {
                $f_name2 = '';
                $f_name = explode(' ', $first_name);
                $last_name = $f_name[2] . ' ' . $last_name;
                $f_name2 = $f_name[0] . ' ' . $f_name[1];
                if (strlen($f_name2) < 11)
                  $mpdf->WriteText($nXA + 16, $nYA, strtoupper($f_name2));
                else if (strlen($f_name2) < 13)
                  $mpdf->WriteText($nXA + 15, $nYA, strtoupper($f_name2));
                else if (strlen($f_name2) < 15)
                  $mpdf->WriteText($nXA + 13, $nYA, strtoupper($f_name2));
                else if (strlen($f_name2) < 18)
                  $mpdf->WriteText($nXA + 11, $nYA, strtoupper($f_name2));
                else if (strlen($f_name2) < 22)
                  $mpdf->WriteText($nXA + 9, $nYA, strtoupper($f_name2));
              }

              if (strlen($last_name) < 7)
                $mpdf->WriteText($nXA + 22, $nYA + 3, strtoupper($last_name));
              else if (strlen($last_name) < 9)
                $mpdf->WriteText($nXA + 19, $nYA + 3, strtoupper($last_name));
              else if (strlen($last_name) < 11)
                $mpdf->WriteText($nXA + 16, $nYA + 3, strtoupper($last_name));
              else if (strlen($last_name) < 13)
                $mpdf->WriteText($nXA + 15, $nYA + 3, strtoupper($last_name));
              else if (strlen($last_name) < 15)
                $mpdf->WriteText($nXA + 13, $nYA + 3, strtoupper($last_name));
              else if (strlen($last_name) < 18)
                $mpdf->WriteText($nXA + 11, $nYA + 3, strtoupper($last_name));
              else if (strlen($last_name) <= 23)
                $mpdf->WriteText($nXA + 9, $nYA + 3, strtoupper($last_name));
            } else
              $mpdf->WriteText($nXA, $nYA, strtoupper($val->name));
            $mpdf->SetTextColor(0, 0, 0);
            $mpdf->SetFont('Lato', '', 6);
            $mpdf->WriteText($mXA, $mYA, $value);
            $mpdf->WriteText($rnXA, $rnYA, $val->session);
            $mpdf->WriteText($idXA, $idYA, $val->id);
            $mpdf->WriteText($adXA, $adYA, $blood_group);
            $mpdf->WriteText($faXA, $faYA, $val->father_name);
            $mpdf->WriteText($maXA, $maYA, $val->mother_name);
            $mpdf->WriteText($dobXA, $dobYA, $val->birth_date);
            $mpdf->WriteText($mobXA, $mobYA, $val->contact_no);

            $mpdf->SetTextColor(0, 0, 0);
            if ($val->current_level == 'HSC 1st Year') {
              $mpdf->WriteText($fnXA, $fnYA, $val->groups);
            } elseif ($category == 'degree') {
              $mpdf->WriteText($fnXA, $fnYA, $val->groups);
            } else {
              $mpdf->WriteText($fnXA, $fnYA, $val->dept_name);
            }
            // $mpdf->WriteHTML($img);

            $mpdf->Image($img, $imXA, $imYA, 19, 19.2, 'jpg', '', true, true);
            // $mpdf->WriteHTML($bar_code);

            if ((($i + 1) % 9) == 0):
              $mpdf->AddPage();
            endif;

            $i++;
          endforeach;

          // $mpdf->Output( dirname(__DIR__) . "/easyLEManage/download/id_cards.pdf","F");
          $time = $category . '_' . date('Y-m-d');
          //   $file_name=public_path()."/download/idcard/id_cards_".$time.".pdf";
          $file_name = "id_cards_" . $time . ".pdf";
          $mpdf->Output($file_name, "I");
          $downlink =  "<center><a href='" . url('/') . "/download/idcard/id_cards_" . $time . ".pdf' target='_blank'>Click to Download</a></center>";
          Session::put('downlink', $downlink);
          return Redirect::route('students.idcard');
        }
      }
    }

    public function idCardGenerateMultiHons()
    {

      if (Session::has('id'))
        $id = Session::get('id');
      else
        $id = '';

      if (Session::has('category'))
        $category = Session::get('category');
      else
        $category = '';

      if (Session::has('group'))
        $group = Session::get('group');
      else
        $group = '';

      if (Session::has('level'))
        $level = Session::get('level');
      else
        $level = '';

      if (Session::has('faculty'))
        $faculty = Session::get('faculty');
      else
        $faculty = '';

      if (Session::has('dept'))
        $dept = Session::get('dept');
      else
        $dept = '';

      if (Session::has('session'))
        $session = Session::get('session');
      else
        $session = '';

      $querystring = '';
      if (!$category) {
        $error_message = 'You must have to select a category';
        return Redirect::back()->withError_message($error_message);
      } else {
        if ($category == 'hsc' || $category == 'degree') {
          if ($category == 'hsc')
            $table = 'student_info_hsc';
          else
            $table = 'student_info_degree';
        } else if ($category == 'honours' || $category == 'masters')
          $table = 'student_info_hons';

        if ($category == 'hsc' || $category == 'degree') {
          $querystring = "select * from {$table} where id>0 and";

          if ($group)
            $querystring = $querystring . " groups='$group' and";
          if ($level)
            $querystring = $querystring . " current_level='$level' and";
          if ($session)
            $querystring = $querystring . " session='$session' and";
        } else if ($category == 'honours' || $category == 'masters') {
          $querystring = "select * from {$table} where id>0 and";

          if ($faculty)
            $querystring = $querystring . " faculty_name='$faculty' and";
          if ($dept)
            $querystring = $querystring . " dept_name='$dept' and";
          if ($level)
            $querystring = $querystring . " current_level='$level' and";
          if ($session)
            $querystring = $querystring . " session='$session' and";
        }

        $querystring = substr($querystring, 0, -3);

        // return $querystring;
        $id_query = DB::select($querystring);
        // $id_query = substr($id_query, 0, -3);
        $query_results = $id_query;
        // $id_query = substr($id_query, 0, -3); // elliminating extra and at the end of the query
        // $query_results=$database->get_all_by_sql($id_query);
        if (count($query_results) == 0)
          echo "<h3 align='center'><font color='red'>No data found to generate ID card.</font></h3>";
        ?>
        <center><br />

    <?php if (count($query_results) > 0) {
          //new mPDF('utf-8', array(190,236));
          //$mpdf = new mPDF('', array(90,60),9,'Arial');
          require app_path() . '/libs/mpdf/third_party/mpdf60/mpdf.php';

          $mpdf = new mPDF('', 'A4', 10, 'Lato');
          $mpdf->ignore_invalid_utf8 = true;
          $mpdf->SetImportUse();
          $pagecount = $mpdf->SetSourceFile(app_path() . '/libs/FrameHons.pdf');
          $tplId = $mpdf->ImportPage($pagecount);
          $actualsize = $mpdf->SetPageTemplate($tplId);
          $mpdf->AddPage();

          $i = 0;
          foreach ($query_results as $key => $val):

            $nXAxis  = 10.5;
            $nYAxis  = 48.1; // for full name
            $idXAxis = 50.5;
            $idYAxis = 68.3; // for id/roll
            $rnXAxis = 26.9;
            $rnYAxis = 68.3; // for session 
            $mXAxis  = 50.5;
            $mYAxis  = 76.7;   // for Class/Current Level
            $fnXAxis = 26.9;
            $fnYAxis = 64;   // for subject
            $adXAxis = 50.5;
            $adYAxis = 72.5;  // for Blood Group
            $imXAxis = 55.7;
            $imYAxis = 32.5;  // for image  
            /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
            $faXAxis = 26.9;
            $faYAxis = 55.3;  // For Father Name
            $maXAxis = 26.9;
            $maYAxis = 59.7;  // For Mother Name
            $dobXAxis = 26.9;
            $dobYAxis = 72.7;   // For Birthday
            $mobXAxis = 26.9;
            $mobYAxis = 76.9;   // For Contact Number

            $j = ($i % 9);
            if ($j == 0) {
              $nXA  = $nXAxis;
              $nYA  = $nYAxis; // for full name
              $idXA = $idXAxis;
              $idYA = $idYAxis; // for id/roll
              $rnXA = $rnXAxis;
              $rnYA = $rnYAxis; // for session 
              $mXA  = $mXAxis;
              $mYA  = $mYAxis;   // for Class/Current Level
              $fnXA = $fnXAxis;
              $fnYA = $fnYAxis;   // for subject
              $adXA =  $adXAxis;
              $adYA = $adYAxis;  // for Blood Group
              $imXA = $imXAxis;
              $imYA = $imYAxis;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis;
              $faYA = $faYAxis;  // For Father Name
              $maXA = $maXAxis;
              $maYA = $maYAxis;  // For Mother Name
              $dobXA = $dobXAxis;
              $dobYA = $dobYAxis;   // For Birthday
              $mobXA = $mobXAxis;
              $mobYA = $mobYAxis;   // For Contact Number
            }
            if ($j == 1) {
              $nXA  = $nXAxis + 69;
              $nYA  = $nYAxis; // for full name
              $idXA = $idXAxis + 69;
              $idYA = $idYAxis; // for id/roll
              $rnXA = $rnXAxis + 69;
              $rnYA = $rnYAxis; // for session 
              $mXA  = $mXAxis + 69;
              $mYA  = $mYAxis;   // for Class/Current Level
              $fnXA = $fnXAxis + 69;
              $fnYA = $fnYAxis;   // for subject
              $adXA =  $adXAxis + 69;
              $adYA = $adYAxis;  // for Blood Group
              $imXA = $imXAxis + 260.2;
              $imYA = $imYAxis;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis + 69;
              $faYA = $faYAxis;  // For Father Name
              $maXA = $maXAxis + 69;
              $maYA = $maYAxis;  // For Mother Name
              $dobXA = $dobXAxis + 69;
              $dobYA = $dobYAxis;   // For Birthday
              $mobXA = $mobXAxis + 69;
              $mobYA = $mobYAxis;   // For Contact Number
            }
            if ($j == 2) {
              $nXA  = $nXAxis + 136;
              $nYA  = $nYAxis; // for full name
              $idXA = $idXAxis + 136;
              $idYA = $idYAxis; // for id/roll
              $rnXA = $rnXAxis + 136;
              $rnYA = $rnYAxis; // for session 
              $mXA  = $mXAxis + 136;
              $mYA  = $mYAxis;   // for Class/Current Level
              $fnXA = $fnXAxis + 136;
              $fnYA = $fnYAxis;   // for subject
              $adXA =  $adXAxis + 136;
              $adYA = $adYAxis;  // for Blood Group
              $imXA = $imXAxis + 514.4;
              $imYA = $imYAxis;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis + 136;
              $faYA = $faYAxis;  // For Father Name
              $maXA = $maXAxis + 136;
              $maYA = $maYAxis;  // For Mother Name
              $dobXA = $dobXAxis + 136;
              $dobYA = $dobYAxis;   // For Birthday
              $mobXA = $mobXAxis + 136;
              $mobYA = $mobYAxis;   // For Contact Number
            }
            if ($j == 3) {
              $nXA  = $nXAxis;
              $nYA  = $nYAxis + 97.6; // for full name
              $idXA = $idXAxis;
              $idYA = $idYAxis + 97.6; // for id/roll
              $rnXA = $rnXAxis;
              $rnYA = $rnYAxis + 97.6; // for session 
              $mXA  = $mXAxis;
              $mYA  = $mYAxis + 97.6;   // for Class/Current Level
              $fnXA = $fnXAxis;
              $fnYA = $fnYAxis + 97.6;   // for subject
              $adXA =  $adXAxis;
              $adYA = $adYAxis + 97.6;  // for Blood Group
              $imXA = $imXAxis - 1.9;
              $imYA = $imYAxis + 368;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis;
              $faYA = $faYAxis + 97.6;  // For Father Name
              $maXA = $maXAxis;
              $maYA = $maYAxis + 97.4;  // For Mother Name
              $dobXA = $dobXAxis;
              $dobYA = $dobYAxis + 97.6;   // For Birthday
              $mobXA = $mobXAxis;
              $mobYA = $mobYAxis + 97.6;   // For Contact Number
            }
            if ($j == 4) {
              $nXA  = $nXAxis + 69;
              $nYA  = $nYAxis + 97.6; // for full name
              $idXA = $idXAxis + 69;
              $idYA = $idYAxis + 97.6; // for id/roll
              $rnXA = $rnXAxis + 69;
              $rnYA = $rnYAxis + 97.6; // for session 
              $mXA  = $mXAxis + 69;
              $mYA  = $mYAxis + 97.6;   // for Class/Current Level
              $fnXA = $fnXAxis + 69;
              $fnYA = $fnYAxis + 97.6;   // for subject
              $adXA =  $adXAxis + 69;
              $adYA = $adYAxis + 97.6;  // for Blood Group
              $imXA = $imXAxis + 260.4;
              $imYA = $imYAxis + 368;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis + 69;
              $faYA = $faYAxis + 97.6;  // For Father Name
              $maXA = $maXAxis + 69;
              $maYA = $maYAxis + 97.6;  // For Mother Name
              $dobXA = $dobXAxis + 69;
              $dobYA = $dobYAxis + 97.6;   // For Birthday
              $mobXA = $mobXAxis + 69;
              $mobYA = $mobYAxis + 97.6;   // For Contact Number
            }

            if ($j == 5) {
              $nXA  = $nXAxis + 136;
              $nYA  = $nYAxis + 97.6; // for full name
              $idXA = $idXAxis + 136;
              $idYA = $idYAxis + 97.6; // for id/roll
              $rnXA = $rnXAxis + 136;
              $rnYA = $rnYAxis + 97.6; // for session 
              $mXA  = $mXAxis + 136;
              $mYA  = $mYAxis + 97.6;   // for Class/Current Level
              $fnXA = $fnXAxis + 136;
              $fnYA = $fnYAxis + 97.6;   // for subject
              $adXA =  $adXAxis + 136;
              $adYA = $adYAxis + 97.6;  // for Blood Group
              $imXA = $imXAxis + 514;
              $imYA = $imYAxis + 368;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis + 136;
              $faYA = $faYAxis + 97.6;  // For Father Name
              $maXA = $maXAxis + 136;
              $maYA = $maYAxis + 97.6;  // For Mother Name
              $dobXA = $dobXAxis + 136;
              $dobYA = $dobYAxis + 97.6;   // For Birthday
              $mobXA = $mobXAxis + 136;
              $mobYA = $mobYAxis + 97.6;   // For Contact Number
            }
            if ($j == 6) {
              $nXA  = $nXAxis;
              $nYA  = $nYAxis + 192.9; // for full name
              $idXA = $idXAxis;
              $idYA = $idYAxis + 192.9; // for id/roll
              $rnXA = $rnXAxis;
              $rnYA = $rnYAxis + 192.9; // for session 
              $mXA  = $mXAxis;
              $mYA  = $mYAxis + 192.9;   // for Class/Current Level
              $fnXA = $fnXAxis;
              $fnYA = $fnYAxis + 192.9;   // for subject
              $adXA =  $adXAxis;
              $adYA = $adYAxis + 192.9;  // for Blood Group
              $imXA = $imXAxis;
              $imYA = $imYAxis + 729;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis;
              $faYA = $faYAxis + 192.9;  // For Father Name
              $maXA = $maXAxis;
              $maYA = $maYAxis + 192.9;  // For Mother Name
              $dobXA = $dobXAxis;
              $dobYA = $dobYAxis + 192.9;   // For Birthday
              $mobXA = $mobXAxis;
              $mobYA = $mobYAxis + 192.9;   // For Contact Number
            }
            if ($j == 7) {
              $nXA  = $nXAxis + 69;
              $nYA  = $nYAxis + 192.9; // for full name
              $idXA = $idXAxis + 69;
              $idYA = $idYAxis + 192.9; // for id/roll
              $rnXA = $rnXAxis + 69;
              $rnYA = $rnYAxis + 192.9; // for session 
              $mXA  = $mXAxis + 69;
              $mYA  = $mYAxis + 193.2;   // for Class/Current Level
              $fnXA = $fnXAxis + 69;
              $fnYA = $fnYAxis + 192.9;   // for subject
              $adXA =  $adXAxis + 69;
              $adYA = $adYAxis + 192.9;  // for Blood Group
              $imXA = $imXAxis + 260.5;
              $imYA = $imYAxis + 729.3;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis + 69;
              $faYA = $faYAxis + 192.9;  // For Father Name
              $maXA = $maXAxis + 69;
              $maYA = $maYAxis + 192.9;  // For Mother Name
              $dobXA = $dobXAxis + 69;
              $dobYA = $dobYAxis + 192.9;   // For Birthday
              $mobXA = $mobXAxis + 69;
              $mobYA = $mobYAxis + 192.9;   // For Contact Number
            }
            if ($j == 8) {
              $nXA  = $nXAxis + 136;
              $nYA  = $nYAxis + 192.9; // for full name
              $idXA = $idXAxis + 136;
              $idYA = $idYAxis + 192.9; // for id/roll
              $rnXA = $rnXAxis + 136;
              $rnYA = $rnYAxis + 192.9; // for session 
              $mXA  = $mXAxis + 136;
              $mYA  = $mYAxis + 192.9;   // for Class/Current Level
              $fnXA = $fnXAxis + 136;
              $fnYA = $fnYAxis + 192.9;   // for subject
              $adXA =  $adXAxis + 136;
              $adYA = $adYAxis + 192.9;  // for Blood Group
              $imXA = $imXAxis + 514;
              $imYA = $imYAxis + 729.3;  // for image  
              /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
              $faXA = $faXAxis + 136;
              $faYA = $faYAxis + 192.9;  // For Father Name
              $maXA = $maXAxis + 136;
              $maYA = $maYAxis + 192.9;  // For Mother Name
              $dobXA = $dobXAxis + 136;
              $dobYA = $dobYAxis + 192.9;   // For Birthday
              $mobXA = $mobXAxis + 136;
              $mobYA = $mobYAxis + 192.9;   // For Contact Number
            }

            $value = $val->current_level;
            // $value = explode(' ', $value);
            $class = explode(' ', $value);
            $faculty = DB::table('faculties')->where('faculty_name', $val->faculty_name)->get();
            $short_code_faculty = $faculty[0]->short_code . ' ' . $class[0];
            $dept_name = $val->dept_name;

            if ($category == 'honours') {
              $blood_groups = DB::table('hons_admitted_student')->where('auto_id', $val->refference_id)->first();
              $blood_group = $blood_groups->blood_group;
              $img =  '<img src="' . url('/') . '/upload/college/honours/' . $val->image . '" style="float:left;margin-left:' . $imXA . 'px;margin-top:' . $imYA . 'px;" width="71.7px" height="72.8px">';
            } elseif ($category == 'hsc') {
              $blood_groups = DB::table('hsc_admitted_students')->where('auto_id', $val->refference_id)->first();
              $blood_group = $blood_groups->blood_group;
              $img = '<img src="' . url('/') . '/upload/college/hsc/' . $val->image . '" style="float:left;margin-left:' . $imXA . 'px;margin-top:' . $imYA . 'px;" width="71.7px" height="72.8px">';
            } elseif ($category == 'masters') {
              $img = '<img src="' . url('/') . '/upload/college/masters/' . $val->image . '" style="float:left;margin-left:' . $imXA . 'px;margin-top:' . $imYA . 'px;" width="68px" height="auto">';
            } elseif ($category == 'degree') {
              $img = '<img src="' . url('/') . '/upload/college/degree/' . $val->image . '" style="float:left;margin-left:' . $imXA . 'px;margin-top:' . $imYA . 'px;" width="68px" height="auto">';
            } else {
              $img = '<img src="' . url('/') . '/upload/college/' . $val->image . '" style="float:left;margin-left:' . $imXA . 'px;margin-top:' . $imYA . 'px;" width="68px" height="auto">';
            }
            // print($val->id);

            // return $img;
            if ($val->current_level == 'Masters 2nd Year') {
              $val->current_level = 'Masters Final';
            }

            $mpdf->SetFont('Lato', 'B', 8);
            $mpdf->SetTextColor(194, 8, 8);
            if (strlen($val->name) <= 11)
              $mpdf->WriteText($nXA + 19, $nYA, strtoupper($val->name));
            else if (strlen($val->name) <= 14)
              $mpdf->WriteText($nXA + 15, $nYA, strtoupper($val->name));
            else if (strlen($val->name) <= 15)
              $mpdf->WriteText($nXA + 14, $nYA, strtoupper($val->name));
            else if (strlen($val->name) < 18)
              $mpdf->WriteText($nXA + 12, $nYA, strtoupper($val->name));
            else if (strlen($val->name) <= 20)
              $mpdf->WriteText($nXA + 12, $nYA, strtoupper($val->name));
            else if (strlen($val->name) <= 23)
              $mpdf->WriteText($nXA + 7, $nYA, strtoupper($val->name));
            else if (strlen($val->name) <= 24)
              $mpdf->WriteText($nXA + 6, $nYA, strtoupper($val->name));
            else if (strlen($val->name) < 25)
              $mpdf->WriteText($nXA + 5, $nYA, strtoupper($val->name));
            else if (strlen($val->name) <= 26)
              $mpdf->WriteText($nXA + 6, $nYA, strtoupper($val->name));
            else if (strlen($val->name) > 26) {
              $temp_name = $val->name;
              $temp_name = explode(' ', $temp_name);
              $first_name = '';
              $last_name = '';

              for ($k = 0; $k < count($temp_name); $k++) {
                if ($k < 3) {
                  if ($k == 0)
                    $first_name = $temp_name[$k];
                  else
                    $first_name = $first_name . ' ' . $temp_name[$k];
                } else {
                  if ($k == 3)
                    $last_name = $temp_name[$k];
                  else
                    $last_name = $last_name . ' ' . $temp_name[$k];
                }
              }

              if (strlen($first_name) < 11)
                $mpdf->WriteText($nXA + 16, $nYA, strtoupper($first_name));
              else if (strlen($first_name) < 13)
                $mpdf->WriteText($nXA + 15, $nYA, strtoupper($first_name));
              else if (strlen($first_name) < 15)
                $mpdf->WriteText($nXA + 13, $nYA, strtoupper($first_name));
              else if (strlen($first_name) < 18)
                $mpdf->WriteText($nXA + 11, $nYA, strtoupper($first_name));
              else if (strlen($first_name) < 22)
                $mpdf->WriteText($nXA + 9, $nYA, strtoupper($first_name));
              else {
                $f_name2 = '';
                $f_name = explode(' ', $first_name);
                $last_name = $f_name[2] . ' ' . $last_name;
                $f_name2 = $f_name[0] . ' ' . $f_name[1];
                if (strlen($f_name2) < 11)
                  $mpdf->WriteText($nXA + 16, $nYA, strtoupper($f_name2));
                else if (strlen($f_name2) < 13)
                  $mpdf->WriteText($nXA + 15, $nYA, strtoupper($f_name2));
                else if (strlen($f_name2) < 15)
                  $mpdf->WriteText($nXA + 13, $nYA, strtoupper($f_name2));
                else if (strlen($f_name2) < 18)
                  $mpdf->WriteText($nXA + 11, $nYA, strtoupper($f_name2));
                else if (strlen($f_name2) < 22)
                  $mpdf->WriteText($nXA + 9, $nYA, strtoupper($f_name2));
              }

              if (strlen($last_name) < 7)
                $mpdf->WriteText($nXA + 22, $nYA + 3, strtoupper($last_name));
              else if (strlen($last_name) < 9)
                $mpdf->WriteText($nXA + 19, $nYA + 3, strtoupper($last_name));
              else if (strlen($last_name) < 11)
                $mpdf->WriteText($nXA + 16, $nYA + 3, strtoupper($last_name));
              else if (strlen($last_name) < 13)
                $mpdf->WriteText($nXA + 15, $nYA + 3, strtoupper($last_name));
              else if (strlen($last_name) < 15)
                $mpdf->WriteText($nXA + 13, $nYA + 3, strtoupper($last_name));
              else if (strlen($last_name) < 18)
                $mpdf->WriteText($nXA + 11, $nYA + 3, strtoupper($last_name));
              else if (strlen($last_name) <= 23)
                $mpdf->WriteText($nXA + 9, $nYA + 3, strtoupper($last_name));
            } else
              $mpdf->WriteText($nXA, $nYA, strtoupper($val->name));
            $mpdf->SetTextColor(0, 0, 0);
            $mpdf->SetFont('Lato', '', 6);
            $mpdf->WriteText($mXA, $mYA, $dept_name);
            // $mpdf->WriteText($clXA, $clYA, $class[0]);
            $mpdf->WriteText($clXA, $clYA, $short_code_faculty);
            $mpdf->WriteText($rnXA, $rnYA, $val->session);
            $mpdf->WriteText($idXA, $idYA, $val->id);
            $mpdf->WriteText($adXA, $adYA, $blood_group);
            $mpdf->WriteText($faXA, $faYA, $val->father_name);
            $mpdf->WriteText($maXA, $maYA, $val->mother_name);
            $mpdf->WriteText($dobXA, $dobYA, $val->birth_date);
            $mpdf->WriteText($mobXA, $mobYA, $val->contact_no);

            $mpdf->SetTextColor(0, 0, 0);
            if ($val->current_level == 'HSC 1st Year') {
              $mpdf->WriteText($fnXA, $fnYA, $val->groups);
            } else {
            }

            $mpdf->SetFont('Lato', 'B', 9);
            $mpdf->WriteText($vdXA, $vdYA, $valid);
            $mpdf->WriteHTML($sig);
            // $mpdf->SetFont('Arial','',8);
            $mpdf->WriteHTML($img);
            $mpdf->WriteHTML($bar_code);

            if ((($i + 1) % 9) == 0):
              $mpdf->AddPage();
            endif;

            $i++;
          endforeach;
          $time = time();
          $file_name = public_path() . "/download/idcard/id_cards_" . $time . ".pdf";
          $mpdf->Output($file_name, "F");
          $downlink =  "<center><a href='" . url('/') . "/download/idcard/id_cards_" . $time . ".pdf' target='_blank'>Click to Download</a></center>";
          Session::put('downlink', $downlink);
          return Redirect::route('students.idcard');
        }
      }
    }
  }
