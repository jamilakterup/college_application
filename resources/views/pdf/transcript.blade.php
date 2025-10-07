<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Transcript</title>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,700i" rel="stylesheet"> 
</head>

<body>
<style>
html, body, div,fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td,{margin: 0; padding: 0; border: 0; outline: 0; font-weight: inherit; font-style: inherit; font-size: 100%; font-family: inherit; vertical-align:top;}:focus {outline: 0;}

table {border-collapse: collapse; border-spacing: 0;} input, select {vertical-align:middle;} abbr[title], dfn[title] {border-bottom:1px dotted; cursor:help;} 
body {font-family: 'Open Sans', sans-serif;}
td.title{ font-size:26px; line-height:36px; color:#000;}
td.subtitle{ font-size:20px; line-height:30px; color:#000;}
td.mintitle{ font-size:16px; line-height:24px; color:#000;}
.order-details tr, .order-details td{ border:1px solid #000000; padding:6px 6px 4px 2px; font-size: 13px;font-weight: 700; color:#000;}
.gpa-table tr, .gpa-table td{border:1px solid #000; padding:2px; font-size: 10px; color: #000; text-align: center;}
.sdudent-info tr, .sdudent-info td{ padding: 10px; font-size: 14px; font-weight: 700;color:#000;}
.sdudent-info td.border-bottom{ border-bottom:1px solid #333;}

</style>
    <div class="invoice-table">
   
        <img src="{{ asset('img/skcr2.jpg') }}" alt="" class="skcr-logo"/>
<p style="font-weight:bold; font-size:13px; text-align: center;">Email: {{config('settings.college_email_address')}}, Web Address: {{config('settings.college_web_address')}}, EIIN No. {{config('settings.college_eiin')}}</p>     
        <div class="left" style="float: left;width: 25%">
        <table width="100%" style="text-align:left; width:100%; margin-top:5px;">
            <tr><td>&nbsp;</td></tr>
        </table>
        </div>
        <div class="middle" style="float: left;width: 43%">
            <table width="100%" style="text-align:left; width:100%; margin-top:50px;">
            <tr>     
                <td width="100%" class="titles" style="text-align:center;"><h2>Academic Transcript</h2></td>
            </tr> 
             <tr>    
                <td width="100%" class="titles" style="text-align:center;"><h2>{{$exam_name->name}}-{{$publish->exam_year}}</h2></td>
            </tr>                    
        </table>
        </div>
        <div class="middle-gap" style="float: left;width: 6%">&nbsp;</div>
        <div class="right" style="float: left;width: 25%">
            <table class="gpa-table" border="1" width="100%" style="margin-top:8px;">
            <tr>
                <td><strong>Letter Grade</strong></td>
                <td><strong>Class Interval</strong></td>
                <td><strong>Grade Point</strong></td>                
            </tr>
            <tr>
                <td>A+</td>
                <td>80-100</td>
                <td>5</td>                
            </tr> 
            <tr>
                <td>A</td>
                <td>70-79</td>
                <td>4</td>                
            </tr> 
            <tr>
                <td>A-</td>
                <td>60-69</td>
                <td>3.5</td>                
            </tr> 
            <tr>
                <td>B</td>
                <td>50-59</td>
                <td>3</td>                
            </tr> 
            <tr>
                <td>C</td>
                <td>40-49</td>
                <td>2</td>                
            </tr> 
            <tr>
                <td>D</td>
                <td>33-39</td>
                <td>1</td>                
            </tr> 
            <tr>
                <td>F</td>
                <td>0-32</td>
                <td>0</td>                
            </tr>            
        </table>
        </div>

        
        <table class="sdudent-info" width="100%" style="text-align:left; width:100%; margin-top:20px;margin-bottom:15px;">
            <tr>        
                <td width="20%"><strong>Student's Name</strong></td>
                <td width="5%">:</td>
                <td width="75%" class="border-bottom">{{$value->name}}</td>
            </tr>
            <tr>        
                <td><strong>Father's Name</strong></td>
                <td>:</td>
                <td>{{$value->father_name}}</td>
            </tr>
            <tr>        
                <td><strong>Mother's Name</strong></td>
                <td>:</td>
                <td>{{$value->mother_name}}</td>
            </tr>
            <tr>        
                <td><strong>Student ID</strong></td>
                <td>:</td>
                <td>{{$value->id}}</td>
            </tr>
            <tr>        
                <td><strong>Session</strong></td>
                <td>:</td>
                <td>{{$value->session}}</td>
            </tr>
             <tr>        
                <td><strong>Group</strong></td>
                <td>:</td>
                <td> {{$value->groups}}</td>
            </tr>                      
        </table>

    <div class="table-left" style="width:100%; float:left;">
        <table class="order-details" border="1" width="100%" style="margin-top:8px;">
            <tr>
                <td width="6%" style="text-align: center;"><strong>Code</strong></td>
                <td width="34%"><strong>Subject Name</strong></td>
                <td width="6%"><strong>CQ</strong></td>
                <td width="7%"><strong>MCQ</strong></td>
                <td width="6%"><strong>PR.</strong></td>
                <td width="8%" style="text-align: center;"><strong>Total Marks</strong></td>
                <td width="7%"><strong>Grade</strong></td>
                <td width="8%" style="text-align: center;"><strong>Grade <br>Point</strong></td>
                <td width="9%" style="text-align: center;"><strong>GPA <br><small style="font-size: 9px;">(Without 4th)</small></strong></td>
                <td width="8%"><strong>GPA</strong></td>           
                
            </tr>
      
            <?php $sub_gpa= App\Models\StudentSubMarkGp::whereStudent_id($value->id)->whereSession($value->session)->whereGroup_id($group_id)->whereExam_id($exam_id)->get();
            $k=1; 

            $cgpa_tot= App\Models\HscGpa::whereStudent_id($value->id)->whereSession($value->session)->whereGroup_id($group_id)->whereExam_id($exam_id)->get();
            ?>  
         
          
           @foreach($sub_gpa as $sub)
            <tr>
                <?php   $particle_mark= App\Models\Mark::whereStudent_id($value->id)->whereSession($value->session)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSubject_id($sub->subject->id)->get();?>
                <td style="text-align: center;">{{$sub->subject->code}}</td>
                @if($sub->fourth!=1)
                <td>{{$sub->subject->name}}</td>
                @else
                    <td>{{$sub->subject->name}} (4th)</td>
                @endif
                    <td style="text-align: center;">{{$particle_mark[0]->converted_mark ?? null}}</td>
                @if(count($particle_mark)==2)
                    <td style="text-align: center;">{{$particle_mark[1]->converted_mark ?? null}}</td>
                    <td style="text-align: center;">-</td>          
                   
                @elseif(count($particle_mark)==3)
                    <td style="text-align: center;">{{$particle_mark[1]->converted_mark ?? null}}</td>
                    <td style="text-align: center;">{{$particle_mark[2]->converted_mark ?? null}}</td>
                @else
                    <td style="text-align: center;">-</td>
                    <td style="text-align: center;">-</td>    
                @endif
                @if($sub->absent!=1)
                <td style="text-align: center;">{{$sub->total_mark}}</td>
                @else
                <td style="text-align: center;">Absent</td>
                @endif
                <td style="text-align: center;">{{$sub->grade}}</td>
                <td style="text-align: center;">{{$sub->point}}</td>
                @if($k==1)
                <td rowspan="{{$sub_gpa->count()-1}}" style="text-align: center;vertical-align: middle;">{{round($cgpa_tot[0]->without_4th,2)}}</td>

                <td rowspan="{{$sub_gpa->count()}}" style="text-align: center;vertical-align: middle;">{{round($cgpa_tot[0]->cgpa,2)}}</td>
                @endif
                @if($k==$sub_gpa->count())
                    <td style="text-align: center; padding:0px; padding-right: 0px; padding-left: 0px;">
                       
                        <table border="0" style="margin: 0; padding: 0;">
                            <tr style="border:0;margin: 0; padding: 0;"><td style="font-size: 11px; padding: 0;">Above 2</td></tr>
                            <?php $zero=0; ?>
                        @if($sub->absent!=1 && $sub->point>2)   
                            <tr style=" padding:0px;border-left: 0; border-right: 0; border-bottom: 0; border-top: 0px; width: 100%; display: inline;"><td style="padding: 0px;font-size: 11px;">---------------<br>{{$sub->point-2}}</td></tr>
                        @elseif($sub->absent!=1 && $sub->point<3)
                            <tr style="padding:0px;border-left: 0; border-right: 0; border-bottom: 0; border-top: 0px solid #ccc;"><td style=" padding:0px;font-size: 11px;">---------------<br>{{$zero}}</td></tr>
                        @elseif($sub->absent1=1) 
                        <tr style="padding:0px;border-left: 0; border-right: 0; border-bottom: 0; border-top: 0px solid #ccc;"><td style=" padding:0px;font-size: 11px;">---------------<br>{{$zero}}</td> </tr>      
                        @endif    
                        </table>
                        
                    </td>
                @endif
            </tr>
            <?php $k++;?>
           @endforeach
           
         
        </table>
   <div class="hr clear" style="height:10px; margin:10px 0px; margin-top:15px; clear:both;">&nbsp;</div>
    <table width="100%">
        <tr style="text-align: right;">
            <td width="70%">&nbsp;</td>
            <td width="30%"><img style="width:150px;" src="{{asset('img/principal_sig.png')}}"><br/>----------------------------</td>
        </tr>
        <tr style="text-align: right;">
            <td width="70%">&nbsp;</td>
            <td width="30%">Principal's Signature</td>
        </tr>

        <tr style="text-align: right;">
            {{-- <td width="70%">Published Date: {{date('d-M-Y')}}</td> --}}
            <td width="70%">Published Date: {{date('Y-m-d',strtotime($publish->date))}}</td>
        </tr>
    </table>
   
   <!--  <div class="hr clear" style="height:10px; margin:10px 0px; margin-top:5px; clear:both;">&nbsp;</div> -->
  
    </div>
</body>
</html>
