<!DOCTYPE html>
<html>
<head>
  <title>Online Admission</title>
  <link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/css/bootstrap.min.css">
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/css/font-awesome.min.css">
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/css/bootstrap-datepicker3.min.css">
<link media="all" type="text/css" rel="stylesheet" href="<?php  echo url('/') ?>/css/style.css">
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/iziToast/iziToast.min.css') }}">

</head>

<body>
  
<?php

$tracking_id = Session::get('tracking_id');
$invoice_id = Session::get('invoice_id');

 $auto_id=deg_tracking_auto_id($tracking_id);
$admission_session = Session::get('admission_session');
$admission_roll = Session::get('admission_roll');

  $payment_status = '';
  $class_roll = '';
  $college_id = '';
  $admitted_students = DB::table('deg_admitted_student')->where('admission_roll', $admission_roll)->where('auto_id',$auto_id)->get();
 

  foreach($admitted_students as $result){
    $admission_roll = $result->admission_roll;
        $name = $result->name;
        $fathers_name = $result->father_name;
        $mothers_name = $result->mother_name;
        $faculty = $result->faculty;
        $subject = $result->subject;
        $deg_sub = $result->deg_subjects;
        $contact_info = $result->contact_no;
        $photo = $result->photo;
        $session = $result->session;
        $gender = $result->gender;
        $religion = $result->religion;
        $guardian_name = $result->guardian_name;
        $guardian_contact = $result->guardian_contact;
        $guardian_relation = $result->guardian_relation;
        $guardian_income = $result->guardian_income;
        $guardian_occupation = $result->guardian_occupation;

        $present_village = $result->present_village;
        $present_po = $result->present_po;
        $present_ps = $result->present_ps;
        $present_dist  = $result->present_dist ;
        $contact_no = $result->contact_no;
        $email = $result->email;
        //$division = $result->division;

        $permanent_village = $result->permanent_village;
        $permanent_po = $result->permanent_po;
        $permanent_ps = $result->permanent_ps;
        $permanent_dist  = $result->permanent_dist ;
        $permanent_email = $result->permanent_email;
        //$permanent_division = $result->permanent_division;
        $permanent_mobile = $result->permanent_mobile;

        $ssc_roll = $result->ssc_roll;
        $ssc_institute = $result->ssc_institute;
        $ssc_board = $result->ssc_board;
        $ssc_gpa = $result->ssc_gpa;

        $hsc_roll = $result->hsc_roll;
        $hsc_board = $result->hsc_board;
        $hsc_institute = $result->hsc_institute;
        $hsc_gpa = $result->hsc_gpa;
        $birth_date=$result->birth_date;
        $ssc_passing_year = $result->ssc_pass_year;
        $hsc_passing_year = $result->hsc_pass_year;
  }

$invoices = DB::table('invoices')->where('id', $invoice_id)->where('admission_session', $session)->where('roll', $admission_roll)->where('type', 'degree_admission')->orderBy('id', 'desc')->get();

  if(count($invoices) > 0){
    $invoice = $invoices->first();
    $pending_amount = $invoice->total_amount;
      $paymentDate = $invoice->txndate;
  }else{
    $pending_amount = 0;
    $paymentDate = 'N/A';
  }
  

    $results_ad_ses = DB::table('admission_config')->where('current_level', 'Degree 1st Year')->where('open', 1)->where('course', 'degree')->where('clossing_date', '>=', date('Y-m-d'))->where('type', 'admission')->get();

    if(count($results_ad_ses) < 1){
      echo '<h2 style="text-align:center;color:red;">Admission Not Open</h2>';
      exit();
    }

    foreach($results_ad_ses as $result){
            $current_admission_session = $result->session;            
        }


    $results = DB::select("SELECT class_roll,id FROM student_info_degree WHERE refference_id=$auto_id 
    and session='$current_admission_session' and current_level='Degree 1st Year'");
    $total_count = count($results);
    if($total_count>0){
        foreach($results as $result){
            $class_roll = $result->class_roll;
            $college_id  = $result->id;
            $payment_status = "Paid";
            $photo = url('/').'/upload/college/degree/'.$session.'/'.$photo;
        }
    }
    else{
        $class_roll = "<span style='color:red;'>Pending</span>";
        $payment_status = "<span style='color:red;'>Pending</span>";
        $college_id = "<span style='color:red;'>Pending</span>";
        $photo =  url('/').'/upload/college/degree/draft/'.$photo;
    }
?>
  <nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">{{config('settings.college_name')}} Degree Online Admission</a>
    </div>
    <a href="<?php echo url('/').'/Admission/Degree' ?>" class="btn btn-danger navbar-btn">Admission</a>
    <a href="<?php echo url('/').'/Admission/Degree/logout'?>" class="btn btn-danger navbar-btn">Logout</a>
  </div>
</nav>

<div class="container">
  
  <div class="panel-group clearfix">


       <div class="{{count($admitted_students) > 0 ? 'col-md-8' : 'col-md-12'}}">
       <p style="color:red;">{{ Session::get('res') }}</p>
       @if (Session::get('tracking_id') != '' && Session::get('password') !='')
        <p class="col-md-10 col-md-offset-1 alert alert-info text-center">
          <?php echo 'Your Ref Id :'.' <span style="color: red;">'.Session::get('tracking_id').'</span> '. 'And Password: <span style="color: red;">'.' '.Session::get('password').'</span> '.'Please remember your Ref Id and password for login. You need to login several time for admission purpose';?>
        </p>
       @endif
        <div class="panel {{$payment_status!="Paid" ? 'panel-info': 'panel-success text-center'}} col-md-10 col-md-offset-1">
        <div class="panel-heading"> {{$payment_status!="Paid"? 'DBBL Payment Method':'Please Download Your Confirmation Slip'}}</div>   
            <div class="panel-body">
              @if($payment_status!="Paid")
                  @php
                    $biller_id = config('settings.college_biller_id');
                    $college_name_bn = config('settings.college_name_bn');
                    $student_id = $admission_roll;
                    $payment_guideline = get_config('degree_adm_payment_guideline');

                    if($payment_guideline){
                      echo @configTempleteToBody($payment_guideline,['student_id'=> $admission_roll, 'college_name_bn'=> $college_name_bn, 'biller_id'=> $biller_id,'total_amount'=>round($total_amount)]);
                    }
                  @endphp
                  <div class="row text-center">
                      <button class="btn btn-primary text-center" onClick="window.location.reload();">Confirm To Download Confirmation Slip</button>
                  </div>
              @else
                  <a style="cursor: pointer;" id="confirm_slip" target="_blank">Download Confirmation Slip</a>
                  <br/> <br/>
                  <a style="cursor: pointer;" id="download_form" target="_blank">Download Admission Form</a>
              @endif
            </div>
        </div>
        @if (count($admitted_students) > 0)
          <div class="col-md-4" style="float: right;">
          <div class="panel panel-info">
            <div class="panel-heading">  </div>
            <div class="panel-body">
                <img  class="user_pic_view pull-center img-polaroid"  src="{{$photo}}" alt="User Photo" />
            </div>
          </div>
        </div>
        @endif

       </div>
       @if (count($admitted_students) > 0)
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
                  <div class="vcard-item"><?php echo $faculty;?></div>
                </li> 

                <li>
                  <span class="item-key">Selected Subjects</span>
                  <div class="vcard-item"><?php echo $deg_sub;?></div>
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
                      <span class="item-key">Payment Status</span>
                      <div class="vcard-item">{!!$payment_status == 'Pending' ? '<span class="text-danger">'.$payment_status.'</span>':'<span class="text-success">'.$payment_status.'</span>'!!}</div>
                  </li>

                  <li>
                      <span class="item-key">Student ID</span>
                      <div class="vcard-item">{!! $college_id !!}</div>
                  </li>

                  <li>
                      <span class="item-key">Class Roll</span>
                      <div class="vcard-item">{!! $class_roll !!}</div>
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
                      <div class="vcard-item"><?php echo $fathers_name;?></div>
                  </li>
                  <li>
                      <span class="item-key">Mother's Name</span>
                      <div class="vcard-item"><?php echo $mothers_name;?></div>
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
                      <span class="item-key">Yearly Income</span>
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


        <div class="row mb">
          <div class="col-md-12">
            <div class="panel panel-info">
              <div class="panel-heading"> Academic Qualification </div>
              <div class="panel-body vcard">
                <ul>
                    <li><table class="table table-bordered table-hover">
                      <tr class='warning'>
                          <td>Exam Type</td>
                          <td>Institution</td>
                          <td>Board</td>
                          <td>Roll</td>
                          <td>Passing Year</td>
                          <td>GPA</td>
                      </tr>
                      <tr>
                          <td>SSC</td>
                          <td><?php echo $ssc_institute;?></td>
                          <td><?php echo $ssc_board;?></td>
                          
                          <td><?php echo $ssc_roll;?></td>
                          <td><?php echo $ssc_passing_year;?></td>
                          <td><?php echo $ssc_gpa;?></td>
                      </tr>
                      <tr >
                          <td>HSC</td>
                          <td><?php echo $hsc_institute;?></td>
                          <td><?php echo $hsc_board;?></td>

                          <td><?php echo $hsc_roll;?></td>
                          <td><?php echo $hsc_passing_year;?></td>
                          <td><?php echo $hsc_gpa;?></td>
                      </tr>

                    </table>    
                  </li>                                                            
               </ul>
                
              </div>
            </div>
          </div>    
        </div>
       @endif
    </div>

 <script src="<?php  echo url('/') ?>/js/jquery.min.js"></script>
<script src="<?php  echo url('/') ?>/js/bootstrap.min.js"></script>
   
<script src="<?php  echo url('/') ?>/js/drop_down.js"></script>
<script src="<?php  echo url('/') ?>/fjs/degree_admission.js"></script>
<script src="{{ asset('vendors/iziToast/iziToast.min.js') }}"></script>
<script src="{{ asset('js/loadingoverlay.min.js') }}"></script>
   

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
        'fathers_name'=>$fathers_name,
        'mothers_name'=>$mothers_name,
        'subject'=>$subject,
        'class_roll'=>$class_roll,
        'student_id'=>$college_id,
        'pending_amount'=>$pending_amount,
        'admission_session'=>$session,
        'admission_roll'=>$admission_roll,
      )
    );



?>
@include('common.message')
</body>


</html>

