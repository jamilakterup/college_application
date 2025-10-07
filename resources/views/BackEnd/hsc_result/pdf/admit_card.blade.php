<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admit Card</title>
</head>

<body>
<style>
html, body, div,fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td,{margin: 0; padding: 0; border: 0; outline: 0; font-weight: inherit; font-style: inherit; font-size: 100%; font-family: inherit; vertical-align:top;}:focus {outline: 0;}

table {border-collapse: collapse; border-spacing: 0;} input, select {vertical-align:middle;} abbr[title], dfn[title] {border-bottom:1px dotted; cursor:help;} 
body {font-family: 'FreeSerif',sans-serif;}
td.title{ font-size:30px; line-height:36px; color:#000;}
td.subtitle{ font-size:24px; line-height:30px; color:#000;}
td.mintitle{ font-size:20px; line-height:24px; color:#000;}
.order-details tr, .order-details td{ border:1px solid #cecece; padding:5px 10px;}

</style>
    <div class="invoice-table">
    <?php $i=1; ?>
    @foreach($student_info_hsc as $info)
   <img src="{{ asset('img/skcr2.jpg') }}" alt="" class="skcr-logo">
    <table width="100%" style="text-align:center; width:100%; margin-top:10px;">
        <tr>
        <td width="10%" class="subtitle">&nbsp;</td>
        <td width="53%" width="33%" class="subtitle">Admit Card</td>
        <td width="37%" class="subtitle">{{$exam_name->name}}-{{date("Y")}} </td>
        </tr>
        
    </table>

    <div class="table-left" style="width:54%; float:left;">
    <table class="order-details" border="1" width="100%" style="margin-top:10px;">
    	<tr>
        <td width="30%">Class Roll</td>
        <td width="70%">{{$info->class_roll}}</td>
        </tr>
        <tr>
        <td>Student Name</td>
      
          <td>{{ucfirst($info->name)}}</td>
        </tr>
        <tr>
        <td>Fathers Name</td>
        <td>{{$info->father_name}}</td>
        </tr>
        <tr>
        <td>Mothers Name</td>
        <td>{{$info->mother_name}}</td>
        </tr>      
        <tr>
        <td>Session</td>
        <td>{{$info->session}}</td>
        </tr>
        <tr>
        <td>Current Level</td>
        <td>{{$info->current_level}}</td>
        </tr>
        <tr>
        <td>Group</td>
        <td>{{$info->groups}}</td>
        </tr>
    </table>
    </div>
    <?php  $sub_info= App\Models\StudentSubInfo::whereStudent_id($info->id)->whereCurrent_level($info->current_level)->get();?>
    <div class="table-right" style="width:44%; float:right;">
    <table class="order-details" border="1" width="100%" style="margin-top:10px;">
       <tr>
            <td width="16%">{{$sub_info[0]->sub1->code}}</td>
            <td width="84%">{{$sub_info[0]->sub1->name}}</td>
       </tr>
        <tr>
            <td>{{$sub_info[0]->sub2->code}}</td>
            <td>{{$sub_info[0]->sub2->name}}</td>
        </tr>
        <tr>
        @if($sub_info[0]->sub3_id!=0)
            <td>{{$sub_info[0]->sub3->code}}</td>
            <td>{{$sub_info[0]->sub3->name}}</td>
        @else
            <td>{{$sub_info[0]->sub4->code}}</td>
            <td>{{$sub_info[0]->sub4->name}}</td>
        @endif
        </tr>
        <tr>
        @if($sub_info[0]->sub3_id!=0)    
            <td>{{$sub_info[0]->sub4->code}}</td>
            <td>{{$sub_info[0]->sub4->name}}</td>
        @else
            <td>{{$sub_info[0]->sub5->code}}</td>
            <td>{{$sub_info[0]->sub5->name}}</td>
         @endif    
        </tr>
        <tr>
        @if($sub_info[0]->sub3_id!=0)    
            <td>{{$sub_info[0]->sub5->code}}</td>
            <td>{{$sub_info[0]->sub5->name}}</td>
        @else
            <td>{{$sub_info[0]->sub6->code}}</td>
            <td>{{$sub_info[0]->sub6->name}}</td>
        @endif    
        </tr>
        <tr>
        @if($sub_info[0]->sub3_id!=0)     
            <td>{{$sub_info[0]->sub6->code}}</td>
            <td>{{$sub_info[0]->sub6->name}}</td>
        @else
            <td>{{$sub_info[0]->fourth->code}}</td>
            <td>{{$sub_info[0]->fourth->name}} (4th)</td>
        @endif    
        </tr>
        @if($sub_info[0]->sub3_id!=0) 
        <tr>       
            <td>{{$sub_info[0]->fourth->code}}</td>
            <td>{{$sub_info[0]->fourth->name}} (4th)</td>          
        </tr> 
        @endif        
    </table>    
    </div>
    <div class="hr clear" style="height:30px; margin:0px 0px; margin-top:30px; clear:both;">&nbsp;</div>
    <table width="100%">
        <tr style="text-align: right;">
            <td width="70%">&nbsp;</td>
            <td width="30%"><img src="{{asset('img/principal_sig.png')}}"><br/>----------------------------</td>
        </tr>
        <tr style="text-align: right;">
            <td width="70%">&nbsp;</td>
            <td width="30%">Principal's Signature</td>
        </tr>

        <tr style="text-align: center;">
            <td width="100%" colspan="2">বিঃ দ্রঃ পরীক্ষা হলে কোন অবস্থাতেই মোবাইল ফোন আনা যাবেনা।</td>
        </tr>
    </table>
    @if($i%2!=0)
   <div class="hr clear" style="border-top:1px solid #ccc;height:0px; margin:10px 0px; margin-top:20px; clear:both;">&nbsp;
   </div>
   @endif
<?php  $i++;?>
@endforeach

</body>
</html>
