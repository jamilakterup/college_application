<?php ini_set('memory_limit', '-1');
ini_set('max_execution_time', 3600);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>{{config('settings.college_name')}} Tabulation Sheet</title>
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
   $info=HscRsltProcessing::find($id);
   //$merits=HscGpa::whereSession($info->session)->whereGroup_id($info->group_id)->whereExam_id($info->exam_id)->orderby('student_id', 'ASC')->get();
   
   $merits=HscGpa::whereGroup_id($info->group_id)->whereExam_id($info->exam_id)->whereExam_year($info->exam_year)->orderby('student_id', 'ASC')->take(10)->get();
   $all_subject=ClasseSubject::whereClasse_id($info->classe_id)->whereGroup_id($info->group_id)->get();
?>
    <div class="invoice-table">          
    
        @php $i=1; @endphp
        @foreach($merits as $merit)

                @if($i%20==1)  
                 <img src="{{ asset('img/skcr2.jpg') }}" alt="" class="clg-logo">             
                <table width="100%" style="text-align:center; width:100%; margin-top:10px;">
                    <tr>
                    <td width="10%" class="subtitle">&nbsp;</td>
                    <td width="20%" class="subtitle"><strong>Tabulation Sheet</strong></td>
                     <td width="45%" class="subtitle"><strong>{{$merits[0]->exam->name}}-{{date("Y")}}</strong></td>
                    <td width="25%" class="subtitle"><strong>{{$info->group->name}} </strong></td>
                    </tr>                
                </table>

                <div class="table-left" style="width:100%; float:left;">
                <table class="order-details" border="1" width="100%" style="margin-top:10px;page-break-inside:avoid;">
                    <tr>
    				    <td rowspan="2">Sl</td>
                        <td rowspan="2">Roll No</td>
                    @foreach($all_subject as $subject)
                        @php 
                        $particle=ConfigExamParticle::whereClasse_id($info->classe_id)->whereGroup_id($info->group_id)->whereSubject_id($subject->subject_id)->count();
                        @endphp                   
                        <td style="text-align: center;" colspan={{$particle}}><strong>{{$subject->subject->code}}</strong></td>
                    @endforeach
                    </tr> 
                    <tr>                   
                        @foreach($all_subject as $subject)
                        @php
                        $particle=ConfigExamParticle::whereClasse_id($info->classe_id)->whereGroup_id($info->group_id)->whereSubject_id($subject->subject_id)->get();
                        @endphp
                        @foreach($particle as $part)                    
                        <td style="text-align: center;"><strong>{{$part->xmparticle->short_name}}</strong></td>
                        @endforeach
                        @endforeach
                        <td> Total Number </td>
                    </tr>
                @endif
                <tr>
    			    <td> {{$i}} </td>
                    <td>{{$merit->student->class_roll}}</td>
                    @php  $total_number = 0;
                    $class_roll = $merit->student->class_roll;
                    $exam_id = $info->exam_id;
                     $check_cgpa =  DB::select("select * from  hsc_cgpa where student_id='$class_roll' and exam_id = '$exam_id' ");
                     @endphp
                    @foreach($all_subject as $subject)
                        @php
                        $marks=Mark::whereStudent_id($merit->student_id)->whereExam_id($info->exam_id)->whereGroup_id($info->group_id)->whereSubject_id($subject->subject_id)->get();

                        $particle=ConfigExamParticle::whereClasse_id($info->classe_id)->whereGroup_id($info->group_id)->whereSubject_id($subject->subject_id)->get();
                        @endphp
                        @if($marks->count()==0)
                            @foreach($particle as $par)
                            <td>&nbsp;</td>
                            @endforeach
                        @else
                        @foreach($marks as $mark)                    
                        <td style="text-align: center;"><strong>{{$mark->mark}}</strong></td>
                        @php $total_number = $total_number + $mark->mark; @endphp
                        @endforeach
                        @endif
                        
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
                 <div class="page-break"></div>           
                @endif
            @php  $i++;@endphp
        @endforeach
        </table>
        </div>
    </body>
</html>
