<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Libs\Study;
use App\Models\College;
use Illuminate\Http\Request;

class StudentController extends Controller
{
  function __construct()
  {
    // $this->middleware('permission:student.index', ['only' => ['student']]);
  }
  
  public function student() {
    
    $title = 'Easy CollegeMate - Student Management';
    $breadcrumb = 'student:Student Management|Dashboard';
    $colleges = College::paginate(Study::paginate());
    
    return view('BackEnd.student.index')
    ->withTitle($title)
    ->withBreadcrumb($breadcrumb)
    ->withColleges($colleges);
  }
  
  
  public function index() {
    
    $title = 'Easy CollegeMate - College Management';
    $breadcrumb = 'students.idcard:Idcard Management|Dashboard';
    return view('BackEnd.student.idcard.index');
  }
  
  
  public function categoryDetails() {
    
    if (Request::ajax()) { 
      $category=Input::get('category');
      return view('BackEnd.student.idcard.category_details_select')
      ->withCategory($category);
    }
  }
  
  
  public function depSelectFaculty(){
    
    if(Request::ajax()) {
      $faculty=Input::get('subject_selection');
      return view('BackEnd.student.idcard.dept_select_as_faculty')
      ->withFaculty($faculty);
    }
  }
  
  
  public function idCardGenerate() {
    
    $id = Input::get('student_id');
    $category = Input::get('category');
    // $group_string = 'group_'.$category;
    $level_string = 'level_'.$category;
    
    $level = Input::get($level_string);
    $faculty = Input::get('faculty');
    $group_hsc = Input::get('group_hsc');
    $group_degree = Input::get('group_degree');
    $type = Input::get('type');
    $results = 0;
    $querystring = '';
    if(!$category) {
      $error_message = 'You must have to select a category';
      return Redirect::back()->withError_message($error_message);  
    } 
    
    if($faculty) {
      $results = DB::select("SELECT faculty_name from faculties where id=$faculty");
    }
    if($group_hsc) {
      $results = DB::select("SELECT faculty_name from faculties where id=$group_hsc");
    }
    if($group_degree) {
      $results = DB::select("SELECT faculty_name from faculties where id=$group_degree");
    }
    if($type == 1 && $results <= 0)
    {
      $error_message = 'Invalid Student ID';
      return Redirect::back()->withError_message($error_message);   
    } 
    
    foreach($results as $result) {
      $faculty = $result->faculty_name;
    } 
    $dept=Input::get('dept');
    $session=Input::get('session');
    $group=$faculty;
    
    if ($type == 2) {
      if(!empty($id))
      Session::set('id',$id);
      if(!empty($category))
      Session::set('category',$category);
      if(!empty($group))
      Session::set('group',$group);
      if(!empty($level))
      Session::set('level',$level);
      
      if(!empty($faculty))
      Session::set('faculty',$faculty);
      if(!empty($dept))
      Session::set('dept',$dept);
      if(!empty($session))
      Session::set('session',$session);
      
      return Redirect::route('students.idcard.id_card_generate_multi');
    }
    else
    {
      if($category=='hsc' || $category=='degree' )
      {
        if($category=='hsc') 
        $table='student_info_hsc';
        else 
        $table='student_info_degree';
      }
      
      else if($category == 'honours' || $category == 'masters')    
      $table = 'student_info_hons';
      
      if(!$id) {
        if($category == 'hsc' || $category == 'degree' )
        {
          $querystring="select * from {$table} where id>0 and";
          
          if($group)
          $querystring=$querystring." groups='$group' and";
          if($level)
          $querystring=$querystring." current_level='$level' and";
          if($session)
          $querystring=$querystring." session='$session' and";
        } 
        
        else if($category == 'honours' || $category == 'masters') { 
          $querystring="select * from {$table} where id>0 and";
          
          if($faculty)
          $querystring=$querystring." faculty_name='$faculty' and";
          if($dept)
          $querystring=$querystring." dept_name='$dept' and";
          if($level)
          $querystring=$querystring." current_level='$level' and";
          if($session)
          $querystring=$querystring." session='$session' and";                    
        }
      }
      
      else
      {
        $query_results=DB::select("select * from {$table} where id={$id}");
      }
      //$query_results = DB::select(substr($id_query, 0, -3)); 
      //$query_results=$database->get_all_by_sql($id_query);
      if($querystring != '') {
        $querystring = substr($querystring, 0, -3);
        $query_results = DB::select($querystring);
      }   
      
      if(count($query_results) == 0)
      echo "<h3 align='center'><font color='red'>No data found to generate ID card.</font></h3>";
      ?> 
      <center><br/>
      
      <?php //if(count($query_results)>0) { 
        
        require app_path().'/libs/mpdf/third_party/mpdf60/mpdf.php';  
        
        $mpdf=new mPDF('',array(54.102,85.598),10,'lato',0,0,0,0 );
        $mpdf->SetTitle('ID Card');
        $mpdf->SetAuthor('Raj IT');
        $mpdf->SetSubject('RC ID Card');
        $mpdf->SetProtection(array('print','print-highres'));
        $mpdf->ignore_invalid_utf8 = true;
        $mpdf->SetImportUse();
        
        foreach($query_results as $key => $val) { 
          
          $mpdf->AddPage();
          if(strlen($val->dept_name) > 17)
          $pagecount = $mpdf->SetSourceFile(app_path().'/libs/id_card_custom_br_dw.pdf');
          else
          $pagecount = $mpdf->SetSourceFile(app_path().'/libs/id_card_custom_br_up.pdf');
          $tplIdx = $mpdf->ImportPage($pagecount);
          $mpdf->UseTemplate($tplIdx);
          $actualsize = $mpdf->UseTemplate($tplIdx);
          
          if($category == 'hsc') {
            $mpdf->Image(public_path()."/upload/college/hsc/{$val->image}", 17.8, 23.3, 18.4, 22.25);
          }
          elseif($category == 'honours') {
            $mpdf->Image(public_path()."/upload/college/honours/{$val->image}", 17.8, 23.3, 18.4, 22.25);
          }  
          elseif($category == 'masters') {
            $mpdf->Image(public_path()."/upload/college/masters/{$val->image}", 17.8, 23.3, 18.4, 22.25);
          }   
          elseif($category == 'degree') {
            $mpdf->Image(public_path()."/upload/college/degree/{$val->image}", 17.8, 23.3, 18.4, 22.25);
          }
          else {
            $mpdf->Image(public_path()."/upload/college/{$val->image}", 17.8, 23.3, 18.4, 22.25);     
          }   
          // 43,55,148
          $mpdf->SetTextColor(0,0,255);
          $css_style = 'style="width: 100%;
          margin-top:-10px;
          margin-right: auto;
          margin-bottom: auto;
          margin-left: 0px;
          text-align: center;
          color:blue;
          padding:0px 10px;
          font-size:9pt;
          line-height:1;
          font-weight:bold;"';
          
          $name_html = sprintf(
            '<div %s>
            <p>%s</p>
            </div>',$css_style,strtoupper($val->name));
            
            $mpdf->WriteHTML($name_html);
            
            $x_offset = 5;
            $y_offset = 33;
            $value=$val->current_level;
            $value=explode(' ', $value);
            
            $mpdf->SetFont('lato', 'B', 8);
            $mpdf->SetTextColor(0,0,0);
            $mpdf->WriteText($x_offset+20, $y_offset+22.5,$val->id);
            
            $mpdf->WriteText($x_offset+20, $y_offset+26.3,$val->session);
            $mpdf->WriteText($x_offset+20, $y_offset+30,$value[0]);
            if(strlen($val->dept_name) > 17) {
              $temp_dept = $val->dept_name;
              $temp_dept = explode(' ', $temp_dept);
              for ($c = 0; $c <count($temp_dept) ; $c++) { 
                if($c < 2)
                {
                  if($c == 0)
                  $dep_name1 = $temp_dept[$c];
                  else
                  $dep_name1 = $dep_name1.' '.$temp_dept[$c];
                } 
                else {
                  if($c == 2)
                  $dep_name2 = $temp_dept[$c];
                  else
                  $dep_name2 = $dep_name2.' '.$temp_dept[$c];
                }
              }
              if(strlen($val->dept_name) > 30) {
                $dep_name2 = "Environmental Sc.";
                $mpdf->SetFont('lato','B',8);
                $mpdf->WriteText($x_offset+20, $y_offset+34.1, $dep_name1);
                $mpdf->WriteText($x_offset+20, $y_offset+37.1, $dep_name2);
              }
              else {
                $mpdf->SetFont('lato','B',8);
                $mpdf->WriteText($x_offset+20, $y_offset+34.1, $dep_name1);
                $mpdf->WriteText($x_offset+20, $y_offset+37.1, $dep_name2);
              }
              $mpdf->SetFont('lato','B',8);
              $mpdf->WriteText($x_offset+20, $y_offset+42,$val->blood_group);
              $mpdf->Image("barcode.php?code={$val->id}", $x_offset+2, $y_offset +43, 40, 3.5);
            }
            else
            {
              $mpdf->SetFont('lato','B',8);
              $mpdf->WriteText($x_offset+20, $y_offset+34.2,$val->dept_name);   
              $mpdf->WriteText($x_offset+20, $y_offset+38,$val->blood_group);
              $mpdf->Image(url('/')."/barcode.php?code={$val->id}", $x_offset+2, $y_offset +43, 40, 3.5);
            }
          }
          
          $file_name = public_path()."/download/idcard/id_cards.pdf";
          $mpdf->Output($file_name,"F");
          
          $downlink =  "<center><a href='".url('/')."/download/idcard/id_cards.pdf' target='_blank'>Click to Download</a></center>";
          
          Session::put('downlink', $downlink);
          return Redirect::route('students.idcard');
          
          //}
          
        }
      }
      
      
      public function idCardGenerateMulti() {
        
        if(Session::has('id'))
        $id = Session::get('id');
        else
        $id = '';
        
        if(Session::has('category'))
        $category = Session::get('category');
        else
        $category = '';
        
        if(Session::has('group'))
        $group = Session::get('group');
        else
        $group = '';
        
        if(Session::has('level'))
        $level = Session::get('level');
        else
        $level = '';
        
        if(Session::has('faculty'))
        $faculty = Session::get('faculty');
        else
        $faculty = '';
        
        if(Session::has('dept'))
        $dept = Session::get('dept');
        else
        $dept = '';
        
        if(Session::has('session'))
        $session = Session::get('session');
        else
        $session = '';
        
        $querystring = '';
        if (!$category) { 
          $error_message = 'You must have to select a category';
          return Redirect::back()->withError_message($error_message);  
        }
        
        else
        {
          if ($category == 'hsc' || $category == 'degree' ) {
            if($category == 'hsc') 
            $table = 'student_info_hsc';
            else 
            $table = 'student_info_degree';
          }
          
          else if($category == 'honours' || $category == 'masters')    
          $table = 'student_info_hons';
          
          if ($category == 'hsc' || $category == 'degree' ) {
            $querystring = "select * from {$table} where id>0 and";
            
            if($group)
            $querystring = $querystring." groups='$group' and";
            if($level)
            $querystring = $querystring." current_level='$level' and";
            if($session)
            $querystring = $querystring." session='$session' and";
          } 
          
          else if($category=='honours' || $category=='masters') {       
            $querystring="select * from {$table} where id>0 and";
            
            if($faculty)
            $querystring=$querystring." faculty_name='$faculty' and";
            if($dept)
            $querystring=$querystring." dept_name='$dept' and";
            if($level)
            $querystring=$querystring." current_level='$level' and";
            if($session)
            $querystring=$querystring." session='$session' and";
          }
          
          $querystring = substr($querystring, 0, -3);
          
          // return $querystring;
          $id_query = DB::select($querystring);
          // $id_query = substr($id_query, 0, -3);
          $query_results = $id_query;      
          // $id_query = substr($id_query, 0, -3); // elliminating extra and at the end of the query
          // $query_results=$database->get_all_by_sql($id_query);
          if(count($query_results) == 0)
          echo "<h3 align='center'><font color='red'>No data found to generate ID card.</font></h3>";
          ?> 
          <center><br/>
          
          <?php if(count($query_results) > 0) { 
            //new mPDF('utf-8', array(190,236));
            //$mpdf = new mPDF('', array(90,60),9,'Arial');
            require app_path().'/libs/mpdf/third_party/mpdf60/mpdf.php';  
            
            $mpdf = new mPDF('','A4',10,'Lato');
            $mpdf->ignore_invalid_utf8 = true;
            $mpdf->SetImportUse();
            $pagecount = $mpdf->SetSourceFile(app_path().'/libs/Frame.pdf');
            $tplId = $mpdf->ImportPage($pagecount);
            $actualsize = $mpdf->SetPageTemplate($tplId);
            $mpdf->AddPage();
            
            $i = 0;
            foreach($query_results as $key => $val):
              
              $j = ($i%9);
              if ($j == 0) {
                $nXAxis  = 8;       $nYAxis  = 46.5; // for full name
                $idXAxis = 48.8;    $idYAxis = 67.5; // for id/roll
                $rnXAxis = 25.1;    $rnYAxis = 67.5; // for session 
                $mXAxis  = 25.1;    $mYAxis  = 63;   // for Class/Current Level
                $fnXAxis = 30;      $fnYAxis = 63;   // for subject
                $adXAxis = 48.8;    $adYAxis = 72;  // for Blood Group
                $imXAxis = 51;      $imYAxis = 24;  // for image  
                /*$barXAxis = -5;   $barYAxis = 253;*/  // for Barcode
                $faXAxis = 25.1;    $faYAxis = 54.4;  // For Father Name
                $maXAxis = 25.1;    $maYAxis = 58.8;  // For Mother Name
                $dobXAxis = 25.1;   $dobYAxis = 72.2;   // For Birthday
                $mobXAxis = 25.1;   $mobYAxis = 76.8;   // For Contact Number
              }
              if ($j == 1){
                $nXAxis  = 77;      $nYAxis  = 47;  // for full name
                $idXAxis = 116.8;   $idYAxis = 67.5;    // for id/roll
                $rnXAxis = 93.3;    $rnYAxis = 67.5;    // for session 
                $mXAxis  = 93.3;    $mYAxis  = 63;  // for Class/Current Level
                $fnXAxis = 99;      $fnYAxis = 63;    // for subject
                $adXAxis = 116.8;   $adYAxis = 72;    // for Blood Group
                $imXAxis = 308;     $imYAxis = 24.8;    // for image
                /*$barXAxis = 253;  $barYAxis = 253;*/  // For Barcode
                $faXAxis = 93.3;    $faYAxis = 54.4;  // For Father Name
                $maXAxis = 93.3;    $maYAxis = 59;  // For Mother Name
                $dobXAxis = 93.3;   $dobYAxis = 72.2;   // For Birthday
                $mobXAxis = 93.3;   $mobYAxis = 76.8;   // For Contact Number
              }
              if ($j == 2){
                $nXAxis  = 146;     $nYAxis  = 47;   // for full name
                $idXAxis = 185;     $idYAxis = 67.6;   // for id/roll
                $rnXAxis = 161.3;   $rnYAxis = 67.6;   // for session 
                $mXAxis  = 161.3;   $mYAxis  = 63.2;   // for Class/Current Level
                $fnXAxis = 167;     $fnYAxis = 63.2;   // for subject
                $adXAxis = 185;     $adYAxis = 72;   // for Blood Group
                $imXAxis = 565;     $imYAxis = 25;   // for image
                /*$barXAxis = 510;    $barYAxis = 253;*/  // For Barcode
                $faXAxis = 161.3;   $faYAxis = 54.5;    // For Father Name
                $maXAxis = 161.3;   $maYAxis = 59;    // For Mother Name
                $dobXAxis = 161.3;  $dobYAxis = 72.2;   // For Birthday
                $mobXAxis = 161.3;  $mobYAxis = 76.7;   // For Contact Number
              }
              if ($j == 3) {
                $nXAxis  = 8;       $nYAxis  = 144;   // for full name
                $idXAxis = 48.8;    $idYAxis = 165.8;   // for Id/Roll
                $rnXAxis = 25.1;    $rnYAxis = 165.8;   // for session 
                $mXAxis  = 25.1;    $mYAxis  = 161;   // for Class/Current Level
                $fnXAxis = 30;      $fnYAxis = 161; // for subject
                $adXAxis = 48.8;    $adYAxis = 170;   // for Blood Group
                $imXAxis = 50.7;    $imYAxis = 394.8; // for image
                /*$barXAxis = -5;     $barYAxis = 625;*/ // For Barcode
                $faXAxis = 25.1;    $faYAxis = 152.5;  // For Father Name
                $maXAxis = 25.1;    $maYAxis = 157;    // For Mother Name
                $dobXAxis = 25.1;   $dobYAxis = 170.2;   // For Birthday
                $mobXAxis = 25.1;   $mobYAxis = 174.5; // For Contact Number
              }
              if ($j == 4) {
                $nXAxis  = 77;      $nYAxis  = 144;   // for full name
                $idXAxis = 116.8;   $idYAxis = 165.6; // for Id/Roll
                $rnXAxis = 93.2;    $rnYAxis = 165.6; // for session 
                $mXAxis  = 93.2;    $mYAxis  = 161;   // for Class/Current Level
                $fnXAxis = 99;      $fnYAxis = 161;   // for subject
                $adXAxis = 116.8;   $adYAxis = 170;   // for Blood Group
                $imXAxis = 307.7;   $imYAxis = 394.8; // for image
                /*$barXAxis =253;   $barYAxis = 625; */   // For Barcode
                $faXAxis = 93.2;    $faYAxis = 152.5;  // For Father Name
                $maXAxis = 93.2;    $maYAxis = 157;    // For Mother Name
                $dobXAxis = 93.2;   $dobYAxis = 170.2; // For Birthday
                $mobXAxis = 93.2;   $mobYAxis = 174.5; // For Contact Number
              }
              
              if ($j == 5) {
                $nXAxis  = 145;     $nYAxis  = 144;     // for full name
                $idXAxis = 185;     $idYAxis = 165.6;   // for Id/Roll
                $rnXAxis = 161.2;   $rnYAxis = 165.6;   // for session 
                $mXAxis  = 161.2;   $mYAxis  = 161;     // for Class/Current Level
                $fnXAxis = 167;     $fnYAxis = 161;   // for subject
                $adXAxis = 185;     $adYAxis = 170;   // for Blood Group
                $imXAxis = 564.9;   $imYAxis = 394.8;   // for image
                /*$barXAxis = 510;    $barYAxis = 625;*/ // For Barcode
                $faXAxis = 161.2;   $faYAxis = 152.5;   // For Father Name
                $maXAxis = 161.2;   $maYAxis = 157;    // For Mother Name
                $dobXAxis = 161.2;  $dobYAxis = 170.2;   // For Birthday
                $mobXAxis = 161.2;  $mobYAxis = 175; // For Contact Number
              }
              if ($j == 6) {
                $nXAxis  = 8;       $nYAxis  = 243;   // for full name
                $idXAxis = 48.8;    $idYAxis = 264.5; // for Id/Roll
                $rnXAxis = 25.1;    $rnYAxis = 264.5;   // for session 
                $mXAxis  = 25.1;    $mYAxis  = 260;   // for Class/Current Level
                $fnXAxis = 30;      $fnYAxis = 260;   // for subject
                $adXAxis = 48.8;    $adYAxis = 269; // For Blood Group
                $imXAxis = 50.4;    $imYAxis = 768.4;   // For image
                /*$barXAxis = -5;   $barYAxis = 1015;*/ // For Barcode
                $faXAxis = 25.1;    $faYAxis = 251.5;   // For Father Name
                $maXAxis = 25.1;    $maYAxis = 255.8;    // For Mother Name
                $dobXAxis = 25.1;   $dobYAxis = 269;   // For Birthday
                $mobXAxis = 25.1;   $mobYAxis = 273.5; // For Contact Number
              }
              if ($j == 7) {
                $nXAxis  =  80;     $nYAxis  =  243;    // for full name
                $idXAxis =  116.8;  $idYAxis =  264.5;  // for Id/Roll
                $rnXAxis =  93.1;   $rnYAxis =  264.5;    // for session 
                $mXAxis  =  93.1;   $mYAxis  =  260;    // for Class/Current Level
                $fnXAxis =  99;    $fnYAxis =  260;    // for subject
                $adXAxis =  116.8;  $adYAxis =  269;  // for Blood Group
                $imXAxis =  307.6;  $imYAxis =  768.5;  // for image
                /*$barXAxis =  253;   $barYAxis = 1015;*/   // For Barcode
                $faXAxis = 93.1;    $faYAxis =  251.5;    // For Father Name
                $maXAxis = 93.1;    $maYAxis = 256;    // For Mother Name
                $dobXAxis = 93.1;   $dobYAxis = 269;   // For Birthday
                $mobXAxis = 93.1;   $mobYAxis = 273.6; // For Contact Number
              }
              if ($j == 8) {
                $nXAxis = 148;      $nYAxis  = 243;     // for full name
                $idXAxis = 185.3;   $idYAxis = 264.5;     // for Id/Roll
                $rnXAxis = 161.2;   $rnYAxis = 264.5;     // for session 
                $mXAxis  = 161.2;   $mYAxis  = 260;     // for Class/Current Level
                $fnXAxis = 166;     $fnYAxis = 260;     // for subject
                $adXAxis = 185.3;   $adYAxis = 269;   // for Blood Group
                $imXAxis = 565.2;   $imYAxis = 768.7;   // for image
                /*$barXAxis = 510;  $barYAxis = 1015;*/   // For Barcode
                $faXAxis = 161.2;   $faYAxis = 251.5;     // For Father Name
                $maXAxis = 161.2;   $maYAxis = 256;    // For Mother Name
                $dobXAxis = 161.2;  $dobYAxis = 269;   // For Birthday
                $mobXAxis = 161.2;  $mobYAxis = 273.4;   // For Contact Number
              }
              
              $value = $val->current_level;
              $value = explode(' ', $value);
              
              if($category == 'honours') {
                $blood_groups = DB::table('hons_admitted_student')->where('auto_id',$val->refference_id)->first();
                $blood_group = $blood_groups->blood_group; 
                $img =  '<img src="'.url('/').'/upload/college/honours/'.$val->image.'" style="float:left;margin-left:'.$imXAxis.'px;margin-top:'.$imYAxis.'px;" width="69.5px" height="84px">';
              }
              elseif($category == 'hsc') {
                $blood_groups = DB::table('hsc_admitted_students')->where('auto_id',$val->refference_id)->first();
                $blood_group = $blood_groups->blood_group;
                $img = '<img src="'.url('/').'/upload/college/hsc/'.$val->image.'" style="float:left;margin-left:'.$imXAxis.'px;margin-top:'.$imYAxis.'px;" width="67.2px" height="75.2px">';
              }
              elseif($category == 'masters') {
                $img = '<img src="'.url('/').'/upload/college/masters/'.$val->image.'" style="float:left;margin-left:'.$imXAxis.'px;margin-top:'.$imYAxis.'px;" width="68px" height="auto">';
              }   
              elseif($category == 'degree') {
                $img = '<img src="'.url('/').'/upload/college/degree/'.$val->image.'" style="float:left;margin-left:'.$imXAxis.'px;margin-top:'.$imYAxis.'px;" width="68px" height="auto">';
              }
              else {
                $img = '<img src="'.url('/').'/upload/college/'.$val->image.'" style="float:left;margin-left:'.$imXAxis.'px;margin-top:'.$imYAxis.'px;" width="68px" height="auto">';
              }
              // print($val->id);
              
              // return $img;
              if($val->current_level=='Masters 2nd Year')
              {
                $val->current_level='Masters Final';
              }
              /*if($i%9 == 2 || $i%9 == 5)
              $bar_code = "<img src='".url('/')."/barcode.php?code={$val->id}' width='178px' height='18px' style='float:left;margin-left:{$barXAxis}px;margin-top:{$barYAxis}px;'/>";
              else
              $bar_code = "<img src='".url('/')."/barcode.php?code={$val->id}'  width='178px' height='18px' style='float:left;margin-left:{$barXAxis}px;margin-top:{$barYAxis}px;'/>";*/
              
              /*$mpdf->SetFont('Lato','B',9);
              $mpdf->WriteText($rnXAxis, $rnYAxis, $val->session);
              $mpdf->WriteText($idXAxis, $idYAxis, $val->id);*/
              
              $mpdf->SetFont('Lato','B',8);
              $mpdf->SetTextColor(194,8,8);
              if (strlen($val->name) <= 11)
              $mpdf->WriteText($nXAxis+19, $nYAxis, strtoupper($val->name));
              else if(strlen($val->name) <= 14)
              $mpdf->WriteText($nXAxis+15, $nYAxis, strtoupper($val->name));
              else if(strlen($val->name) <= 15)
              $mpdf->WriteText($nXAxis+14, $nYAxis, strtoupper($val->name));
              else if(strlen($val->name) < 18)
              $mpdf->WriteText($nXAxis+12, $nYAxis, strtoupper($val->name));
              else if(strlen($val->name) <= 20)
              $mpdf->WriteText($nXAxis+12, $nYAxis, strtoupper($val->name));
              else if(strlen($val->name) <= 23)
              $mpdf->WriteText($nXAxis+7, $nYAxis, strtoupper($val->name));
              else if(strlen($val->name) <= 24)
              $mpdf->WriteText($nXAxis+6, $nYAxis, strtoupper($val->name));
              else if(strlen($val->name) < 25)
              $mpdf->WriteText($nXAxis+5, $nYAxis, strtoupper($val->name));
              else if(strlen($val->name) <= 26)
              $mpdf->WriteText($nXAxis+6, $nYAxis, strtoupper($val->name));
              else if(strlen($val->name) > 26)
              {
                $temp_name = $val->name;
                $temp_name = explode(' ', $temp_name);
                $first_name = '';
                $last_name = '';
                
                for ($k = 0; $k <count($temp_name) ; $k++) { 
                  if($k < 3)
                  {
                    if($k == 0)
                    $first_name = $temp_name[$k];
                    else  
                    $first_name = $first_name.' '.$temp_name[$k];
                  }
                  else
                  {
                    if($k == 3)
                    $last_name = $temp_name[$k];
                    else
                    $last_name = $last_name.' '.$temp_name[$k]; 
                  }
                }
                
                if(strlen($first_name) < 11)
                $mpdf->WriteText($nXAxis+16, $nYAxis, strtoupper($first_name));
                else if(strlen($first_name)<13)
                $mpdf->WriteText($nXAxis+15, $nYAxis, strtoupper($first_name));
                else if(strlen($first_name)<15)
                $mpdf->WriteText($nXAxis+13, $nYAxis, strtoupper($first_name));
                else if(strlen($first_name)<18)
                $mpdf->WriteText($nXAxis+11, $nYAxis, strtoupper($first_name));
                else if(strlen($first_name)<22)
                $mpdf->WriteText($nXAxis+9, $nYAxis, strtoupper($first_name));
                else
                {
                  $f_name2 = '';  
                  $f_name = explode(' ', $first_name);
                  $last_name = $f_name[2].' '.$last_name;
                  $f_name2 = $f_name[0].' '.$f_name[1];
                  if(strlen($f_name2) < 11)
                  $mpdf->WriteText($nXAxis+16, $nYAxis, strtoupper($f_name2));
                  else if(strlen($f_name2)<13)
                  $mpdf->WriteText($nXAxis+15, $nYAxis, strtoupper($f_name2));
                  else if(strlen($f_name2)<15)
                  $mpdf->WriteText($nXAxis+13, $nYAxis, strtoupper($f_name2));
                  else if(strlen($f_name2)<18)
                  $mpdf->WriteText($nXAxis+11, $nYAxis, strtoupper($f_name2));
                  else if(strlen($f_name2)<22)
                  $mpdf->WriteText($nXAxis+9, $nYAxis, strtoupper($f_name2));
                }
                
                if(strlen($last_name) < 7)
                $mpdf->WriteText($nXAxis+22, $nYAxis+3, strtoupper($last_name));
                else if(strlen($last_name)<9)
                $mpdf->WriteText($nXAxis+19, $nYAxis+3, strtoupper($last_name));
                else if(strlen($last_name)<11)
                $mpdf->WriteText($nXAxis+16, $nYAxis+3, strtoupper($last_name));
                else if(strlen($last_name)<13)
                $mpdf->WriteText($nXAxis+15, $nYAxis+3, strtoupper($last_name));
                else if(strlen($last_name)<15)
                $mpdf->WriteText($nXAxis+13, $nYAxis+3, strtoupper($last_name));
                else if(strlen($last_name)<18)
                $mpdf->WriteText($nXAxis+11, $nYAxis+3, strtoupper($last_name));
                else if(strlen($last_name)<=23)
                $mpdf->WriteText($nXAxis+9, $nYAxis+3, strtoupper($last_name));
                
              }
              else
              $mpdf->WriteText($nXAxis, $nYAxis, strtoupper($val->name));
              $mpdf->SetTextColor(0,0,0);
              $mpdf->SetFont('Lato','',6);
              $mpdf->WriteText($mXAxis, $mYAxis, $value[0]);
              $mpdf->WriteText($rnXAxis, $rnYAxis, $val->session);
              $mpdf->WriteText($idXAxis, $idYAxis, $val->id);
              $mpdf->WriteText($adXAxis, $adYAxis, $blood_group);
              $mpdf->WriteText($faXAxis, $faYAxis, $val->father_name);
              $mpdf->WriteText($maXAxis, $maYAxis, $val->mother_name);
              $mpdf->WriteText($dobXAxis, $dobYAxis, $val->birth_date);
              $mpdf->WriteText($mobXAxis, $mobYAxis, $val->contact_no);
              
              $mpdf->SetTextColor(0,0,0);
              if($val->current_level == 'HSC 1st Year') {
                $mpdf->WriteText($fnXAxis, $fnYAxis, $val->groups);
              }
              else
              {
                /*if(strlen($val->dept_name)>17)
                {
                  $temp_dept=$val->dept_name;
                  $temp_dept=explode(' ', $temp_dept);
                  for ($c=0; $c <count($temp_dept) ; $c++) { 
                    if($c<2)
                    {
                      if($c==0)
                      $dep_name1=$temp_dept[$c];
                      else
                      $dep_name1=$dep_name1.' '.$temp_dept[$c];
                    } 
                    else
                    {
                      if($c==2)
                      $dep_name2=$temp_dept[$c];
                      else
                      $dep_name2=$dep_name2.' '.$temp_dept[$c];
                    }
                  }
                  $mpdf->SetFont('Lato','B',7);
                  $mpdf->WriteText($fnXAxis-1, $fnYAxis-1.5, $dep_name1);
                  $mpdf->WriteText($fnXAxis-1, $fnYAxis+1, $dep_name2);
                }
                else
                {
                  $mpdf->SetFont('Lato','B',9);
                  $mpdf->WriteText($fnXAxis, $fnYAxis, $val->dept_name);
                } */
              }
              
              $mpdf->SetFont('Lato','B',9);
              $mpdf->WriteText($vdXAxis, $vdYAxis, $valid);
              $mpdf->WriteHTML($sig);
              // $mpdf->SetFont('Arial','',8);
              $mpdf->WriteHTML($img);
              $mpdf->WriteHTML($bar_code);
              
              if((($i+1)%9) == 0):
                $mpdf->AddPage();
              endif;
              
              $i++;
            endforeach;
            
            // $mpdf->Output( dirname(__DIR__) . "/easyLEManage/download/id_cards.pdf","F");
            $file_name=public_path()."/download/idcard/id_cards.pdf";
            $mpdf->Output($file_name,"F");
            $downlink =  "<center><a href='".url('/')."/download/idcard/id_cards.pdf' target='_blank'>Click to Download</a></center>";
            Session::put('downlink', $downlink);
            return Redirect::route('students.idcard');
            
          }
        }
      }
    }
