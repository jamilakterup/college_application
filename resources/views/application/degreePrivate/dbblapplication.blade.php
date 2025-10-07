<!DOCTYPE html>
<html>
<head>
    <title>Online Masters Private Registration</title>
    <link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-datepicker3.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
</head>

<body>
  
  @php
    $tracking_id=Session::get('tracking_id');
    $invoice_id=Session::get('invoice_id');
    $admission_roll=Session::get('admission_roll');

    $auto_id= deg_tracking_auto_id($tracking_id);

    $configs = DB::table('admission_config')->where('course', 'degree')->where('type', 'registration')->where('open', 1)->where('current_level', 'Degree 2nd Year')->get();

    if(count($configs) < 1){
      echo '<h2 style="text-align:center;color:red;">Application Not Open</h2>';
      exit();
    }

    foreach($configs as $result){
            $current_admission_session = $result->session;            
            $session = $result->session;     
    }

    $class_roll = '';
    $college_id = '';
    $results = DB::table('degree_application_admitted_student')->where('admission_roll', $admission_roll)->where('session', $current_admission_session)->where('auto_id',$auto_id)->get();

    foreach($results as $result){
        $admission_roll = $result->admission_roll;
        $name = $result->name;
        $father_name = $result->father_name;
        $subject = $result->dept_name;
        $session = $result->session;
        $contact_no = $result->contact_no;
        $invoice_id = $result->application_invoice_id;
    }

      $invoices = DB::table('invoices')->where('id', $invoice_id)->where('admission_session', $session)->where('roll', $admission_roll)->where('type', 'degree_registration')->get();

      if(count($invoices) > 0){
        $invoice = $invoices->first();
        $pending_amount = $invoice->total_amount;
        $paymentDate = $invoice->txndate;
        $payment_status = $invoice->status;
      }else{
        $pending_amount = 0;
        $paymentDate = 'N/A';
      }


      $student_info = DB::table('degree_student_applications')->where('refference_id', $auto_id)->where('current_level', 'Masters 2nd Year')->where('session', $current_admission_session)->get();
      $total_count = count($student_info);
  @endphp


  <nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">{{config('settings.college_name')}} Degree Private Online Registration</a>
    </div>
    <a href="<?php echo url('/').'/Registration/Degree/Private' ?>" class="btn btn-danger navbar-btn">Registration</a>
    <a href="<?php echo url('/').'/Registration/Degree/Private/logout'?>" class="btn btn-danger navbar-btn">Logout</a>
  </div>
</nav>

<div class="container">
  
  <div class="panel-group clearfix">


       <div class="col-md-12">
       <p style="color:red;">{{ Session::get('res') }}</p>
       @if($payment_status=="Paid")
        <p class="alert alert-warning text-center"><strong class="text-danger">আপনার টাকা সফল্ভাবে জমা হয়েছে, দয়া করে আপনার কনফার্ম স্লিপ ডাউনলোড করুন।</strong></p>
       @endif
        <div class="panel {{$payment_status!="Paid" ? 'panel-info': 'panel-success text-center'}} col-md-10 col-md-offset-1">
        <div class="panel-heading"> {{$payment_status!="Paid"? 'DBBL Payment Method':'Please Download Your Confirmation Slip'}}</div>   
            <div class="panel-body">
              
            @if($payment_status!="Paid")
                @php
                  $biller_id = config('settings.college_biller_id');
                  $college_name_bn = config('settings.college_name_bn');
                  $student_id = $admission_roll;
                  $payment_guideline = get_config('deg_private_reg_payment_guideline');

                  if($payment_guideline){
                    echo @configTempleteToBody($payment_guideline,['student_id'=> $admission_roll, 'college_name_bn'=> $college_name_bn, 'biller_id'=> $biller_id,'total_amount'=>round($total_amount)]);
                  }
                @endphp
                <div class="row text-center">
                    <button class="btn btn-primary text-center" onClick="window.location.reload();">Confirm To Download Confirmation Slip</button>
                </div>
            @endif

              <?php if($payment_status=="Paid"){ ?>
                <a style="cursor: pointer;" id="confirm_slip" target="_blank">Download Confirmation Slip</a>
              <?php } ?>
            </div>
          </div>

        </div>

       </div>
       @if (count($results) > 0)
         <div class="row mb">
        <div class="col-md-12">
          <div class="panel panel-info col-md-10 col-md-offset-1">
            <div class="panel-heading"> Application Details </div>
            <div class="panel-body vcard">
              <ul>

                <li>
                    <span class="item-key">Full name</span>
                    <div class="vcard-item"><?php echo $name;?></div>
                </li>

                <li>
                    <span class="item-key">Contact No</span>
                    <div class="vcard-item"><?php echo $contact_no;?></div>
                </li>

              <li>
                <span class="item-key">Admission Roll</span>
                <div class="vcard-item"><?php echo $admission_roll;?></div>
              </li>

              <li>
                <span class="item-key">Subject</span>
                <div class="vcard-item"><?php echo $subject;?></div>
              </li>

              <li>
                  <span class="item-key">Payment Status</span>
                  <div class="vcard-item">{!!$payment_status == 'Pending' ? '<span class="text-danger">'.$payment_status.'</span>':'<span class="text-success">'.$payment_status.'</span>'!!}</div>
              </li>       
            </ul>
            </div>
          </div>
        </div>

              
      </div>

       @endif
    </div>
</div>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('fjs/degree_private_reg.js') }}"></script>
<script src="{{ asset('js/loadingoverlay.min.js') }}"></script>

</body>


</html>

<div class="modal fade" id="download_form_file" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div id="download_link1"><p>Please Wait....</p></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

    <script type="text/javascript">
  </script>