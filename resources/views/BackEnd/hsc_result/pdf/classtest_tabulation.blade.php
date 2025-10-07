<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{config('settings.college_name')}} Class Tabulation Sheet</title>
    <style>
        .page-break {
            page-break-after: always;
        }
        html, body, div,fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td,{margin: 0; padding: 0; border: 0; outline: 0; font-weight: inherit; font-style: inherit; font-size: 100%; font-family: inherit; vertical-align:top;}:focus {outline: 0;}

        table {border-collapse: collapse; border-spacing: 0;} input, select {vertical-align:middle;} abbr[title], dfn[title] {border-bottom:1px dotted; cursor:help;} 
        body {font-family: 'FreeSerif',sans-serif;}
        td.title{ font-size:30px; line-height:36px; color:#000;}
        td.subtitle{ font-size:24px; line-height:30px; color:#000;}
        td.mintitle{ font-size:20px; line-height:24px; color:#000;}
        .order-details tr, .order-details td{ border:1px solid #cecece; padding:5px 10px;}

    </style>
</head>

<body>

<?php  
   $info= App\Models\HscRsltProcessing::find($id);
   $merits= App\Models\HscGpa::whereSession($info->session)->whereGroup_id($info->group_id)->whereExam_id($info->exam_id)->orderby('student_id', 'ASC')->get();
   $all_subject= App\Models\ClassSubject::whereClasse_id($info->classe_id)->whereGroup_id($info->group_id)->get();
?>
    <div class="invoice-table">          
    
        @php $i=1; @endphp
        @foreach($merits as $merit)
            @php try {
                @endphp
                @if($i%20==1)  
                 <img src="@php echo url('/');@endphp/img/skcr2.jpg" alt="" class="RC-logo">             
                <table width="100%" style="text-align:center; width:100%; margin-top:10px;">
                    <tr>
                    <td width="10%" class="subtitle">&nbsp;</td>
                    <td width="20%" class="subtitle"><strong>Tabulation Sheet</strong></td>
                     <td width="45%" class="subtitle"><strong>{{$merits[0]->exam->name}}-{{$info->exam_year}}</strong></td>
                    <td width="25%" class="subtitle"><strong>{{$info->group->name}} </strong></td>
                    </tr>                
                </table>

                <div class="table-left" style="width:100%; float:left;">
                <table class="order-details" border="1" width="100%" style="margin-top:10px;page-break-inside:avoid;">
                    <tr>
                        <td rowspan="1">Roll No</td>
                    @foreach($all_subject as $subject)
                        @php 
                        $particle= App\Models\ConfigExamParticle::whereClasse_id($info->classe_id)->whereGroup_id($info->group_id)->whereSubject_id($subject->subject_id)->count();
                        @endphp                    
                        <td style="text-align: center;" ><strong>{{$subject->subject->code}}</strong></td>
                    @endforeach
                     <td> Total Number </td>
                    </tr>
                @endif
                <tr>
                    <td>{{$merit->student->class_roll}}</td>
                    @php  $total_number = 0;
                    $class_roll = $merit->student->class_roll;
                    $exam_id = $info->exam_id;
                    $stu_id = $merit->student->id;
                    
                     $check_cgpa =  DB::select("select * from  hsc_cgpa where student_id='$stu_id' and exam_id = '$exam_id' ");
                     @endphp
                    @foreach($all_subject as $subject)
                        @php 
                        $marks= App\Models\StudentSubMarkGp::whereStudent_id($merit->student_id)->whereExam_id($info->exam_id)->whereGroup_id($info->group_id)->whereSubject_id($subject->subject_id)->whereSession($info->session)->get();

                        $particle= App\Models\ConfigExamParticle::whereClasse_id($info->classe_id)->whereGroup_id($info->group_id)->whereSubject_id($subject->subject_id)->get();
                        if(count($marks)>0){
                        @endphp
                        @foreach($marks as $mark)
                        <td style="text-align: center;"><strong>{{$mark->total_mark}}</strong></td>
                        @php $total_number = $total_number + $mark->total_mark; @endphp
                         @endforeach
                        @php } else{
                            echo "<td style='text-align: center;'><strong>-</strong></td>";
                        }@endphp
                        
                    @endforeach
                    <td> @php 
                     if($check_cgpa[0]->cgpa != 0){
                        echo $total_number;}
                        else {
                        echo 'F';
                        } 
                    @endphp
                </tr>
                @if($i%20==0)
                 </table>       
                @endif
            @php  $i++;@endphp
            @php
            
                } catch (Exception $e) {
                    continue;
                }
            @endphp
        @endforeach     
        </table>
        </div>
    </body>
</html>
