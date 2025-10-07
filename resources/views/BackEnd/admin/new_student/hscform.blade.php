@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Add New Hsc Student')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<?php if(Session::get('stuId')!='' && Session::get('stuRoll')!=''){?>
<div class="alert alert-success">
Student Added successfully!  <span style="color:red; font-weight:bold;"><?php echo 'Student Id :'.' '.session::get('stuId').' ';?></span>And <span style="color:red; font-weight:bold;"><?php echo 'Class Roll :'.' '.session::get('stuRoll').' ';?></span>Please remember your Student Id and  Class Roll for login.
</div>
 <?php } ?>

{!! Form::open(['route'=> 'admin.newstudent.hscsubmit', 'method' => 'post', 'files'=> true]) !!}
<div class="row mb">	
	<div class="col-md-6">
		<div class="card border border-primary">
          <div class="card-header bg-primary">
            Persional Information
          </div>
          <div class="card-block">
				<div class="form-group row">
					<label for="registration_id" class="col-sm-4 col-form-label">Registration No:</label>
					<div class="col-sm-8">
						{!! Form::text('registration_id', null, ['class'=> 'form-control', 'id' => 'registration_id', 'placeholder' => 'Enter Registration No']) !!}
						{!!invalid_feedback('registration_id')!!}
					</div>
				</div>
				<div class="form-group row">
					<label for="name" class="col-sm-4 col-form-label">Name</label>
					<div class="col-sm-8">
						{!! Form::text('student_name', null, ['class'=> 'form-control', 'id' => 'name', 'required'=> true,'placeholder' => 'Enter Student Name']) !!}
						{!!invalid_feedback('student_name')!!}
					</div>
				</div>
				<div class="form-group row">
					<label for="session" class="col-sm-4 col-form-label">Session</label>
					<div class="col-sm-8">

						{!! Form::select('session', selective_multiple_session(), null, ['class'=>'form-control show-tick', 'required'=> true]) !!}
						{!!invalid_feedback('session')!!}
					</div>
				</div>
				<div class="form-group row">
					<label for="photo" class="col-sm-4 col-form-label">Your picture</label>
					<div class="col-sm-8">
						{!! Form::file('photo', ['class'=> 'form-control', 'id' => 'photo']) !!}
						{!!invalid_feedback('photo')!!}
					</div>
				</div>

				<div class="form-group row">
					<label for="father_name" class="col-sm-4 col-form-label">Father's Name</label>
					<div class="col-sm-8">
						{!! Form::text('father_name', null, ['class'=> 'form-control', 'id' => 'father_name', 'required'=> true, 'placeholder' => "Father's Name"]) !!}
						{!!invalid_feedback('father_name')!!}
					</div>

				</div>

				<div class="form-group row">
					<label for="mother_name" class="col-sm-4 col-form-label">Mother's Name</label>
					<div class="col-sm-8">
						{!! Form::text('mother_name', null, ['class'=> 'form-control', 'id' => 'mother_name', 'required'=> true, 'placeholder' => "Mother's Name"]) !!}
						{!!invalid_feedback('mother_name')!!}
					</div>
				</div>
				
				<div class="form-group row">
					<label for="birth_date" class="col-sm-4 col-form-label">Date Of Birth</label>
					<div class="col-sm-8">
						{!! Form::text('birth_date', null, ['class'=> 'form-control', 'id' => 'birth_date', 'required'=> true, 'placeholder' => "Date Of Birth"]) !!}
						{!!invalid_feedback('birth_date')!!}
					</div>
				</div>

				<div class="form-group row">
					<label for="blood_group" class="col-sm-4 col-form-label">Blood Group</label>
					<div class="col-sm-8">

						{!! Form::select('blood_group', selective_blood_lists(), null, ['class' => 'form-control', 'id'=> 'blood_group']) !!}
						{!!invalid_feedback('blood_group')!!}
					</div>
				</div>

				<div class="form-group row">
					<label for="gender" class="col-sm-4 col-form-label">Gender</label>
					<div class="col-sm-8">
						{!! Form::select('gender', selective_gender_list(), null, ['class' => 'form-control', 'id'=> 'gender', 'required'=> true]) !!}
						{!!invalid_feedback('gender')!!}
					</div>
				</div>

				<div class="form-group row">
					<label for="religion" class="col-sm-4 col-form-label">Religion</label>
					<div class="col-sm-8">
						{!! Form::select('religion', selective_religion_list(), null, ['class' => 'form-control', 'id'=> 'religion', 'required'=> true]) !!}
						{!!invalid_feedback('religion')!!}
					</div>
				</div>
				<div class="form-group row">
					<label for="guardian_mobile" class="col-sm-4 col-form-label">Mobile</label>
					<div class="col-sm-8">
						{!! Form::text('mobile', null, ['class'=> 'form-control', 'id' => 'mobile', 'required'=> true, 'placeholder' => "Mobile"]) !!}
						{!!invalid_feedback('mobile')!!}
					</div>
				</div>						
			
          </div>
        </div>
	</div>
	<div class="col-md-6">
		<div class="card border border-primary">
		  <div class="card-header bg-primary">
			SSC Information
		  </div>
		  <div class="card-block">
				<div class="form-group row">
					<label for="ssc_registration" class="col-sm-4 col-form-label">SSC Registration No:</label>
					<div class="col-sm-8">
						{!! Form::text('ssc_registration', null, ['class'=> 'form-control', 'id' => 'ssc_registration', 'required'=> true, 'placeholder' => "Enter SSC Registration"]) !!}
						{!!invalid_feedback('ssc_registration')!!}
					</div>
				</div>
				<div class="form-group row">
					<label for="ssc_roll" class="col-sm-4 col-form-label">SSC Roll :</label>
					<div class="col-sm-8">
						{!! Form::text('ssc_roll', null, ['class'=> 'form-control', 'id' => 'ssc_roll', 'required'=> true, 'placeholder' => "Enter SSC Roll"]) !!}
						{!!invalid_feedback('ssc_roll')!!}
					</div>
				</div>						  
				<div class="form-group row">
					<label for="ssc_gpa" class="col-sm-4 col-form-label">SSC GPA :</label>
					<div class="col-sm-8">
						{!! Form::text('ssc_gpa', null, ['class'=> 'form-control', 'id' => 'ssc_gpa', 'required'=> true, 'placeholder' => "Enter SSC GPA"]) !!}
						{!!invalid_feedback('ssc_gpa')!!}
					</div>
				</div>						  
				<div class="form-group row">
					<label for="ssc_institute" class="col-sm-4 col-form-label">SSC Institute :</label>
					<div class="col-sm-8">
						{!! Form::text('ssc_institute', null, ['class'=> 'form-control', 'id' => 'ssc_institute', 'placeholder' => "Enter SSC Institute"]) !!}
						{!!invalid_feedback('ssc_institute')!!}
					</div>
				</div>
				<div class="form-group row">
					<label for="ssc_group" class="col-sm-4 col-form-label">SSC Group :</label>
					<div class="col-sm-8">
						{!! Form::select('ssc_group', selective_multiple_study_group(), null, ['class' => 'form-control', 'id'=> 'ssc_group']) !!}
						{!!invalid_feedback('ssc_group')!!}
					</div>
				</div>

				<div class="form-group row">
					<label for="ssc_session" class="col-sm-4 col-form-label">SSC Session :</label>
					<div class="col-sm-8">
						{!! Form::select('ssc_session', selective_multiple_session(), null, ['class' => 'form-control', 'id'=> 'ssc_session']) !!}
						{!!invalid_feedback('ssc_session')!!}
					</div>
				</div>
		  </div>
		</div>
	</div>

	<div class="col-md-10">
		<div class="card border border-primary">
			<div class="card-header bg-primary">
				Admission Information
			</div>
			<div class="card-block">
				<div class="form-group row">
					<label for="hsc_group" required="required" class="col-sm-4 control-label">HSC Group :</label>
					<div class="col-sm-8">
						<?php 
                  //$admission_group =  Session::get('hsc_group');
                  //$admission_group = trim($admission_group);

						?>
						{!! Form::select('hsc_group', selective_multiple_study_group(), null, ['class' => 'form-control', 'id'=> 'hsc_group', 'required'=> true]) !!}
						{!!invalid_feedback('hsc_group')!!}
					</div>
				</div>
				<div class="form-group row">
					<label for="current_level" class="col-sm-4 control-label">Current Level :</label>
					<div class="col-sm-8">
						{!! Form::select('current_level', selective_multiple_hsc_level(), null, ['class' => 'form-control', 'id'=> 'current_level', 'required'=> true]) !!}
						{!!invalid_feedback('current_level')!!}
					</div>
				</div>						  
				<div class="form-group row">
					<label for="compulsory_course_codes" class="col-sm-4 control-label">Compulsory Course Info :</label>
					<div class="col-sm-8">
						<div class="compulsory_course_codes">   
							<table  class="table table-bordered table-striped">
								<!-- <caption >Compulsory Courses &amp; Codes</caption> -->
								<thead >
									<tr style="background:#fffddd">
										<th>Course Code</th>
										<th >Course Name</th>
									</tr>
								</thead>
								<tbody >
									<tr>
										<td >ex: 101-102</td>
										<td >ex: Bangla</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="help-block"></div>
					</div>
				</div>

				<div class="form-group row">
					<label for="selective_course_codes" class="col-sm-4 control-label">Selective Course Info :</label>
					<div class="col-sm-8">
						<div class="selective_course_codes"> 
							<table class="table table-bordered table-striped">
								<!-- <caption>Selective Courses &amp; Codes</caption> -->
								<thead >
									<tr style="background:#fffddd">
										<th >Course Code</th>
										<th >Course Name</th>
									</tr>
								</thead>
								<tbody >
									<tr>
										<td >ex: 174-175</td>
										<td >ex: Physics</td>
									</tr>
								</tbody>
							</table>
						</div>  
						<div class="help-block"></div>
					</div>
				</div>

				<div class="form-group row">
					<label for="optional_course_codes" class="col-sm-4 control-label">Optional Course Info :</label>
					<div class="col-sm-8">
						<div class="optional_course_codes">
							<table  class="table table-bordered table-striped">
								<!-- <caption>Optional Courses &amp; Codes</caption> -->
								<thead >
									<tr style="background:#fffddd">
										<th >Course Code</th>
										<th >Course Name</th>
									</tr>
								</thead>
								<tbody >
									<tr>
										<td >ex: 178-179</td>
										<td >ex: Biology</td>
									</tr>
								</tbody>
							</table>
						</div> 
						<div class="help-block"></div>
					</div>
				</div>

				<button style="float: right;" type="submit" class="btn btn-danger navbar-btn" >Submit</button>
			</div>
		</div>
	</div>
</div>
{!! Form::close() !!}
@endsection

@push('scripts')
<script src="{{ asset('fjs/hsc_admission.js') }}"></script>
<script type="text/javascript">



	$('form').on('submit', function() {
		var compulsory_course_codes = '';
		var selective_course_codes = '';
		var optional_course_codes = '';

		var val = $("#hsc_group").val();
		var prev = '';

		if ( (val == 'Science') || (val == 'Business Studies') ) 
		{

			var selective_course_code = $(".selective_course_codes select").val();

			if (selective_course_code == '') {
				alert("Selective Courses must be selected properly");
				return false;
			}


			var optional_course_code = $(".optional_course_codes select").val();
			if (optional_course_code == '') {
				alert("optional Course must be selected properly");
				return false;
			}

			$(".selective_course_codes select > option").each(function() {
				if (this.value != '') {
					var s = $('#selective_course_codes').val();
					s = s.replace("," + this.value, "");
					$('#selective_course_codes').val(s);
				}
			});

			if (!($('#selective_course_codes').val().indexOf(selective_course_code) > (-1))) {
				$('#selective_course_codes').val($('#selective_course_codes').val() + "," + selective_course_code);
			}

			compulsory_course_codes = $('#compulsory_course_codes').val();
			selective_course_codes = $('#selective_course_codes').val();
			optional_course_codes = $('#optional_course_codes').val();
        //alert(selective_course_codes);
    } 

    else if (val == 'Humanities')
    {
    	var count = $('.selective_course_codes input[type=checkbox]:checked').length;
    	if (count < 3) {
    		alert("At-least 3 of the selective subjects must be chosen");
    		return false;
    	} 
    	else 
    	{
    		var selective_course_codes = "";
    		var a = [];
    		$('.selective_course_codes input[type=checkbox]:checked').each(function() {
    			var id = $(this).attr('id').replace(/check/, '');
    			var option = $("#text"+id).val();
    			a.push(option);
    		});
    		selective_course_codes = a.join(',');
    		$('#selective_course_codes').val(selective_course_codes);
    	}

    	var optional_course_code = $(".optional_course_codes select").val();
    	if (optional_course_code == '') 
    	{
    		alert("Optional Course must be selected");
    		return;
    	}

    	compulsory_course_codes = $('#compulsory_course_codes').val();
    	selective_course_codes = $('#selective_course_codes').val();
    	optional_course_codes = $('#optional_course_codes').val();
    }
    var mobile = $('#student_mobile').val();
    var mobile_number_confirmation = $('#student_mobile_re').val();
    var password = $('#inputPassword').val();
    var password_confirmation = $('#inputPasswordConfirm').val();

    if(mobile!=mobile_number_confirmation){
    	$('.mobile_number_confirmation_error').html('<span style="color:red;">Mobile Number  Mismatch</span>');
    	return false;
    } else {
    	$('.mobile_number_confirmation_error').html('');
    }

    if(password!=password_confirmation){
    	$('.confirm_password_error').html('<span style="color:red;">Password  Mismatch</span>');
    	return false;
    } else {
    	$('.confirm_password_error').html('');
    }


    $(':disabled').each(function(e) {
    	$(this).removeAttr('disabled');
    })
});


	$('#birth_date').datepicker({
		format: 'yyyy-mm-dd',
		endDate: new Date() 

	});
	var same_address=0;
	$('#same').click(function(){
		if($(this).is(':checked')){
      	//alert($('#present_po').val());
      	$('#permanent_village').attr('readonly','readonly');
      	$('#permanent_village').val($('#present_village').val()).change();
      	$('#permanent_post_office').attr('readonly','readonly');
      	$('#permanent_post_office').val($('#present_po').val()).change();
      	$('#permanent_district').attr('disabled','disabled');
      	$('#permanent_district').val($('#present_dist').val()).change();
      	$('#permanent_thana').val($('#present_thana').val()).change();
      	$('#permanent_thana').attr('disabled','disabled');
      	$('#permanent_mobile_no').attr('readonly','readonly');
      	$('#permanent_mobile_no').val($('#student_mobile').val()).change();

      	same_address = 1;
      }
      else{
      	$('#permanent_village').removeAttr('readonly');
      	$('#permanent_post_office').removeAttr('readonly');
      	$('#permanent_district').removeAttr('disabled');
      	$('#permanent_thana').removeAttr('disabled');
      	$('#permanent_mobile_no').removeAttr('readonly');
      	same_address = 0;
      }
  });	



	$("#hsc_group").change(function() {
		var val = $("#hsc_group").val();
		if ( (val == 'Science') || (val == 'Business Studies') ) {
			$(".selective_course_codes select").off();
			$(document).on('change','.selective_course_codes select',function(){
				var option = $(this).val();
				var id = $(this).attr('id').replace(/select/, '');
          //alert(id);
          $("input[type=text]#text"+id).attr('value', option);
          if (option == '') {
          	option = "Select";
          	$(".optional_course_codes select").val('');
          	$(".optional_course_codes select option").removeAttr('disabled');
          	$(".optional_course_codes select").attr('disabled','disabled');
          } else {
          	$(".optional_course_codes select").val('');
          	$(".optional_course_codes select option").removeAttr('disabled');
          	$(".optional_course_codes select").removeAttr('disabled');
          	$(".optional_course_codes select option[value='" + option + "']").attr('disabled','true');
          }
          $("input[type=text]#text"+id).val(option);
      });
			$(document).on('change','.optional_course_codes #selecting',function(){
        	//alert('arts');
        	var option = $(this).val();
        	$("#optional_course_codes").attr('value', option);
        	if (option == '') option = "Select";
        	$("#texting").val(option);
        });
		} else if (val == 'Humanities') {
        //alert('arts');
        $(".selective_course_codes select").off();
        $(document).on('change','.selective_course_codes select',function(){       	
        	var option = $(this).val();
        	if (option == '') option = "Select";
        	var id = $(this).attr('id').replace(/select/, '');
        	$("input[type=text]#text"+id).val(option).change();
        	$("input[type='checkbox']#text"+id).val(option).change();
        });
        $(document).on('change','.optional_course_codes #selecting',function(){
        	var option = $(this).val();
        	$("#optional_course_codes").attr('value', option);
        	if (option == '') option = "Select";
        	$("#texting").val(option);
        });
        $(document).on('change','.selective_course_codes input[type=checkbox]',function(){ 
        	var count = $('.selective_course_codes input[type=checkbox]:checked').length;
        	if (count == 4) {
        		alert("You can only select 3 of these selective subjects");
        		$(this).prop('checked',false);
        		return;
        	}
        	if (count == 3) $(".optional_course_codes #selecting").removeAttr('disabled');
        	else {
        		$(".optional_course_codes #selecting").prop("selectedIndex", 0);
        		$(".optional_course_codes #selecting").attr('disabled','disabled');

        	}
        	var id = $(this).attr('id').replace(/check/, '');
        	var option = $(this).val();

        	if (option == "Select") {
        		alert("Please select a subject 1st, and then; tick the related checkbox");
        		$(this).prop('checked',false);
        	} else {
        		if ($(this).prop('checked') == true) {

        			var intId = id.match(/\d+/);

        			$("#select"+intId).attr('disabled','true');
        			$("#select"+intId+" > option").each(function() {
        				$(".optional_course_codes select option[value='" + this.value + "']").attr('disabled','true');
        			});

        			$(".optional_course_codes select option[value='" + option + "']").attr('disabled','true');
        		} else {
        			var intId = id.match(/\d+/);
        			$("#select"+intId).removeAttr('disabled');
        			$("#select"+intId+" > option").each(function() {

        				$(".optional_course_codes select option[value='" + this.value + "']").removeAttr('disabled');
        			});

        			$(".optional_course_codes select option[value='" + option + "']").removeAttr('disabled');
        		}
        	}
        });
    }
});
</script>
@endpush