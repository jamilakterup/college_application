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

$tracking_id=Session::get('tracking_id');
  $auto_id = hsc_tracking_auto_id($tracking_id);
  $payment_status = '';
  $class_roll = '';
  $college_id = '';
  $results = DB::table('hsc_admitted_students')->where('auto_id',$auto_id)->get();
  foreach($results as $result){
    $entry_time = $result->entry_time;
    $photo = $result->photo;
    $name = $result->name;
    $bangla_name = $result->bangla_name;
    $PIN_number = $result->PIN_number;
    $fathers_name = $result->fathers_name;
    $mothers_name = $result->mothers_name;
    $date_of_birth = $result->date_of_birth;
    $religion = $result->religion;
    $password = $result->password;
    $sex = $result->sex;
    $guardian_name = $result->guardian_name;
    $guardian_phone = $result->guardian_phone;
    $relation = $result->relation;
    $village = $result->village;
    $post_office = $result->post_office;
    $district = $result->district;
    $upozilla = $result->upozilla;
    $mobile = $result->mobile;
    $permanent_village = $result->permanent_village;
    $permanent_post_office = $result->permanent_post_office;
    $permanent_thana = $result->permanent_thana;
    $permanent_district = $result->permanent_district;
    $ssc_institution = $result->ssc_institution;
    $ssc_group = $result->ssc_group;    
    $hsc_group = $result->hsc_group;  
    $ssc_roll = $result->ssc_roll;
    $ssc_passing_year = $result->ssc_passing_year;
    $ssc_gpa = $result->ssc_gpa;
    $ssc_board = $result->ssc_board;
    $permanent_mobile = $result->permanent_mobile;
    $income = $result->income;
    $occupation = $result->occupation;
    $admission_session = $result->admission_session;
    $payment_status = $result->payment_status;
    $invoice_id = $result->invoice_id;
  }
  

  $invoices = DB::table('invoices')->where('id', $invoice_id)->where('admission_session', $admission_session)->where('roll', $ssc_roll)->where('type', 'hsc_admission')->get();

    if(count($invoices) > 0){
      $invoice = $invoices->first();
      $toatalDDBLPayAmount = $invoice->total_amount;
      $paymentDate = $invoice->txndate;
    }else{
      $toatalDDBLPayAmount = 0;
      $paymentDate = 'N/A';
    }


  
  
  
  $results = DB::table('student_info_hsc')->where('refference_id',$auto_id)->where('session',$admission_session)->where('current_level','HSC 1st Year')->get();

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
      <a class="navbar-brand" href="#">{{config('settings.college_name')}} Online Admission</a>
    </div>
    <a href="<?php echo url('/').'/Admission/HSC' ?>" class="btn btn-danger navbar-btn">Admission</a>
    <a href="<?php echo url('/').'/Admission/HSC/logout'?>" class="btn btn-danger navbar-btn">Logout</a>
  </div>
</nav>

<div class="container">
  <input type="hidden" id="hidden_ref_id" name="hidden_ref_id" value="<?php echo $auto_id; ?>">
  <div class="panel-group">

    <div class="row mb">

   
      <div class="col-md-8">
       <p style="color:red;">{{ Session::get('res') }}</p>
        <div class="panel {{$payment_status!="Paid" ? 'panel-info': 'panel-success text-center'}} col-md-10 col-md-offset-1">
			<div class="panel-heading"> {{$payment_status!="Paid"? 'DBBL Payment Method':'Please Download Your Confirmation Slip'}}</div>		
          <div class="panel-body">       
           @if($payment_status!="Paid")

              @php
                  $biller_id = config('settings.college_biller_id');
                  $college_name_bn = config('settings.college_name_bn');
                  $student_id = $ssc_roll;
                  $payment_guideline = get_config('hsc_adm_payment_guideline');

                  if($payment_guideline){
                    echo @configTempleteToBody($payment_guideline,['student_id'=> $ssc_roll, 'college_name_bn'=> $college_name_bn, 'biller_id'=> $biller_id,'total_amount'=>$toatalDDBLPayAmount]);
                  }
              @endphp
                <div class="row text-center">
                    <button class="btn btn-primary text-center" onClick="window.location.reload();">Confirm To Download Confirmation Slip</button>
                </div>
              @endif
              @if($payment_status=="Paid")
                <a style="cursor: pointer;" id="hscconfirm_slip" target="_blank">Download Confirmation Slip</a>
                <br/> <br/>

                <a style="cursor: pointer;" id="download_form" target="_blank">Download Admission Form</a>
                <br/><br/>

                <a style="cursor: pointer;" id="hsc_commitment_slip" target="_blank">Download Your Commitment ( অঙ্গীকারনামা )</a>
                <br/> <br/>

             @endif
          </div>
        </div>

      </div>

      <div class="col-md-4">
        <div class="panel panel-info">
          <div class="panel-heading">  </div>
          <div class="panel-body">
            @php
            if(\File::exists(public_path('upload/college/hsc/draft/'.$photo))){
                $photo_url = url('upload/college/hsc/draft/'.$photo);
            }else{
                $photo_url = url('upload/college/hsc/'.$admission_session.'/'.$photo);
            }
            @endphp
              <img  class="user_pic_view pull-center img-polaroid"  src="{{$photo_url}}" alt="User Photo" /> 

          </div>
        </div>
      </div>

    </div>

    <div class="row mb">
      <div class="col-md-12">
        <a href="{{route('student.hsc.admission.editForm')}}" class="btn btn-primary">Edit Form</a>
      </div>
    </div>

    <div class="row mb">
      <div class="col-md-6">
        <div class="panel panel-info">
          <div class="panel-heading"> Admission Details </div>
          <div class="panel-body vcard">
            <ul>
            <li>
              <span class="item-key">Admission Session</span>
              <div class="vcard-item"><?php echo $admission_session;?></div>
            </li>
            <li>
              <span class="item-key">Group</span>
              <?php 
              $admission_group=$hsc_group;
              $admission_group_display=$admission_group;

               ?>
              <div class="vcard-item"><?php echo ucwords($admission_group_display);?></div>
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
              <span class="item-key">Name</span>
              <div class="vcard-item"><?php echo $name;?></div>
            </li>
            <li>
              <span class="item-key">Name (In Bangla)</span>
                <div class="vcard-item"><?php echo $bangla_name;?></div>
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
                <div class="vcard-item"><?php echo $date_of_birth;?></div>
            </li> 
            <li>
              <span class="item-key">Gender</span>
                <div class="vcard-item"><?php echo $sex;?></div>
            </li> 
            <li>
              <span class="item-key">Religion</span>
                <div class="vcard-item"><?php echo $religion;?></div>
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
                <div class="vcard-item"><?php echo $guardian_phone;?></div>
            </li>
            <li>
              <span class="item-key">Relationship</span>
                <div class="vcard-item"><?php echo $relation;?></div>
            </li>  
            <li>
              <span class="item-key">Occupation</span>
                <div class="vcard-item"><?php echo $occupation;?></div>
            </li> 
            <li>
              <span class="item-key">Yearly Income</span>
                <div class="vcard-item"><?php echo $income;?></div>
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
              <div class="vcard-item"><?php echo $district;?></div>
            </li>
            <li>
              <span class="item-key">Upozilla</span>
                <div class="vcard-item"><?php echo $upozilla;?></div>
            </li>
            <li>
              <span class="item-key">Post Office</span>
                <div class="vcard-item"><?php echo $post_office;?></div>
            </li>  
            <li>
              <span class="item-key">Village</span>
                <div class="vcard-item"><?php echo $village;?></div>
            </li> 
            <li>
              <span class="item-key">Contact No</span>
                <div class="vcard-item"><?php echo $mobile;?></div>
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
              <div class="vcard-item"><?php echo $permanent_district;?></div>
            </li>
            <li>
              <span class="item-key">Thana</span>
                <div class="vcard-item"><?php echo $permanent_thana;?></div>
            </li>
            <li>
              <span class="item-key">Post Office</span>
                <div class="vcard-item"><?php echo $permanent_post_office;?></div>
            </li>  
            <li>
              <span class="item-key">Village</span>
                <div class="vcard-item"><?php echo $permanent_village;?></div>
            </li> 
            <li>
              <span class="item-key">Contact No</span>
                <div class="vcard-item"><?php echo $permanent_mobile;?></div>
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
            <li>
              <table class="table table-bordered table-hover">
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
                  <td><?php echo $ssc_institution;?></td>
                  <td><?php echo $ssc_board;?></td>
                  <td><?php echo $ssc_roll;?></td>
                  <td><?php echo $ssc_passing_year;?></td>
                  <td><?php echo $ssc_gpa;?></td>
                </tr>
              </table>    
            </li>
          </ul>
          </div>
        </div>
      </div>    
    </div>


    <div class="row mb">
      <div class="col-md-12">
        <div class="panel panel-info">
          <div class="panel-heading"> HSC Subjects Info </div>
          <div class="panel-body vcard">
            <ul>
            <li id="subject_info">
              <table class="table table-bordered table-hover">
                <tr class='warning'>
                  <td>Subject Type</td>
                  <td>Subjects</td>
                </tr>
                <tr>
                  <td>Compulsory</td>
                  <td>A,B,C</td>
                </tr>
                <tr>
                  <td>Selective</td>
                  <td>X,Y,Z</td>
                </tr>
                <tr>
                  <td>Optional</td>
                  <td>P</td>
                </tr>
              </table>      
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
<script src="<?php  echo url('/') ?>/fjs/hsc_admission.js"></script>
<script src="{{ asset('vendors/iziToast/iziToast.min.js') }}"></script>
<script src="{{ asset('js/loadingoverlay.min.js') }}"></script>


   
<script type="text/javascript">
$("#document").ready(function(){
  /*Start of subject information loading*/
  $('#myModal').modal('show');
  var id=$("#hidden_ref_id").val();
  //ajax start
  $.ajax({
    type:'POST',
    url:'SubjectCodeSequence',
    data:{id:id,status:1},
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    success:function(response){
      $('#subject_info').html(response);
    }
  });//end of ajax
  $('#myModal').modal('hide');
  /*End of subject information loading*/
});
</script>   

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
            $("#download_form_file").modal('show');
            $("#download_link1").html(response);
          }
        });
    
    });


        $("#download_tid").click(function(){
      $("#download_tid_file").modal('show');
      $("#confirm_slidownload_linkp_file").html("Please Wait.Processing..");
        $.ajax({
          type:'POST',
          url:'tidId',
          data:{},
          headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
          beforeSend: function() {
              $.LoadingOverlay("show");
          },
          success:function(response){
            $.LoadingOverlay("hide");
            $("#download_link2").html(response);
          }
        });
    
    });


    $("#hscconfirm_slip").click(function(){
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
          $("#confirm_slip_file").modal('show');
          $("#download_link").html(response);
        }
      });
    });  

    $("#hsc_commitment_slip").click(function(){
      $("#confirm_slidownload_linkp_file").html("Please Wait.Processing..");
      $.ajax({
        type:'POST',
        url:'slipCommitment',
        data:{},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        beforeSend: function() {
          $.LoadingOverlay("show");
        },
        success:function(response){
          $.LoadingOverlay("hide");
          $("#confirm_slip_file").modal('show');
          $("#download_link").html(response);
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
  Session::put('student_name',$name);
  Session::put('tracking_id',$tracking_id);
  Session::put('form_id',$auto_id);
  Session::put('user_id',$auto_id);



$pending_amount='';
 $results = DB::table('payment_info')->where('refference_id', $auto_id)->get();
    $found_total = count($results);

    if($found_total>0){
      foreach($results as $result){
        $pending_amount = $result->total_amount;
		$paymentDate = $result->update_date;
      }
    }
    else{
      $pending_amount =0;
    } 


    Session::put(
      'slip_id',
      array(
        'name'=>$name,
        'fathers_name'=>$fathers_name,
        'mothers_name'=>$mothers_name,
        'hsc_group'=>$hsc_group,
        'class_roll'=>$class_roll,
        'student_id'=>$college_id,
        'pending_amount'=>$pending_amount
      )
    );

?>
@include('common.message')
</body>


</html>

