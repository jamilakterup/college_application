@php
if($print_id==1)
{ 
    
    if($id!=9518 && $id!=1648)
    {
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
        
        foreach($results as $key => $val){
            
            $mpdf->AddPageByArray([
                'margin-top'=> 0,
                'margin-bootom'=> 0,
                'margin-left'=> 0,
                'margin-right'=> 0,
            ]);
            
            if(strlen($val['department'])>21)
            $pagecount = $mpdf->SetSourceFile(app_path().'/libs/teacher/teacher_front_2.pdf');
            else
            $pagecount = $mpdf->SetSourceFile(app_path().'/libs/teacher/teacher_front.pdf');
            
            $tplIdx = $mpdf->ImportPage($pagecount);
            $mpdf->UseTemplate($tplIdx);
            $actualsize = $mpdf->UseTemplate($tplIdx);
            
            $mpdf->Image($val['image'], 17.95, 23.3, 18.1, 21.65);
            
            $mpdf->SetTextColor(0,0,255);
            $css_style= 'style="width: 100%;
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
            
            $name_html =sprintf(
            '<div %s>
                <p>%s</p>
            </div>',$css_style,strtoupper($val['name']));
            
            $mpdf->WriteHTML($name_html);
            
            $x_offset = 5;
            $y_offset = 33;
            $value=$val['current_level'];
            $value=explode(' ', $value);
            
            $mpdf->SetFont('lato', 'B', 8);
            $mpdf->SetTextColor(0,0,0);
            $mpdf->WriteText($x_offset+19, $y_offset+22.9,$val['teacher_id']);
            $mpdf->WriteText($x_offset+19, $y_offset+26.9,$val['designation']);
            if(strlen($val['department'])>21)
            {
                $temp_dept=$val['department'];
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
                
                $mpdf->WriteText($x_offset+19, $y_offset+29.8,$dep_name1);
                $mpdf->WriteText($x_offset+19, $y_offset+32.8,$dep_name2);
                
                $mpdf->WriteText($x_offset+19, $y_offset+36.8,$val['personal_mobile']);
                $mpdf->WriteText($x_offset+19, $y_offset+40.7,$val['blood_group']);
                $mpdf->Image(url('/')."/barcode.php?code={$val['teacher_id']}", $x_offset+2, $y_offset +44, 40, 3.5);
            }
            else
            {
                $mpdf->WriteText($x_offset+19, $y_offset+30.5,$val['department']);
                $mpdf->WriteText($x_offset+19, $y_offset+34.5,$val['personal_mobile']);
                $mpdf->WriteText($x_offset+19, $y_offset+38.4,$val['blood_group']);
                $mpdf->Image(url('/')."/barcode.php?code={$val['teacher_id']}", $x_offset+2, $y_offset +44, 40, 3.5);
            }
        }
        
        $fname=$category.'-Teacher-Front-Side.pdf';
        $file_name=public_path()."/download/teacher/idcard/{$fname}";
        $mpdf->Output($file_name,"F");
        
        $downlink = "<center><a href='".url('/')."/download/teacher/idcard/{$fname}' target='_blank'>Click to Download</a></center>";
        Session::flash('downlink', $downlink);
        echo $downlink;
    }
    else
    {    
        
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
        
        foreach($results as $key => $val){
            $mpdf->AddPageByArray([
                'margin-top'=> 0,
                'margin-bootom'=> 0,
                'margin-left'=> 0,
                'margin-right'=> 0,
            ]);
            $pagecount = $mpdf->SetSourceFile(app_path().'/libs/teacher/vc_front.pdf');
            $tplIdx    = $mpdf->ImportPage($pagecount);
            $mpdf->UseTemplate($tplIdx);
            $actualsize = $mpdf->UseTemplate($tplIdx);
            
            
            $mpdf->Image($val['image'], 17.1, 27.29, 19.8, 23.7);
            
            $mpdf->SetTextColor(0,0,255);
            $css_style= 'style="width: 100%;
            margin-top:-8px;
            margin-right: auto;
            margin-bottom: auto;
            margin-left: 0px;
            text-align: center;
            color:blue;
            padding:0px 10px;
            font-size:9pt;
            line-height:1;
            font-weight:bold;"';
            
            $name_html =sprintf(
            '<div %s>
                <p>%s</p>
            </div>',$css_style,strtoupper($val['name']));
            
            $mpdf->WriteHTML($name_html);
            
            $x_offset = 5;
            $y_offset = 33;
            $value=$val['current_level'];
            $value=explode(' ', $value);
            
            $tech='0000'.$val['teacher_id'];
            $mpdf->SetFont('lato', 'B', 8);
            $mpdf->SetTextColor(0,0,0);
            $mpdf->WriteText($x_offset+21.5, $y_offset+27.75,$tech);
            $mpdf->WriteText($x_offset+21.5, $y_offset+31.85,$val['designation']);
            $mpdf->WriteText($x_offset+21.5, $y_offset+35.75,$val['personal_mobile']);
            $mpdf->WriteText($x_offset+21.5, $y_offset+39.5,$val['phone_office']);
            $mpdf->WriteText($x_offset+21.5, $y_offset+43.5,$val['blood_group']);
            
        }
        
        $fname=$val['teacher_id'].'-Teacher-Front-Side.pdf';
        $file_name=public_path()."/download/teacher/idcard/{$fname}";
        $mpdf->Output($file_name,"F");
        
        $downlink = "<center><a href='".url('/')."/download/teacher/idcard/{$fname}' target='_blank'>Click to Download</a></center>";
        Session::flash('downlink', $downlink);
        echo $downlink;		
    }	
    
    
}
else
{
    if($id!=9518 && $id!=1648)
    {	
        
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
        
        
        foreach($results as $key => $val){ 
            
            
            $mpdf->AddPageByArray([
                'margin-top'=> 0,
                'margin-bootom'=> 0,
                'margin-left'=> 0,
                'margin-right'=> 0,
            ]);
            $pagecount = $mpdf->SetSourceFile(app_path().'/libs/teacher/teacher_Back.pdf');
            $tplIdx    = $mpdf->ImportPage($pagecount);
            $mpdf->UseTemplate($tplIdx);
            $actualsize = $mpdf->UseTemplate($tplIdx);
            
            
            $x_offset = 5;
            $y_offset = 3;
            
            $mpdf->SetFont('lato', 'B', 7);
            $mpdf->SetTextColor(0,0,0);
            $mpdf->WriteText($x_offset+11.75, $y_offset+10.9,$val['spouse_name']);
            $mpdf->WriteText($x_offset+11.75, $y_offset+15,$val['relation']);
            
            $mpdf->Image(public_path()."/upload/college/teacher/pix.jpg", 16, 14, 1,1);
            $css_style= 'style="width: 100%;
            margin-top:6px;
            margin-right: auto;
            margin-bottom: auto;
            margin-left: 0px;
            text-align: left;
            padding:0px 5px 0px 64px;
            font-size:7pt;
            line-height:1;
            font-weight:bold;
            "';
            
            $name_html =sprintf(
            '<div %s>
                <p>%s</p>
            </div>',$css_style,$val['address']);
            
            $mpdf->WriteHTML($name_html);
            
            
            
            $mpdf->SetFont('lato', 'B', 7);
            $mpdf->WriteText($x_offset+11.75, $y_offset+26.5,$val['spouse_phone']);
            $mpdf->WriteText($x_offset+11.75, $y_offset+30.35,$val['spouse_mobile']);
            $mpdf->WriteText($x_offset+23, $y_offset+34.25,$val['phone_office']);
            
            
            
        }
        
        $fname=$category.'-Teacher-Back-Side.pdf';
        $file_name=public_path()."/download/teacher/idcard/{$fname}";
        $mpdf->Output($file_name,"F");
        
        $downlink = "<center><a href='".url('/')."/download/teacher/idcard/{$fname}' target='_blank'>Click to Download</a></center>";
        Session::flash('downlink', $downlink);
        echo $downlink; 
    }	
    
    else
    {
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
        
        
        foreach($results as $key => $val){ 
            
            
            $mpdf->AddPageByArray([
                'margin-top'=> 0,
                'margin-bootom'=> 0,
                'margin-left'=> 0,
                'margin-right'=> 0,
            ]);
            $pagecount = $mpdf->SetSourceFile(app_path().'/libs/teacher/vc_back.pdf');
            $tplIdx    = $mpdf->ImportPage($pagecount);
            $mpdf->UseTemplate($tplIdx);
            $actualsize = $mpdf->UseTemplate($tplIdx);
            
            $x_offset = 5;
            $y_offset = 3;
            
            $mpdf->SetFont('lato', 'B', 7);
            $mpdf->SetTextColor(0,0,0);
            $mpdf->WriteText($x_offset+12, $y_offset+11.9,$val['spouse_name']);
            $mpdf->WriteText($x_offset+12, $y_offset+15.9,$val['relation']);
            //
            $mpdf->Image(public_path()."/upload/college/teacher/pix.jpg", 16, 14.5, 1,1);
            $css_style= 'style="width: 90%;
            margin-top:5.7px;
            margin-right: auto;
            margin-bottom: auto;
            margin-left: 0px;
            text-align: left;
            padding:0px 12px 0px 64px;
            font-size:7pt;
            line-height:1.5;
            font-weight:bold;
            
            "';
            //
            $name_html =sprintf(
            '<div  %s>
                <p>%s</p>
            </div>',$css_style,$val['address']);
            
            $mpdf->WriteHTML($name_html);
            
            $mpdf->SetFont('lato', 'B', 7);
            $mpdf->WriteText($x_offset+12, $y_offset+27.7,$val['spouse_phone']);
            $mpdf->WriteText($x_offset+12, $y_offset+31.3,$val['spouse_mobile']);
            
            
        }
        
        $fname=$val['teacher_id'].'-Teacher-Back-Side.pdf';
        $file_name=public_path()."/download/teacher/idcard/{$fname}";
        $mpdf->Output($file_name,"F");
        
        $downlink = "<center><a href='".url('/')."/download/teacher/idcard/{$fname}' target='_blank'>Click to Download</a></center>";
        Session::flash('downlink', $downlink);
        echo $downlink;
    }	
    
}
@endphp