<?php
function num_eng_to_ben($english) {
    $bn_digits=array('০','১','২','৩','৪','৫','৬','৭','৮','৯');
    $bengali = str_replace(range(0, 9),$bn_digits, $english);
    return $bengali; 
}
    $id=$teacher->id;		
    $name=$teacher->name;		
    $department=$teacher->department;
    $position_name=$teacher->position;
    $reference_no=$teacher->reference_no;
    $transfer_to=$teacher->transfer_to;
    $outgoing_college=$teacher->outgoing_college;	
    $incoming_college=$teacher->incoming_college;	
    $release_date=$teacher->release_date;	
    $release_time=$teacher->release_time;
    $status=$teacher->status;
    $join_date=$teacher->join_date;

	$cur_month=date('F',strtotime($release_date));
	$cur_year=num_eng_to_ben(date('Y',strtotime($release_date)));
	$cur_date=num_eng_to_ben(date('d',strtotime($release_date)));
	$cur_day=date('l',strtotime($release_date));
	$cur_am_pm=date('A',strtotime($release_time));
	$cur_time_12=num_eng_to_ben(date('g:i',strtotime($release_time)));

	$position_name_ben=$position_name;
	$department_ben=$department;
	$id_ben=num_eng_to_ben($id);
	$reference_no_ben=num_eng_to_ben($reference_no);
	$college=config('settings.college_name_bn');
	$area=config('settings.college_district_bn');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Release Letter</title>
</head>
<body>
    <div class='table_container'>
        <table width='100%' class='sign'>
            <tr>
                <td align='left'>
                    বাংলাদেশ ফরম নং-২৪০৩<span style='float:right;font-size:10px'>DSHE- Artical 47 From</span><br/><br/><span style='padding-left:25px'>প্রতি,</span><br/><span style='padding-left:35px'>প্রধান হিসাব রক্ষণ অফিসার</span><br/><span style='padding-left:35px'>শিক্ষা মন্ত্রণালয়</span><br/><span style='padding-left:35px'>৪৫, পুরানা পল্টন, ঢাকা।</span>
                </td>			
            </tr>
    
            <tr>
                <td style='text-align:justify; padding-left:25px;'>
                    <br/><span style='padding-left:25px;text-align:justify;'>বেসামরিক </span> হিসাব পদ্ধতির ৪৭ নং অনুচ্ছেদের বিধি 
                    অনুযায়ী আমি/আমরা নিম্নস্বাক্ষরকারীগণ বিবরণ দিতেছি যে, 
                    আমরা {{$cur_month}}/{{$cur_year}} মাসের {{$cur_date}} তারিখ {{$cur_day}} 
                    {{$cur_am_pm}} {{$cur_time_12}} ঘটিকায় {{$college}}, {{$area}} এর 
                    <b>{{$position_name_ben}} ({{$department_ben}})</b> বিভাগ পদের দায়িত্বভার 
                    যথাক্রমে অর্পন ও গ্রহণ করিলাম। শিক্ষা মন্ত্রণালয়ের/মাধ্যমিক ও উচ্চশিক্ষা 
                    অধিদপ্তরের {{$reference_no_ben}}	সংখ্যক স্মারক মোতাবেক।
                </td>
    
                <td >
                    <br/><span style='padding-left:25px;'>
                </td>		
            </tr>
    
            <tr>
                <td style='padding-left:25px' align='right'>
                    <br/><br/><br/>
                    &nbsp;<br/>
                    &nbsp;<br/>
                    মুক্ত অফিসার
                </td>		
            </tr>
    
            <tr>
                <td style='padding-left:25px;' align='right'>
                    <br/><br/><br/>({{$name}})<br/><span style='font-size:10px'>(আইডি নং-{{$id_ben}})</span><br/>গ্রহণকারী অফিসার
                </td>		
            </tr>
    
            
    
        </table>
    
    
            <br/><table width='100%' class='letter' style='padding-left:30px;'>
            <tr>
                <td align='left'>
                    স্মারক নং-
                </td>
                <td align='right' style='padding-right:30px;'>
                    তারিখ : {{num_eng_to_ben(date('d/m/Y',strtotime($join_date)))}}
                </td>		
            </tr>
    
            <tr style='padding-left:30px;'>
                <td align='left' colspan='2'>
                    <br/>অনুলিপি অবগতি ও প্রয়োজনীয় কার্যার্থে প্রেরিত হইলঃ
                </td>		
            </tr>
    
            <tr style='padding-left:30px;'>
                <td align='left' colspan='2'>
                    ০১। সচিব, সংস্থাপন মন্ত্রণালয়, বাংলাদেশ সচিবালয়, ঢাকা।<br/>
                    ০২। সচিব, শিক্ষা মন্ত্রণালয়, বাংলাদেশ সচিবালয়, ঢাকা।<br/>
                    ০৩। মহাপরিচালক, মাধ্যমিক ও উচ্চশিক্ষা অধিদপ্তর, বাংলাদেশ, ঢাকা।<br/>
                    ০৪। অধ্যক্ষ, {{$incoming_college}}। (প্রবেশ)<br/>
                                    
                    ০৫। জনাব <span style='border-bottom: 1px solid #000000; display: inline-block; width:400px;'></span> {{$college}}, {{$area}}। (বিমুক্ত)<br/>
                    ০৬। জনাব {{$name}} ({{$id_ben}}), {{$department_ben}} বিভাগ, {{$college}}, {{$area}}। (গ্রহণকারী)<br/>
                    ০৭। প্রধান হিসাব রক্ষণ অফিসার, শিক্ষা মন্ত্রণালয়, ৪৫, পুরানা পল্টন, ঢাকা।<br/>
                    ০৮। জেলা হিসাব রক্ষণ অফিসার, রাজশাহী।<br/>
                    ০৯। ডকুমেণ্টেশন সেল, মাধ্যমিক ও উচ্চশিক্ষা অধিদপ্তর, ঢাকা।<br/>
                    ১০। আইসিটি সেল, {{$college}}, {{$area}}। <br/>
                    ১১। সংরক্ষণ নথি। <br/>
                </td>		
            </tr>
        </table>
        
    
        <table width='100%' class='foot'>
            <tr style='padding-right:25px;'>
                <td align='right' colspan='2'>
                    <div style='padding-right:20px'><br/><br/>অধ্যক্ষ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/> {{$college}},{{$area}}।</div>
                </td>		
            </tr>
        </table>
    </div>
    
</body>
</html>
