<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php ini_set('memory_limit', '-1');
ini_set('max_execution_time', 3600);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Exam Sit Plan</title>
</head>

<body>
<style>
html, body, div,fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td,{margin: 0; padding: 0; border: 0; outline: 0; font-weight: inherit; font-style: inherit; font-size: 100%; font-family: inherit; vertical-align:top;}:focus {outline: 0;}
.page-break {
                page-break-after: always;
            }
table {border-collapse: collapse; border-spacing: 0;} input, select {vertical-align:middle;} abbr[title], dfn[title] {border-bottom:1px dotted; cursor:help;} 
body {font-family: 'FreeSerif',sans-serif;}
td.title{ font-size:30px; line-height:36px; color:#000;}
td.subtitle{ font-size:24px; line-height:30px; color:#000;}
td.mintitle{ font-size:20px; line-height:24px; color:#000;}
table tr,  table td{ border:1px solid #3498db; padding:10px 10px; }
.sitplan-details td{ border-left:1px solid #3498db;border-right:1px solid #3498db;border-top:1px solid #3498db;border-bottom:1px solid #3498db;}
.sitplan-details tr, .sitplan-details td.blank-td{ border:1px solid #fff; }
.tdfont{font-size:30px;} 
</style>
 <?php
 $i=1;
 ?>
 <div class="sitplan-table"> 
@foreach($student_infos as $info)
<?php
if($current_level == 1) {
$cur_year =  'HSC 1st Year';
$class = '11th';
}
else{
	$cur_year =  'HSC 2nd Year';
	$class = '12th';
}

$sub1 = DB::table("student_subject_info")->where('student_id',$info->id)->where('current_level',$cur_year)->Where('sub1_id',$subject)->count();
$sub2 = DB::table("student_subject_info")->where('student_id',$info->id)->where('current_level',$cur_year)->Where('sub2_id',$subject)->count();
$sub3 = DB::table("student_subject_info")->where('student_id',$info->id)->where('current_level',$cur_year)->Where('sub3_id',$subject)->count();
$sub4 = DB::table("student_subject_info")->where('student_id',$info->id)->where('current_level',$cur_year)->Where('sub4_id',$subject)->count();
$sub5 = DB::table("student_subject_info")->where('student_id',$info->id)->where('current_level',$cur_year)->Where('sub5_id',$subject)->count();
$sub6 = DB::table("student_subject_info")->where('student_id',$info->id)->where('current_level',$cur_year)->Where('sub6_id',$subject)->count();
$fourth_id = DB::table("student_subject_info")->where('student_id',$info->id)->where('current_level',$cur_year)->Where('fourth_id',$subject)->count();
$count_subject = $sub1+$sub2+$sub3+$sub4+$sub5+$sub6+$fourth_id;
if($count_subject>0){
$subject_name = DB::table("subjects")->where('id',$subject)->pluck('name');

?>
<div class="table-left" style="width:43%; position: relative; float:left; display:inline-block; margin-bottom:40px; margin-left:40px;">
<table style="text-align:center;" >
  <tr>
    <td colspan="2"><span class="tdfont"><strong>{{config('settings.college_name')}} {{config('settings.college_name') !='' ? ', '.config('settings.college_district'):''}} <br>{{$exam_name->name}}-{{date("Y")}}</strong></span></td>
  </tr>
  <tr>
    <td width="17%"><span class="tdfont">Subject</span></td>
    <td width="83%" ><span class="tdfont">{{$subject_name}}</span></td>
  </tr>
  <tr>
    <td width="17%"><span class="tdfont">Class</span></td>
    <td width="83%" ><span class="tdfont">{{$class}} ( {{$info->groups}} )</span></td>
  </tr>
  <tr>
    <td width="17%"><span class="tdfont">Name</span></td>
    <td width="83%" ><span class="tdfont">{{$info->name}}</span></td>
  </tr>
    <tr>
    <td width="17%"><span class="tdfont">Roll</span></td>
    <td width="83%" ><span class="tdfont">{{$info->id}}</span></td>
  </tr>
</table>
    </div>
	<?php if($i%10==0){?>
    <div class="page-break"></div>  
	<?php } ?>
<?php $i++; } ?>
@endforeach 
</div>

</body>
</html>
