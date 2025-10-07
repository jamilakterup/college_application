<?php
	 defined('COLLEGE_NAME_BENGALI')? null:define("COLLEGE_NAME_BENGALI",'বিল চলন শহিদ শামসুজ্জোহা কলেজ');
	 defined('AREA_NAME_BENGALI')? null:define("AREA_NAME_BENGALI",'নাটোর');
	define('PREF_LEN', 2);
	
	define('HSC_PREF', '11');
    define('DEGREE_PREF', '22');
    define('HONS_PREF', '33');
    define('MSC2ND_PREF', '44');
    define('MSC1ST_PREF', '55');
    define('INS_CODE', 'ngdc');
    define('SMS_USERNAME', 'ecm_ngc1');
    define('SMS_PASSWORD', 'ngc55196');
  
   function get_info_by_dbbl_trxid($transaction_id){

		$username = "dbill";
		$password = "dBILL!23";
		
		$remote_url = "https://mbsrv.dutchbanglabank.com/BillPayGW/BillInfoService?shortcode=244&userid=RGDC101&password=j874hejduierunaigt&opcode=GT&txnid=".$transaction_id;
		
		$opts = array(
		    'http' => array(
		        'method' => "GET",
		        'host' => '10.10.200.142',
		        'header' => "Authorization: Basic " . base64_encode("$username:$password")
		    )
		);

		$context = stream_context_create($opts);
		$file = file_get_contents($remote_url, false, $context);

		$res = explode('|', $file);

		if (count($res)>2){
			$transaction_array['response']="ok";
			$transaction_array['trx_id']=$transaction_id;
			$transaction_array['bill_id']=$res[1];
			$transaction_array['amount']=$res[2];
			$transaction_array['payment_date']=$res[3];
		}
		else{
			$transaction_array['response']="Error";
		}
		return $transaction_array;

	}	
	
	
   function get_info_by_trxid_honours_formfillip($transaction_id){

		$username = "dbill";
		$password = "dBILL!23";

		// return $transaction_array['response']="Error";
		
		$remote_url = "https://mbsrv.dutchbanglabank.com/BillPayGW/BillInfoService?shortcode=2304&userid=RGWHNS03101&password=j874hejduierunaigt&opcode=GT&txnid=".$transaction_id;
		
		$opts = array(
		    'http' => array(
		        'method' => "GET",
		        'host' => '10.10.200.142',
		        'header' => "Authorization: Basic " . base64_encode("$username:$password")
		    )
		);

		$context = stream_context_create($opts);
		$file = file_get_contents($remote_url, false, $context);

		$res = explode('|', $file);

		/*var_dump($res);
		die();*/

		if (count($res)>2){
			$transaction_array['response']="ok";
			$transaction_array['trx_id']=$transaction_id;
			$transaction_array['bill_id']=$res[1];
			$transaction_array['amount']=$res[2];
			$transaction_array['payment_date']=$res[3];
		}
		else{
			$transaction_array['response']="Error";
		}
		return $transaction_array;

	}	


function get_info_by_trxid_honours_fourth_formfillip($transaction_id){

		$username = "dbill";
		$password = "dBILL!23";

		// return $transaction_array['response']="Error";
		
		$remote_url = "https://mbsrv.dutchbanglabank.com/BillPayGW/BillInfoService?shortcode=2305&userid=RGWHNS101&password=j874hejduierunaigt&opcode=GT&txnid=".$transaction_id;
		
		$opts = array(
		    'http' => array(
		        'method' => "GET",
		        'host' => '10.10.200.142',
		        'header' => "Authorization: Basic " . base64_encode("$username:$password")
		    )
		);

		$context = stream_context_create($opts);
		$file = file_get_contents($remote_url, false, $context);

		$res = explode('|', $file);

		/*var_dump($res);
		die();*/

		if (count($res)>2){
			$transaction_array['response']="ok";
			$transaction_array['trx_id']=$transaction_id;
			$transaction_array['bill_id']=$res[1];
			$transaction_array['amount']=$res[2];
			$transaction_array['payment_date']=$res[3];
		}
		else{
			$transaction_array['response']="Error";
		}
		return $transaction_array;

	}


function get_info_by_trxid_honours_second_formfillip($transaction_id){

		$username = "dbill";
		$password = "dBILL!23";

		// return $transaction_array['response']="Error";
		
		$remote_url = "https://mbsrv.dutchbanglabank.com/BillPayGW/BillInfoService?shortcode=2303&userid=RGWHNS02101&password=j874hejduierunaigt&opcode=GT&txnid=".$transaction_id;
		
		$opts = array(
		    'http' => array(
		        'method' => "GET",
		        'host' => '10.10.200.142',
		        'header' => "Authorization: Basic " . base64_encode("$username:$password")
		    )
		);

		$context = stream_context_create($opts);
		$file = file_get_contents($remote_url, false, $context);

		$res = explode('|', $file);

		/*var_dump($res);
		die();*/

		if (count($res)>2){
			$transaction_array['response']="ok";
			$transaction_array['trx_id']=$transaction_id;
			$transaction_array['bill_id']=$res[1];
			$transaction_array['amount']=$res[2];
			$transaction_array['payment_date']=$res[3];
		}
		else{
			$transaction_array['response']="Error";
		}
		return $transaction_array;

	}	

	
   function get_info_by_trxid_master($transaction_id){


		$username = "dbill";
		$password = "dBILL!23";

		// return $transaction_array['response']="Error";
		
		$remote_url = "https://mbsrv.dutchbanglabank.com/BillPayGW/BillInfoService?shortcode=2306&userid=RGWCMS101&password=j874hejduierunaigt&opcode=GT&txnid=".$transaction_id;
		
		$opts = array(
		    'http' => array(
		        'method' => "GET",
		        'host' => '10.10.200.142',
		        'header' => "Authorization: Basic " . base64_encode("$username:$password")
		    )
		);
		
		$opts = array(
		    'http' => array(
		        'method' => "GET",
		        'host' => '10.10.200.142',
		        'header' => "Authorization: Basic " . base64_encode("$username:$password")
		    )
		);

		$context = stream_context_create($opts);
		$file = file_get_contents($remote_url, false, $context);

		$res = explode('|', $file);

		/*var_dump($res);
		die();*/

		if (count($res)>2){
			$transaction_array['response']="ok";
			$transaction_array['trx_id']=$transaction_id;
			$transaction_array['bill_id']=$res[1];
			$transaction_array['amount']=$res[2];
			$transaction_array['payment_date']=$res[3];
		}
		else{
			$transaction_array['response']="Error";
		}
		return $transaction_array;

	}




   function get_info_by_trxid_deg_admission($transaction_id){


		$username = "dbill";
		$password = "dBILL!23";

		// return $transaction_array['response']="Error";
		
		$remote_url = "https://mbsrv.dutchbanglabank.com/BillPayGW/BillInfoService?shortcode=267&userid=RGWCMS501&password=j874hejduierunaigt&opcode=GT&txnid=".$transaction_id;
		
		$opts = array(
		    'http' => array(
		        'method' => "GET",
		        'host' => '10.10.200.142',
		        'header' => "Authorization: Basic " . base64_encode("$username:$password")
		    )
		);
		
		$opts = array(
		    'http' => array(
		        'method' => "GET",
		        'host' => '10.10.200.142',
		        'header' => "Authorization: Basic " . base64_encode("$username:$password")
		    )
		);

		$context = stream_context_create($opts);
		$file = file_get_contents($remote_url, false, $context);

		$res = explode('|', $file);

		/*var_dump($res);
		die();*/

		if (count($res)>2){
			$transaction_array['response']="ok";
			$transaction_array['trx_id']=$transaction_id;
			$transaction_array['bill_id']=$res[1];
			$transaction_array['amount']=$res[2];
			$transaction_array['payment_date']=$res[3];
		}
		else{
			$transaction_array['response']="Error";
		}
		return $transaction_array;

	}
	
	
   function get_info_by_trxid_hsc_formfillup($transaction_id){


		$username = "dbill";
		$password = "dBILL!23";

		// return $transaction_array['response']="Error";
		
		$remote_url = "https://mbsrv.dutchbanglabank.com/BillPayGW/BillInfoService?shortcode=2008&userid=RGWHNS01101&password=j874hejduierunaigt&opcode=GT&txnid=".$transaction_id;
		
		$opts = array(
		    'http' => array(
		        'method' => "GET",
		        'host' => '10.10.200.142',
		        'header' => "Authorization: Basic " . base64_encode("$username:$password")
		    )
		);
		
		$opts = array(
		    'http' => array(
		        'method' => "GET",
		        'host' => '10.10.200.142',
		        'header' => "Authorization: Basic " . base64_encode("$username:$password")
		    )
		);

		$context = stream_context_create($opts);
		$file = file_get_contents($remote_url, false, $context);

		$res = explode('|', $file);

		/*var_dump($res);
		die();*/

		if (count($res)>2){
			$transaction_array['response']="ok";
			$transaction_array['trx_id']=$transaction_id;
			$transaction_array['bill_id']=$res[1];
			$transaction_array['amount']=$res[2];
			$transaction_array['payment_date']=$res[3];
		}
		else{
			$transaction_array['response']="Error";
		}
		return $transaction_array;

	}	




function get_info_by_trxid_hsc_admission($transaction_id){


		$username = "dbill";
		$password = "dBILL!23";

		// return $transaction_array['response']="Error";
		
		$remote_url = "https://mbsrv.dutchbanglabank.com/BillPayGW/BillInfoService?shortcode=264&userid=RGWC101&password=j874hejduierunaigt&opcode=GT&txnid=".$transaction_id;
		
		$opts = array(
		    'http' => array(
		        'method' => "GET",
		        'host' => '10.10.200.142',
		        'header' => "Authorization: Basic " . base64_encode("$username:$password")
		    )
		);
		
		$opts = array(
		    'http' => array(
		        'method' => "GET",
		        'host' => '10.10.200.142',
		        'header' => "Authorization: Basic " . base64_encode("$username:$password")
		    )
		);

		$context = stream_context_create($opts);
		$file = file_get_contents($remote_url, false, $context);

		$res = explode('|', $file);

		/*var_dump($res);
		die();*/

		if (count($res)>2){
			$transaction_array['response']="ok";
			$transaction_array['trx_id']=$transaction_id;
			$transaction_array['bill_id']=$res[1];
			$transaction_array['amount']=$res[2];
			$transaction_array['payment_date']=$res[3];
		}
		else{
			$transaction_array['response']="Error";
		}
		return $transaction_array;

	}
	
   ?>