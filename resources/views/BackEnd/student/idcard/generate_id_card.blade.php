@php

$mpdf = new \Mpdf\Mpdf(
    array_merge(addCustomFontToMpdf(), 
        [
            'mode' => 'utf-8', 'format' => array(54.102,85.598),'default_font' => 'lato','font_size' => 14
        ]
    )
);

$mpdf->SetTitle('ID Card');
$mpdf->SetAuthor('Raj IT');
$mpdf->SetSubject(INS_CODE.' ID Card');
$mpdf->SetProtection(array('print','print-highres'));
$mpdf->ignore_invalid_utf8 = true;


foreach($student_info as $key => $val) {
    $mpdf->AddPageByArray([
        'margin-top'=> 0,
        'margin-bootom'=> 0,
        'margin-left'=> 0,
        'margin-right'=> 0,
    ]);

    $ref_id = $val->refference_id;
    $image = $val->image ?? '';
    $name = $val->name ?? '';
    $father_name = $val->father_name ?? '';
    $mother_name = $val->mother_name ?? '';
    $perm_village = $val->permanent_village;
    $perm_po = $val->permanent_po;
    $perm_ps = $val->permanent_ps;
    $perm_dist = $val->permanent_dist;
    $faculty_name = $val->faculty_name ?? '';
    $dept_name = $val->dept_name ?? '';
    $session = $val->session ?? '';
    $birth_date = $val->birth_date ?? '';
    $contact_no = $val->contact_no ?? '';
    $class_roll = $val->class_roll ?? '';
    $blood_group = $val->blood_group ?? '';
    $current_level = $val->current_level ?? '';
    $level=$current_level;
    $class=explode(' ', $level)[0];
    
    if($category == 'hsc') {
        $adm_table = 'hsc_admitted_students';
        $class = $class;
        $validity = '30-09-2026';
    }
    elseif($category == 'honours') {
        $adm_table = 'hons_admitted_student';
        $validity = '30-06-2030';
    }  
    elseif($category == 'masters') {
        $adm_table = 'masters_admitted_student';
        $validity = '31-12-2025';
    }   
    elseif($category == 'degree') {
        $adm_table = 'deg_admitted_student';
        $validity = '30-06-2028';
    }

    $admitted_student = DB::table($adm_table)->where('auto_id', $ref_id)->first();

    if(!is_null($admitted_student)){
        if(empty($blood_group)){
            $blood_group = $admitted_student->blood_group ?? '';
        }
    }
    

    if($type == '1'){
        if($category == 'hsc'):
            $last_three_id = substr($class_roll, -3);

            if($faculty_name == 'Science'){
                if ($last_three_id >= 1 && $last_three_id <= 150)
                    $house = 'Polashi';
                elseif($last_three_id >= 151 && $last_three_id <= 300)
                    $house = 'Ekushey';
                elseif($last_three_id >= 301 && $last_three_id <= 450)
                    $house = 'Racecourse';
                elseif($last_three_id >= 451 && $last_three_id <= 600)
                    $house = 'Mujib Nagar';
                else
                    $house = 'Unknown';

            }elseif($faculty_name == 'Humanities'){
                if ($last_three_id >= 1 && $last_three_id <= 150)
                    $house = 'Teknaf';
                elseif($last_three_id >= 151 && $last_three_id <= 300)
                    $house = 'Tetulia';
                else
                    $house = 'Unknown';

            }elseif($faculty_name == 'Business Studies'){
                if ($last_three_id >= 1 && $last_three_id <= 200)
                    $house = 'Chalan Beel';
                elseif($last_three_id >= 201 && $last_three_id <= 300)
                    $house = 'Saint Martin';
                else
                    $house = 'Unknown';
            }
        endif;


        if($category == 'hsc')
            $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/hsc_idcard_frame_front.pdf');
        if($category == 'masters')
            $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/masters_idcard_front.pdf');
        if($category == 'honours')
            $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/hons_idcard_front.pdf');
        if($category == 'degree')
            $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/degree_idcard_front.pdf');

        $tplIdx = $mpdf->ImportPage($pagecount);
        $mpdf->UseTemplate($tplIdx);
        $actualsize = $mpdf->UseTemplate($tplIdx);
        $mpdf->Image(public_path()."/upload/college/{$category}/{$session}/{$image}",17.5, 18.7, 19, 19.25);

        $css_style = 'style="width: 80%;
        margin: 1 auto 0;
        text-align: center;
        font-size:8pt;
        line-height:1;
        color:rgb(3,2,169);
        display:inline-block;
        float:left;
        font-weight:bold;"';
        
        $name_html = sprintf(
        '<div %s>
            <p>%s</p>
        </div>',$css_style,strtoupper($name));

        $mpdf->SetFont('lato', 'B', 8);
        $mpdf->WriteHTML($name_html);
        
        $mpdf->SetTextColor(0,0,255);
        if($category == 'honours'){
            $x_offset = 11;
            $y_offset = 31.5;
        }else{
            $x_offset = 5;
            $y_offset = 33;
        }
        $mpdf->SetFont('lato', 'BL', 7.8);
        $mpdf->SetTextColor(0,0,0);
        $mpdf->WriteText($x_offset+14.5, $y_offset+19.5,$class);
        $mpdf->WriteText($x_offset+14.5, $y_offset+24.5,$faculty_name);
        $mpdf->WriteText($x_offset+14.5, $y_offset+29,$class_roll);
        $mpdf->WriteText($x_offset+14.5, $y_offset+33.6,$session);
        if($category == 'hsc')
            $mpdf->WriteText($x_offset+14.5, $y_offset+38.3,$house ?? null);
        if($category != 'degree' && $category != 'hsc')
            $mpdf->WriteText($x_offset+14.5, $y_offset+38.3,$dept_name ?? null); 
    }else{
        if($category == 'hsc')
            $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/hsc_idcard_frame_back.pdf');
        if($category == 'masters')
            $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/masters_idcard_back.pdf');
        if($category == 'honours')
            $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/hons_idcard_back.pdf');
        if($category == 'degree')
            $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/degree_idcard_back.pdf');
        $tplIdx = $mpdf->ImportPage($pagecount);
        $mpdf->UseTemplate($tplIdx);
        $actualsize = $mpdf->UseTemplate($tplIdx);
        $x_offset = 24.3;
        $y_offset = 14.3;
        $mpdf->SetFont('lato', 'BL', 6.3);
        $mpdf->SetTextColor(0,0,0);
        $mpdf->WriteText($x_offset, $y_offset,$father_name);
        $mpdf->WriteText($x_offset, $y_offset+5,$mother_name);
        $mpdf->WriteText($x_offset, $y_offset+10,$perm_village);
        $mpdf->WriteText($x_offset, $y_offset+15,$perm_po);
        $mpdf->WriteText($x_offset, $y_offset+20,$perm_ps);
        $mpdf->WriteText($x_offset, $y_offset+25,$perm_dist);
        $mpdf->WriteText($x_offset, $y_offset+29.6,$contact_no);
        $mpdf->WriteText($x_offset, $y_offset+34.7,$blood_group);
        $mpdf->SetFont('lato', 'BL', 7.5);
        $mpdf->SetTextColor(255,255,255);
        $mpdf->WriteText($x_offset-2, $y_offset+69.4,$validity);
    }

}

// $file_name = public_path()."/download/idcard/id_cards.pdf";
echo $mpdf->Output('id card -'.$current_level.'.pdf',"I");
exit();

$downlink =  "<center><a href='".url('/')."/download/idcard/id_cards.pdf' target='_blank'>Click to Download</a></center>";

Session::put('downlink', $downlink);
return Redirect::route('students.idcard');
@endphp