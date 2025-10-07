<?php

$mpdf = new Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
    $mpdf->ignore_invalid_utf8 = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;
    // Specify a PDF template
    $pagecount = $mpdf->SetSourceFile(base_path('app/Libs/ongikarnama.pdf'));

    // Import the last page of the source PDF file
    $tplId = $mpdf->ImportPage($pagecount);
    $mpdf->UseTemplate($tplId);
    $session = numtobn($student->session);
    $mpdf->SetFont('solaimanlipi','R',15);
    $mpdf->WriteText(45, 42, $admitted_student->bangla_name);
    $mpdf->WriteText(130, 52, groupBnName($student->groups));
    $mpdf->WriteText(50, 62, numtobn($student->class_roll));
    $mpdf->WriteText(98, 64, numtobn($student->session));
    $mpdf->WriteText(63, 128, $admitted_student->bangla_name);
    $mpdf->WriteText(62, 138, numtobn($student->contact_no));
    $filename = $student->id."_admission_commitment.pdf";
    $file_path=public_path()."/download/hsc/";
    $mpdf->Output($file_path.'/'.$filename);
    echo "<center><a href='".url('/')."/download/hsc/".$filename."' target='_blank'>Click to Download</a></center>";