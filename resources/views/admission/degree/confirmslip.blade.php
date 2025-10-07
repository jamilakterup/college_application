<?php 	

use Mpdf\Mpdf;

            $auto_id = Session::get('auto_id');
            $invoice_id = Session::get('invoice_id');
            $admission_roll = Session::get('admission_roll');
            $tracking_id =HSC_PREF.$auto_id ;

            $invoice = DB::table('invoices')->where('id', $invoice_id)->first();
			$admited_student = DB::table('hons_admitted_student')->where('admission_invoice_id', $invoice_id)->where('auto_id', $auto_id)->first();
			$amount= DB::table('payment_info')->where('refference_id',$tracking_id)->first();
			
			$name = $admited_student->name;
			$fathers_name = $admited_student->father_name;
			$mothers_name = $admited_student->mother_name;
			$paid_amount =$invoice->total_amount;
	

        $currentYear = substr($admited_student->session, 0, strrpos($admited_student->session, '-'));
    	

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
        $mpdf->ignore_invalid_utf8 = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoVietnamese = true;
        $mpdf->autoArabic = true;
        $mpdf->autoLangToFont = true;
        $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/masters_application_confirmation.pdf');
        $tplId = $mpdf->ImportPage($pagecount);
        $actualsize = $mpdf->SetPageTemplate($tplId);
        $mpdf->AddPage();
        $mpdf->SetFont('Times','B',16);
        //$mpdf->WriteText(77, 57.5, $currentYear);
        $mpdf->SetFont('siyamrupali','',12);  
        $mpdf->WriteText(95, 79.5, strtoupper($name));    
        $mpdf->WriteText(95, 75.5, strtoupper($fathers_name));
        //$mpdf->WriteText(95, 85.5, strtoupper($mothers_name));
        //$mpdf->WriteText(95, 116.5, $tracking_id);    
        //$mpdf->WriteText(95, 136.5, $invoice->trx_id);
        //$mpdf->WriteText(95, 147, $invoice->total_amount);

		$file_name=public_path()."/download/masters/{$tracking_id}_application.pdf";
	   $mpdf->Output($file_name);	
		
		
		echo "<center><a href='".url('/')."/download/masters/{$tracking_id}_application.pdf' target='_blank'>Click to Download</a></center>";
?>


