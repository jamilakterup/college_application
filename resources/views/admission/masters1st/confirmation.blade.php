<!DOCTYPE html>
<html>
<head>
  <title>Online Masters Part-1 Admission</title>
  <link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/css/bootstrap.min.css">
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/css/font-awesome.min.css">
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/css/bootstrap-datepicker3.min.css">
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/css/style.css">

</head>

<body>
  
<?php

$tracking_id=Session::get('tracking_id');

 $auto_id=msc1st_tracking_auto_id($tracking_id);
$admission_session = Session::get('admission_session');
$admission_roll = Session::get('admission_roll');

  $payment_status = '';
  $class_roll = '';
  $college_id = '';
  $results = DB::table('masters_admitted_student')->where('admission_roll', $admission_roll)->where('auto_id',$auto_id)->get();
 

  foreach($results as $result){
    $auto_id = $result->auto_id;
    $entry_time = $result->entry_time;
    $name = $result->name;
    $father_name = $result->father_name;
    $father_income = $result->father_income;
    $mother_name = $result->mother_name;
    $birth_date = $result->birth_date;
    $gender = $result->gender;
    $permanent_email = $result->permanent_email;
    $email = $result->email;
    $password = $result->password;
    $permanent_mobile = $result->permanent_mobile;
    $contact_no = $result->contact_no;
    $photo = $result->photo;
    $religion = $result->religion;
    $blood = $result->blood_group;
    $permanent_village = $result->permanent_village;
    $present_village = $result->present_village;
    $permanent_po = $result->permanent_po;
    $present_po = $result->present_po;
    $permanent_ps = $result->permanent_ps;
    $present_ps = $result->present_ps;
    $permanent_dist = $result->permanent_dist;
    $present_dist = $result->present_dist;
    $guardian_name = $result->guardian_name;
    $guardian_contact = $result->guardian_contact;
    $guardian_relation = $result->guardian_relation;
    $guardian_income = $result->guardian_income;
    $guardian_occupation = $result->guardian_occupation;
    $ssc_roll = $result->ssc_roll;
    $ssc_institute = $result->ssc_institute;
    $ssc_board = $result->ssc_board;
    $ssc_gpa = $result->ssc_gpa;
    $ssc_pass_year = $result->ssc_pass_year;
    $hsc_roll = $result->hsc_roll;
    $hsc_institute = $result->hsc_institute;
    $hsc_board = $result->hsc_board;
    $hsc_gpa = $result->hsc_gpa;
    $hsc_pass_year = $result->hsc_pass_year;
    $payment_status = $result->payment_status;
    $paid_date = $result->paid_date;
    $complete_sms = $result->complete_sms;
    $sent_time = $result->sent_time;
    $status = $result->status;
    $honrs_passing_institute = $result->honrs_passing_institute;
    $honrs_passing_year = $result->honrs_passing_year;
    $honrs_passing_cgpa = $result->honrs_passing_cgpa;
    $honrs_session = $result->honrs_session;
    $from_faculty = $result->from_faculty;
    $to_faculty = $result->to_faculty;
    $from_subject = $result->from_subject;
    $to_subject = $result->to_subject;
    $session = $result->session;
    $admission_roll= $result->admission_roll;
    $invoice_id = $result->admission_invoice_id;
  }

$invoices = DB::table('invoices')->where('id', $invoice_id)->where('admission_session', $session)->where('roll', $admission_roll)->where('type', 'masters_admission')->get();

  if(count($invoices) > 0){
    $invoice = $invoices->first();
    $pending_amount = $invoice->total_amount;
      $paymentDate = $invoice->txndate;
  }else{
    $pending_amount = 0;
    $paymentDate = 'N/A';
  }
  

    $results_ad_ses = DB::table('admission_config')->where('course', 'masters')->where('current_level', 'Masters 1st Year')->where('open', 1)->get();

    if(count($results_ad_ses) < 1){
      echo '<h2 style="text-align:center;color:red;">Admission Not Open</h2>';
      exit();
    }

    foreach($results_ad_ses as $result){
            $current_admission_session = $result->session;            
        }


    $results = DB::select("SELECT class_roll,id FROM student_info_masters WHERE refference_id=$auto_id 
    and session='$current_admission_session' and  current_level='Masters 1st Year'");
    $total_count = count($results);
    if($total_count>0){
        foreach($results as $result){
            $class_roll = $result->class_roll;
            $college_id  = $result->id;
            $payment_status = "Paid";
        }
    }
    else{
        $class_roll = "<span style='color:red;'>Pending</span>";
        $payment_status = "<span style='color:red;'>Pending</span>";
        $college_id = "<span style='color:red;'>Pending</span>";
    }
?>
  <nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">{{config('settings.college_name')}} {{config('settings.college_name') !='' ? ', '.config('settings.college_district'):''}} Online Masters Part-1 Admission</a>
    </div>
    <a href="<?php echo url('/').'/Admission/Masters1st' ?>" class="btn btn-danger navbar-btn">Admission</a>
    <a href="<?php echo url('/').'/Admission/Masters1st/logout'?>" class="btn btn-danger navbar-btn">Logout</a>
  </div>
</nav>

<div class="container">
  <input type="hidden" id="hidden_ref_id" name="hidden_ref_id" value="<?php echo $auto_id; ?>">
  <div class="panel-group">

    <div class="row mb">

      <div class="col-md-8">
       <p style="color:red;">{{ Session::get('res') }}</p>
        <div class="panel panel-info">
          <div class="panel-heading">  DBBL Payment Method  </div>
          <div class="panel-body">
           <?php if($payment_status!="Paid"){ ?>     
            <ul class="instruction" style="font-size:16px;">
              

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
                     Other <b>0</b> (Zero) সিলেক্ট করু
                  </li>
                  
                  <li>
                    {{config('settings.college_name_bn')}} এর  Biller ID <b>{{config('settings.college_biller_id')}}</b> টাইপ করুন
                  </li> 
                     <li>
                    Bill No-এ আপনার  Admission Roll <strong><?php echo $admission_roll ?></strong> দিন
                  </li>
                  <li>
                    টাকার পরিমাণ <strong> <?php echo round($pending_amount) ;?> </strong> লিখুন 
                  </li>          
                  <li>
                    <b>PIN</b> টাইপ করুন
                  </li>
                  <li>
                    OK/Send বাটন চাপুন
                  </li>

                  <li>            
                        আপনি DBBL থেকে একটি <b>TrxID</b> পাবেন
                      </li>
                      <li>            
                        উপরের ফর্মে DBBL প্রাপ্ত <b>TrxID</b> দিয়ে <b>Submit</b> বাটনে ক্লিক করুন
                      </li>

                      <li>
                        পেমেন্ট সফলভাবে সম্পূর্ণ হলে আপনার কনফার্মেশন স্লিপ টি ডাউনলোড করুন|
                      </li>

                      <li>
                        তথ্য পাওয়ার প্রয়োজনে DBBL Help Line - <b>16216</b> নম্বরে কল করতে পারেন
                      </li>
                </ol>

            </ul> 
            
            <div class="row" style="display: ;">
              <div class="col-sm-8 col-sm-offset-1">
                  {!! Form::open(['url'=> 'Admission/Masters1st/payment/approve', 'method'=> 'post']) !!}
                      <div class="form-group">
                          {!! Form::text('transaction_id',null , ['class'=> 'form-control input-lg', 'placeholder'=> 'Enter Your Transaction ID', 'required'=> true]) !!}
                      </div>
                      {!! Form::submit('Submit', ['class'=> 'btn btn-primary btn-lg']) !!}
                  {!! Form::close() !!}
              </div>
          </div> 

            <?php } ?>

              <?php if($payment_status=="Paid"){ ?>
              <a id="confirm_slip" target="_blank">Download Confirmation Slip</a>
              <br/> <br/>
              <a id="download_form" target="_blank">Download Admission Form</a>
              <?php } ?>
          </div>
        </div>
      </div> 

      <div class="col-md-4" style="float: right;">
        <div class="panel panel-info">
          <div class="panel-heading">  </div>
          <div class="panel-body">
              <img  class="user_pic_view pull-center img-polaroid"  src="<?php echo url('/').'/upload/college/masters/draft/'. $photo;?>" alt="User Photo" /> 

          </div>
        </div>
      </div>      
    </div>  

    <div class="row mb">
      <div class="col-md-6">
        <div class="panel panel-info">
          <div class="panel-heading"> Admission Details </div>
          <div class="panel-body vcard">
            <ul>
          
              <li>
                  <span class="item-key">Admission Roll</span>
                  <div class="vcard-item"><?php echo $admission_roll;?></div>
              </li>

              <li>
                  <span class="item-key">Admitted Faculty</span>
                  <div class="vcard-item"><?php echo $to_faculty;?></div>
              </li>
              <li>
                  <span class="item-key">Admitted Subject</span>
                  <div class="vcard-item"><?php echo $to_subject;?></div>
              </li>
              <li>
                  <span class="item-key">Masters Session</span>
                  <div class="vcard-item"><?php echo $session;?></div>
              </li>           
            </ul>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="panel panel-info">
          <div class="panel-heading"> College Details </div>
          <div class="panel-body vcard">
            <ul>  
              <li>
                  <span class="item-key">Class Roll</span>
                  <div class="vcard-item"><?php echo $class_roll;?></div>
              </li>
               <li>
                  <span class="item-key">Student ID</span>
                  <div class="vcard-item"><?php echo $college_id;?></div>
              </li>
              <li>
                  <span class="item-key">Payment Status</span>
                  <div class="vcard-item"><?php echo $payment_status;?></div>
              </li>
                                                                     
            </ul>
          </div>
        </div>
      </div>      
    </div>

    <div class="row mb">
      <div class="col-md-6">
        <div class="panel panel-info">
          <div class="panel-heading"> Personal Information </div>
          <div class="panel-body vcard">
            <ul>
              <li>
                  <span class="item-key">Full name</span>
                  <div class="vcard-item"><?php echo $name;?></div>
              </li>
              <li>
                  <span class="item-key">Father's Name</span>
                  <div class="vcard-item"><?php echo $father_name;?></div>
              </li>
              <li>
                  <span class="item-key">Mother's Name</span>
                  <div class="vcard-item"><?php echo $mother_name;?></div>
              </li>

              <li>
                  <span class="item-key">Date of Birth</span>
                  <div class="vcard-item"><?php echo $birth_date;?></div>
              </li>


              <li>
                  <span class="item-key">Gender</span>
                  <div class="vcard-item"><?php echo $gender;?></div>
              </li>
              <li>
                  <span class="item-key">Religion</span>
                  <div class="vcard-item">
                      <?php echo $religion;?>
                  </div>
              </li>
              <li>
                  <span class="item-key">Blood Group</span>
                  <div class="vcard-item">
                    <?php if($blood=='') $blood=" - "; ?> 
                      <?php echo $blood;?>
                  </div>
              </li>
                                       
            </ul>
          </div>
        </div>
      </div>   

      <div class="col-md-6">
        <div class="panel panel-info">
          <div class="panel-heading"> Guardian Information </div>
          <div class="panel-body vcard">
            <ul>
              <li>
                  <span class="item-key">Name</span>
                  <div class="vcard-item"><?php echo $guardian_name;?></div>
              </li>
              <li>
                  <span class="item-key">Phone</span>
                  <div class="vcard-item"><?php echo $guardian_contact;?></div>
              </li>
              <li>
                  <span class="item-key">Relationship </span>
                  <div class="vcard-item"><?php echo $guardian_relation;?></div>
              </li>

               <li>
                  <span class="item-key">Occupation</span>
                  <div class="vcard-item"><?php echo $guardian_occupation;?>&nbsp;</div>
              </li>

               <li>
                  <span class="item-key">guardian's Income</span>
                  <div class="vcard-item"><?php echo $guardian_income;?></div>
              </li>
                                        
            </ul>
          </div>
        </div>
      </div> 

    </div>  


    <div class="row mb">
      <div class="col-md-6">
        <div class="panel panel-info">
          <div class="panel-heading"> Present Address </div>
          <div class="panel-body vcard">
            <ul>
              <li>
                  <span class="item-key">District</span>
                  <div class="vcard-item"><?php echo $present_dist;?></div>
              </li>
              <li>
                  <span class="item-key">Upozilla</span>
                  <div class="vcard-item">
                   <?php echo $present_ps;?>   
                  </div>
              </li>
              <li>
                  <span class="item-key">Post Office</span>
                  <div class="vcard-item"><?php echo $present_po;?></div>
              </li>
              <li>
                  <span class="item-key">Village</span>
                  <div class="vcard-item"><?php echo $present_village;?></div>
              </li>
             
              <li>
                  <span class="item-key">Contact No</span>
                  <div class="vcard-item"><?php echo $contact_no;?></div>
              </li>


                      
            </ul>
          </div>
        </div>
      </div>   

      <div class="col-md-6">
        <div class="panel panel-info">
          <div class="panel-heading"> Permanent Address </div>
          <div class="panel-body vcard">
            <ul>
              <li>
                  <span class="item-key">District</span>
                  <div class="vcard-item"><?php echo $permanent_dist;?></div>
              </li>
              <li>
                  <span class="item-key">Upozilla</span>
                  <div class="vcard-item">
                      <?php echo $permanent_ps;?>
                  </div>
              </li>
              <li>
                  <span class="item-key">Post Office</span>
                  <div class="vcard-item"><?php echo $permanent_po;?></div>
              </li>

                   <li>
                  <span class="item-key">Village</span>
                  <div class="vcard-item"><?php echo $permanent_village;?></div>
              </li>
              
              
             
              
              
              <li>
                  <span class="item-key">Contact No</span>
                  <div class="vcard-item">
                      <?php echo $permanent_mobile;?>
                  </div>
              </li>
                   
            </ul>
          </div>
        </div>
      </div> 

    </div>

  </div>
</div>





 <script src="<?php  echo url('/') ?>/js/jquery.min.js"></script>
<script src="<?php  echo url('/') ?>/js/bootstrap.min.js"></script>
   
<script src="<?php  echo url('/') ?>/js/drop_down.js"></script>
<script src="<?php  echo url('/') ?>/fjs/masters_1st_admission.js"></script>
<script src="{{ asset('vendors/iziToast/iziToast.min.js') }}"></script>
<script src="{{ asset('js/loadingoverlay.min.js') }}"></script>
@include('common.message')
   

<?php if($payment_status != 'Pending'){ ?>
<script type="text/javascript">
  $("#document").ready(function(){

      $("#download_form").click(function(){
      $("#confirm_slidownload_linkp_file").html("Please Wait.Processing..");
        $.ajax({
          type:'POST',
          url:'formId',
          data:{},
          headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
          beforeSend: function() {
              $.LoadingOverlay("show");
          },
          success:function(response){
            $.LoadingOverlay("hide");
            $("#download_link1").html(response);
            $("#download_form_file").modal('show');
          }
        });
    
    });

    $("#confirm_slip").click(function(){
      $("#confirm_slidownload_linkp_file").html("Please Wait.Processing..");
      $.ajax({
        type:'POST',
        url:'slipId',
        data:{},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        beforeSend: function() {
            $.LoadingOverlay("show");
        },
        success:function(response){
          $.LoadingOverlay("hide");
          $("#download_link").html(response);
          $("#confirm_slip_file").modal('show');
        }
      });
    });        

  });
</script>

<?php } ?>

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


 <div class="modal fade" id="download_tid_file" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div id="download_link2"><p>Please Wait....</p></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

 <div class="modal fade" id="confirm_slip_file" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div id="download_link"><p>Please Wait....</p></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

<?php
    Session::put('payment_amount',$pending_amount);
    Session::put('student_name',$name);
    Session::put('tracking_id',$tracking_id);
    Session::put('form_id',$tracking_id);
    Session::put('user_id',$tracking_id);

    Session::put(
      'slip_id',
      array(
        'name'=>$name,
        'fathers_name'=>$father_name,
        'mothers_name'=>$mother_name,
        'subject'=>$to_subject,
        'class_roll'=>$class_roll,
        'student_id'=>$college_id,
        'pending_amount'=>$pending_amount,
        'admission_session'=>$session,
        'admission_roll'=>$admission_roll,
      )
    );



?>
</body>


</html>

