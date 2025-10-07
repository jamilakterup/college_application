<!DOCTYPE html>
<html>
<head>
	<title>Online Masters Part-1 Admission Form</title>
	<meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="Codeply">
    <link rel="shortcut icon" href="{{asset('upload/sites/'.config('settings.site_favicon'))}}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/styles.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fcss/animate.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/iziToast/iziToast.min.css') }}">
    <style type="text/css">
    	.row{
    		margin-bottom: 5px;
    	}
    </style>
</head>

<body>
	
  

  <nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">{{config('settings.college_name')}} {{config('settings.college_name') !='' ? ', '.config('settings.college_district'):''}}</a>
    </div>
    <a href="<?php echo url('/').'/Admission/Masters1st' ?>" class="btn btn-danger navbar-btn">Admission</a>
    <a href="<?php echo url('/').'/Admission/Masters1st/signin'?>" class="btn btn-danger navbar-btn">Login</a>
  </div>
</nav>

<div class="container">
  
  	<div class="panel-group">

  	<div class="row mb">
  		<div class="col-md-12">
		    <div class="panel panel-info">
		      <div class="panel-heading">গুরত্বপূর্ণ নির্দেশাবলীঃ</div>
		      <div class="panel-body">ভবিষ্যতে লগইনের জন্য নিচে আপনার পছন্দমত পাসওয়ার্ড দিন। পাসওয়ার্ডটি সংরক্ষন করুন, কারন ভর্তি পরবর্তী সময়ে এই সাইটে আপনার আবার লগইন করার প্রয়োজন হবে। </div>
		    </div>
	    </div>
    </div>
<?php   if( Session::get('tracking_id')!='' && Session::get('password')!='') {

      ?>
      	<div class="row mb">
  		<div class="col-md-12">
		    <div class="panel panel-info">
		      <div class="panel-heading">Ref Id and Password</div>
		      <div class="panel-body bg-success" style="font-size:18px;">
		      	<p>Your Ref Id : <span style="color: red;">{{session::get('tracking_id')}}</span> And Password: <span style="color: red;">{{Session::get('password')}}</span> Please remember your Ref Id and password for login. You need to login several time for admission purpose</p>
		      </div>
		    </div>
	    </div>
    </div>
    <?php } ?>
   <form action="{{route('student.masters1st.admission.mastersInformationSubmit')}}" method="POST" enctype="multipart/form-data">
   	@csrf
    
    <div class="row mb">
    	<div class="col-md-12">
	    <div class="panel panel-primary">
	    	<div class="panel-heading">Password</div>
	      <div class="panel-body">

	         	<div class="form-group">
	           	<div class="col-sm-6">		                 
	              <div class="form-group">
	                {!! Form::password('password', ['id'=> 'inputPassword', 'class'=> 'form-control input-lg', 'data-minlength'=> '3', 'placeholder'=> 'Password', 'required'=> true]) !!}
	                <div class="help-block">Minimum of 3 characters</div>
	              </div>
	            </div>   
	           	<div class="col-sm-6">               
	             <div class="form-group">
	               {!! Form::password('password', ['id'=> 'inputPasswordConfirm', 'class'=> 'form-control input-lg', 'data-minlength'=> '3', 'placeholder'=> 'Confirm Password', 'required'=> true, 'data-match'=> '#inputPassword','data-match-error'=> "Password don't match"]) !!}
	             <div class="controls">
                  <div class="confirm_password_error"></div>
                </div>
	            	</div>
	        		</div>
	       		</div> 
	      </div>
	    </div>
	    </div>
    </div>

    <div class="row mb">
      <div class="col-md-12">
	    <div class="panel panel-primary">
	    	<div class="panel-heading">Masters Information</div>
	      <div class="panel-body">

	         <div class="form-group">
	           	<div class="col-sm-12">		                 
	              <div class="form-group">
					  
							<table class='table table-bordered table-striped table-hover'>
							<tr><td>Admission Roll</td> <td><input type="text" name="admission_roll" value="<?php echo $admission_roll ?>"  class="form-control" readonly></td><tr>
							</table>
					  
	              </div>
	            </div>   

	       	</div> 
	      </div>
	    </div>
	   </div>
    </div>	
	

	@php
	$config = DB::table('masters_online_adm_config')->where('open', 1)->where('current_level', 'Masters 2nd Year')->first();
	@endphp
	
    <div class="row mb">	
	   	<div class="col-md-6">
		   	<div class="panel panel-primary">
			  	<div class="panel-heading">Persional Information</div>
			    <div class="panel-body">
				   	
						  <div class="form-group">
						    <label for="name" class="col-sm-4 control-label">Name</label>
						    <div class="col-sm-8">
						      {!! Form::text('student_name', $name, ['class'=> 'form-control', 'id'=> 'name', 'required'=> true, 'readonly' => true]) !!}
						      <div class="help-block"></div>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="photo" class="col-sm-4 control-label">Your picture</label>
						    <div class="col-sm-8">
						    <div class="img-thumbnail mb-1" id="student_img_image_pre_area" style="display: none; width: 80px;">
                  <img style="height: 100%; width: 100%;" src="" id="student_img_image_pre" alt="Not Set Yet">
                </div>

                 {!! Form::file('photo', ['class'=> 'form-control image_data', 'id'=> 'photo', 'required'=> true, 'data-type' => 'student_img']) !!}
						      <div class="help-block"></div>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="name_bangla" class="col-sm-4 control-label">Name (In Bangla)</label>
						    <div class="col-sm-8">
						      {!! Form::text('name_bangla', null, ['class'=> 'form-control', 'id'=> 'name_bangla', 'required'=> true, 'placeholder' => "Name in Bangla"]) !!}
						      <div class="help-block"></div>
						    </div>
						  </div>
						  <div class="form-group">
						    <label for="father_name" class="col-sm-4 control-label">Father's Name</label>
						    <div class="col-sm-8">
						      {!! Form::text('father_name', null, ['class'=> 'form-control', 'id'=> 'father_name', 'required'=> true, 'placeholder' => "Father's Name"]) !!}
						      <div class="help-block"></div>
						    </div>
						    
						  </div>
						  
						  <div class="form-group">
						    <label for="mother_name" class="col-sm-4 control-label">Mother's Name</label>
						    <div class="col-sm-8">
						      {!! Form::text('mother_name', null, ['class' => 'form-control', 'placeholder' => "Mother's Name" , 'required' => true, 'id' => 'mother_name']) !!}
						      <div class="help-block">{{invalid_feedback('photo')}}</div>
						    </div>
						  </div>
						
							  <div class="form-group">
							    <label for="birth_date" class="col-sm-4 control-label">Date Of Birth</label>
								    <div class="col-sm-8">
								      {!! Form::text('birth_date', null, ['class' => 'form-control date', 'placeholder' => "Date Of Birth" , 'required' => true, 'id' => 'birth_date', 'autocomplete'=> 'off']) !!}
								      <div class="help-block"></div>
								    </div>
							  </div>

						  <div class="form-group">
						    <label for="blood_group" class="col-sm-4 control-label">Blood Group</label>
							     <div class="col-sm-8">
								      {!! Form::select('blood_group', selective_blood_lists(), null, ['class'=> 'form-control', 'id'=> 'blood_group', 'required' => true]) !!}
							      <div class="help-block"></div>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="gender" class="col-sm-4 control-label">Gender</label>
							     <div class="col-sm-8">
								         {!! Form::select('gender', selective_gender_list(), null, ['class'=> 'form-control', 'id'=> 'gender', 'required' => true]) !!}
							      <div class="help-block"></div>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="religion" class="col-sm-4 control-label">Religion</label>
							     <div class="col-sm-8">
								      {!! Form::select('religion', selective_religion_list(), null, ['class'=> 'form-control', 'id'=> 'religion', 'required' => true]) !!}
							      <div class="help-block"></div>
						    </div>
						  </div>
						
					</div>
				</div>
			</div>
			<div class="col-md-6">
		   	<div class="panel panel-primary">
			  	<div class="panel-heading">Guardian Information</div>
			    <div class="panel-body">
							  <div class="form-group">
						      <label for="guardian_info" class="col-sm-4 control-label">Guardian Info</label>
							    <div class="col-sm-8">
							      <input type="radio" name="guardian_info" value="Father" >&nbsp;&nbsp;Father&nbsp;&nbsp;&nbsp;&nbsp;
							      <input type="radio" name="guardian_info" value="Mother" >&nbsp;&nbsp;Mother&nbsp;&nbsp;&nbsp;&nbsp;
							      <input type="radio" name="guardian_info" value="Other" >&nbsp;&nbsp;Other

							      <div class="help-block"></div>
							    </div>
						    </div>


						    <div class="form-group">
						     <label for="guardian_name" class="col-sm-4 control-label">Guardian Name</label>
						      <div class="col-sm-8">
						         {!! Form::text('guardian_name', null, ['class' => 'form-control', 'placeholder' => "Guardian Name" , 'required' => true, 'id' => 'guardian_name']) !!}
						         <div class="help-block"></div>
						      </div>
						    </div>
						    <div class="form-group">
						     <label for="guardian_relation" class="col-sm-4 control-label">Guardian Relation</label>
						      <div class="col-sm-8">
						         {!! Form::text('guardian_relation', null, ['class' => 'form-control', 'placeholder' => "Guardian Relation" , 'required' => true, 'id' => 'guardian_relation']) !!}
						         <div class="help-block"></div>
						      </div>
						    </div>

						    <div class="form-group">
						     <label for="guardian_mobile" class="col-sm-4 control-label">Guardian Mobile</label>
						      <div class="col-sm-8">
						         {!! Form::text('guardian_mobile', null, ['class' => 'form-control', 'placeholder' => "Guardian Mobile" , 'required' => true, 'id' => 'guardian_mobile']) !!}
						         <div class="help-block"></div>
						      </div>
						    </div>

						    <div class="form-group">
						     <label for="father_name" class="col-sm-4 control-label">Guardian Occupation</label>
						      <div class="col-sm-8">
						         {!! Form::text('occupation', null, ['class' => 'form-control', 'placeholder' => "Guardian Occupation" , 'required' => true, 'id' => 'occupation']) !!}
						         <div class="help-block"></div>
						      </div>
						    </div>
						    <div class="form-group">
						    <label for="religion" class="col-sm-4 control-label">Guardian's Yearly Income </label>
							     <div class="col-sm-8">
										{!! Form::text('income', null, ['class' => 'form-control', 'required' => true, 'id' => 'income']) !!}
							       <div class="help-block">{{invalid_feedback('income')}}</div>
						       </div>
						  </div>
						
					</div>
				</div>
			</div>
		</div>
	<!-- 	End of Persional & Guardian  -->
	<div class="row mb">	
	   	<div class="col-md-6">
		   	<div class="panel panel-primary">
			  	<div class="panel-heading">Present Address</div>
			    <div class="panel-body">
						  <div class="form-group">
						    <label for="present_village" class="col-sm-4 control-label">Village</label>
						    <div class="col-sm-8">
						      {!! Form::text('present_village', null, ['class' => 'form-control', 'placeholder' => "Village" , 'required' => true, 'id' => 'present_village']) !!}
						      <div class="help-block"></div>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="present_po" class="col-sm-4 control-label">Post Office</label>
						    <div class="col-sm-8">
						      {!! Form::text('present_po', null, ['class' => 'form-control', 'placeholder' => "Post Office" , 'required' => true, 'id' => 'present_po']) !!}
						      <div class="help-block">{!!invalid_feedback('present_po')!!}</div>
						    </div>
						  </div>
						  <?php  $dist=DB::table('district_thana')->distinct()->get(['district']); ?>
						  <div class="form-group">
						    <label for="present_dist" class="col-sm-4 control-label">District</label>
							     <div class="col-sm-8">
							        <select required="required" name="present_dist" id="present_dist" class="form-control">
							          <option>--Select--</option>
							          <?php foreach($dist as $value)  {?>
							          	<option value="<?php echo $value->district ?>"><?php echo $value->district ?></option>
							          	<?php  } ?>
							         </select>
							      <div class="help-block"></div>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="present_thana" class="col-sm-4 control-label">Thana</label>
							     <div class="col-sm-8">
								        <select required="required" name="present_thana" id="present_thana" class="form-control">
								          <option value="">--Select--</option>
								         </select>
							      <div class="help-block"></div>
						    </div>
						  </div>

						   <div class="form-group">
						    <label for="student_mobile" class="col-sm-4 control-label">Mobile Number</label>
						    <div class="col-sm-8">
						      {!! Form::text('student_mobile', null, ['class' => 'form-control', 'placeholder' => "01xxxxxxxxx" , 'required' => true, 'id' => 'student_mobile', 'maxlength' => '11']) !!}
						      <div class="help-block">{!!invalid_feedback('student_mobile')!!}</div>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="student_mobile_re" class="col-sm-4 control-label">Retype Mobile Number</label>
						    <div class="col-sm-8">
						      <input required="required" type="text"  class="form-control" name="student_mobile_re" id="student_mobile_re" value="">
						      <div class="help-block"></div>
						    </div>
	                <div class="controls">
	                  <div class="mobile_number_confirmation_error"></div>
	                </div>						    
						  </div>
					</div>
				</div>
			</div>	

			<div class="col-md-6">
		   	<div class="panel panel-primary">
			  	<div class="panel-heading">Permanent Address</div>
           	<div class="controls">
            	<input style="margin-bottom:1em;margin-left: 1em;" type="checkbox" name="same" id="same"> Same as present address </div>			  	
			    <div class="panel-body">
						  <div class="form-group">
						    <label for="permanent_village" class="col-sm-4 control-label">Village</label>
						    <div class="col-sm-8">
						      {!! Form::text('permanent_village', null, ['class' => 'form-control', 'placeholder' => "Permanent Village" , 'required' => true, 'id' => 'permanent_village']) !!}
						      <div class="help-block"></div>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="permanent_post_office" class="col-sm-4 control-label">Post</label>
						    <div class="col-sm-8">
						      {!! Form::text('permanent_post_office', null, ['class' => 'form-control', 'placeholder' => "Permanent Post Office" , 'required' => true, 'id' => 'permanent_post_office']) !!}
						      <div class="help-block"></div>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="permanent_district" class="col-sm-4 control-label">District</label>
							     <div class="col-sm-8">
								    <select required="required" name="permanent_district" id="permanent_district" class="form-control">
						          <option>--Select--</option>
						          <?php foreach($dist as $value1)  {?>
						          	<option value="<?php echo $value1->district ?>"><?php echo $value1->district ?></option>
						          	<?php  } ?>
						         </select>
							      <div class="help-block"></div>
						    </div>
						  </div>

						  <div class="form-group">
						    <label for="permanent_thana" class="col-sm-4 control-label">Thana</label>
							     <div class="col-sm-8">
								        <select required="required" name="permanent_thana" id="permanent_thana" class="form-control">
								          <option value="">--Select--</option>
								          

								         </select>
							      <div class="help-block"></div>
						    </div>
						  </div>

						   <div class="form-group">
						    <label for="student_mobile" class="col-sm-4 control-label">Mobile Number</label>
						    <div class="col-sm-8">
						      {!! Form::text('permanent_mobile_no', null, ['class' => 'form-control', 'placeholder' => "Permanent Mobile Number" , 'required' => true, 'id' => 'permanent_mobile_no']) !!}
						      <div class="help-block"></div>
						    </div>
						  </div>						  						  
					</div>
				</div>
			</div>

      </div>

      <div class="row mb">	
	    		<div class="col-md-12">
	    			<div class="panel panel-primary" style="overflow: auto;">
	    				<div class="panel-heading">Academic Information</div>
	    				<div class="panel-body">
	    					<table class="table">
	    						<tbody>
	    							<tr class="warning">
	    								<td width="10%"></td>
	    								<td width="20%">Institution</td>
	    								<td width="10%">Board</td>
	    								<td width="15%">Roll</td>
	    								<td width="10%">Passing Year</td>
	    								<td width="10%">GPA</td>
	    							</tr>

	    							<tr class="success">
	    								<td>S.S.C.</td>
	    								<td>
	    									{!! Form::text('ssc_institution', null, ['class' => 'input-xlarge focused form-control', 'placeholder' => "Institution" , 'required' => true, 'id' => 'ssc_institution', 'for'=> 'focusedInput']) !!}
	    									<div class="ssc_institution_error"></div>
	    								</td>

	    								<td> 
	    									{!! Form::select('ssc_board', selective_boards(), null, ['id'=> 'ssc_board', 'required' => true, 'class'=> ' form-control']) !!}
	    									<div class="ssc_board_error"></div>
	    								</td>

	    								<td>
	    									{!! Form::text('ssc_roll', null, ['class' => 'input-medium focused form-control', 'placeholder' => "Roll" , 'required' => true, 'id' => 'ssc_roll', 'for'=> 'focusedInput']) !!}
	    									<div class="ssc_roll_error">{!!invalid_feedback('ssc_roll')!!}</div>
	    								</td>

	    								@php
	    									// dd(selective_multiple_passing_year(2007, 2015));
	    								@endphp

	    								<td>
	    									{!! Form::select('ssc_passing_year', selective_multiple_passing_year(2005, 2015), null, ['id'=> 'ssc_passing_year', 'required' => true, 'class'=> ' form-control']) !!}
	    									<div class="ssc_passing_year_error"></div>
	    								</td>
	    								<td>
	    									{!! Form::text('ssc_gpa', null, ['class' => 'input-small focused form-control', 'placeholder' => "GPA" , 'required' => true, 'id' => 'ssc_gpa', 'for'=> 'focusedInput']) !!}
	    									<div class="ssc_gpa_error">{!!invalid_feedback('ssc_gpa')!!}</div>
	    								</td>
	    							</tr>

	    							<tr class="success">
	    								<td>H.S.C</td>
	    								<td> 
	    									{!! Form::text('hsc_institution', null, ['class' => 'input-xlarge focused form-control', 'placeholder' => "Institution" , 'required' => true, 'id' => 'hsc_institution', 'for'=> 'focusedInput']) !!}
	    									<div class="hsc_institution_error"></div>
	    								</td>

	    								<td> 
	    									{!! Form::select('hsc_board', selective_boards(), null, ['id'=> 'hsc_board', 'required' => true, 'class'=> ' form-control']) !!}
	    									<div class="hsc_board_error"></div>
	    								</td>
	    								<td>
	    									{!! Form::text('hsc_roll', null, ['class' => 'input-medium focused form-control', 'placeholder' => "Roll" , 'required' => true, 'id' => 'hsc_roll', 'for'=> 'focusedInput']) !!}
	    									<div class="hsc_roll_error">{!!invalid_feedback('hsc_roll')!!}</div>
	    								</td>

	    								<td>
	    									{!! Form::select('hsc_passing_year', selective_multiple_passing_year(2008, 2018), null, ['id'=> 'hsc_passing_year', 'required' => true, 'class'=> ' form-control']) !!}
	    									<div class="hsc_passing_error"></div>
	    								</td>
	    								<td>
	    									{!! Form::text('hsc_gpa', null, ['class' => 'input-small focused form-control', 'placeholder' => "GPA" , 'required' => true, 'id' => 'hsc_gpa', 'for'=> 'focusedInput']) !!}
	    									<div class="hsc_gpa_error">{!!invalid_feedback('hsc_gpa')!!}</div>
	    								</td>
	    							</tr>

		    						<tr class="success">
	                    <td>Deg.</td>
	                    <td colspan="2">
	                      {!! Form::text('deg_institution', null, ['class' => 'input-xlarge focused form-control', 'placeholder' => "Institution" , 'required' => true, 'id' => 'deg_institution', 'for'=> 'focusedInput']) !!}

	                    </td>

	                    <td>
	                      {!! Form::select('deg_institution_session', selective_multiple_session(), null, ['class'=> 'form-control', 'id'=>'deg_institution_session']) !!}
	                    </td>

	                    <td>

	                      {!! Form::select('deg_passing_year', selective_multiple_passing_year(), null, ['id'=> 'deg_passing_year', 'required' => true, 'class'=> ' form-control']) !!}
	                      
	                    </td>
	                    <td>

	                    {!! Form::text('deg_cgpa', null, ['class' => 'input-small focused form-control', 'placeholder' => "CGPA (in 4)" , 'required' => true, 'id' => 'deg_cgpa', 'for'=> 'focusedInput']) !!}
	    								<div class="deg_cgpa_error">{!!invalid_feedback('deg_cgpa')!!}</div>
	                      
	                    </td>
	                  </tr>

	    						</tbody>
	    					</table>
	    				</div>
	    			</div>    
	    		</div>

	    	</div>


    	<button style="float: right;" type="submit" class="btn btn-danger navbar-btn" >Submit</button>						    									  
		</form>

 </div>			  

 </div>

		<script src="{{ asset('fjs/jquery.min.js') }}"></script>
  	<script src="{{ asset('fjs/bootstrap.min.js') }}"></script>
  	<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
  	<script src="{{ asset('fjs/scripts.js') }}"></script>
  	<script src="{{ asset('fjs/masters_1st_admission.js') }}"></script>
  	<script src="{{ asset('vendors/iziToast/iziToast.min.js') }}"></script>

  	@include('common.dropdown_js')
  	@include('common.message')

  	<script type="text/javascript">

		$('form').on('submit', function() {

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
		</script>
</body>


</html>

