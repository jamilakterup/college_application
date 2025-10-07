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

    $auto_id= msc_tracking_auto_id($tracking_id);

    $configs = DB::table('admission_config')->where('course', 'masters')->where('type', 'registration')->where('open', 1)->where('current_level', 'Masters 2nd Year')->get();

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
    $results = DB::table('masters_application_admitted_student')->where('admission_roll', $admission_roll)->where('session', $current_admission_session)->where('auto_id',$auto_id)->get();
 

    foreach($results as $result){
        $admission_roll = $result->admission_roll;
        $name = $result->name;
        $father_name = $result->father_name;
        $subject = $result->dept_name;
        $session = $result->session;
        $contact_no = $result->contact_no;
        $invoice_id = $result->application_invoice_id;
    }

      $invoices = DB::table('invoices')->where('id', $invoice_id)->where('admission_session', $session)->where('roll', $admission_roll)->where('type', 'masters_registration')->get();

      if(count($invoices) > 0){
        $invoice = $invoices->first();
        $pending_amount = $invoice->total_amount;
        $paymentDate = $invoice->txndate;
        $payment_status = $invoice->status;
      }else{
        $pending_amount = 0;
        $paymentDate = 'N/A';
      }


      $student_info = DB::table('masters_student_applications')->where('refference_id', $auto_id)->where('current_level', 'Masters 2nd Year')->where('session', $current_admission_session)->get();
      $total_count = count($student_info);
  @endphp


  <nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">{{config('settings.college_name')}} Masters Private Online Registration</a>
    </div>
    <a href="<?php echo url('/').'/Registration/Masters/Private' ?>" class="btn btn-danger navbar-btn">Registration</a>
    <a href="<?php echo url('/').'/Registration/Masters/Private/logout'?>" class="btn btn-danger navbar-btn">Logout</a>
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
             <?php if($payment_status!="Paid"){ ?>
          <h4 class="text-center text-danger">* পূরণকৃত তথ্য যাচাই করে টাকা পরিশোধ করুন</h4>
          <ol>
            <li>যেসকল মোবাইলে DBBL মোবাইল এ্যাকাউন্ট রয়েছে,সেই মোবাইল থেকে <b>*322#</b> ডায়াল করে DBBL মোবাইল ব্যাংক মেনুতে যান
          </li>
          <li>
             পেমেন্ট <b>1</b> (One) সিলেক্ট করুন
          </li>
          
          <li>
             Self <b>1</b> (One) সিলেক্ট করুন
          </li>

          <li>
             Other <b>2</b> (Two) সিলেক্ট করুন
          </li>
          
          <li>
             Other <b>0</b> (Zero) সিলেক্ট করু
          </li>
          
          <li>
            {{config('settings.college_name_bn')}} এর  Biller ID <b>{{config('settings.college_biller_id')}}</b> টাইপ করুন
          </li> 
             <li>
            Bill No-এ আপনার  Admission Roll <strong><?php echo $admission_roll ?></strong> দিন
          </li>
          <li>
            টাকার পরিমাণ <strong> <?php echo round($total_amount) ;?> </strong> লিখুন 
          </li>          
          <li>
            <b>PIN</b> টাইপ করুন
          </li>
          <li>
            OK/Send বাটন চাপুন
          </li>

          <li>
            পেমেন্ট সফলভাবে সম্পূর্ণ হলে পেজটি রিফ্রেশ করুন এবং আপনার কনফার্মেশন স্লিপ টি ডাউনলোড করুন|
          </li>
        </ol>
              <?php } ?>
                <?php if($payment_status=="Paid"){ ?>
                  <a style="cursor: pointer;" id="confirm_slip" target="_blank">Download Confirmation Slip</a>
                  <br/> <br/>
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
                    <span class="item-key">Father's name</span>
                    <div class="vcard-item"><?php echo $father_name;?></div>
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
<script src="{{ asset('fjs/masters_private_reg.js') }}"></script>
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