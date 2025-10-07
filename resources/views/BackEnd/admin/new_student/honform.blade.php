@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Add New Honours Student')

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

{!! Form::open(['route'=> 'admin.newstudent.honSubmit', 'method' => 'post', 'files'=> true]) !!}
<div class="col-md-12">
	<div class="card border border-primary">
	  <div class="card-header bg-primary">
		Persional Information
	  </div>
	  <div class="card-block">
		
				   	
		  <div class="form-group row">
		    <label for="name" class="col-sm-4 col-form-label">Name</label>
		    <div class="col-sm-8">
		      <input type="text" name="student_name"  class="form-control" id="name" required="required" >
		      {!!invalid_feedback('student_name')!!}
		    </div>
		  </div>
		  <div class="form-group row">
		    <label for="admission_roll" class="col-sm-4 col-form-label">Admission Roll</label>
		    <div class="col-sm-8">
		      <input type="text" name="admission_roll"  required="required" class="form-control"  required="required" >
		      {!!invalid_feedback('admission_roll')!!}
		    </div>
		  </div>						  
		  <div class="form-group row">
		    <label for="faculty" class="col-sm-4 col-form-label">Faculty</label>
			     <div class="col-sm-8">
				     {!! Form::select('faculty', selective_multiple_faculty(), null, ['class' => 'form-control', 'id'=> 'faculty', 'required' => true]) !!}
			      {!!invalid_feedback('faculty')!!}
		    </div>
		  </div>
		  <div class="form-group row">
		    <label for="subject" class="col-sm-4 col-form-label">Subject</label>
		    <div class="col-sm-8">
			{!! Form::select('subject', selective_multiple_subject(), null, ['class' => 'form-control select2', 'required'=> true]) !!}
		      {!!invalid_feedback('subject')!!}
		    </div>
		  </div>
		  <div class="form-group row">
		    <label for="religion" class="col-sm-4 col-form-label">Current Level</label>
			     <div class="col-sm-8">
				  {!! Form::select('current_level', selective_multiple_honours_level(), null, ['class' => 'form-control', 'required'=> true]) !!}
			      {!!invalid_feedback('current_level')!!}
		    </div>
		  </div>	
		  <div class="form-group row">
		    <label for="session" class="col-sm-4 col-form-label">Session</label>
			     <div class="col-sm-8">
				  {!! Form::select('session', selective_multiple_session(), null, ['class' => 'form-control', 'id'=> 'session', 'required' => true]) !!}
			      {!!invalid_feedback('session')!!}
		    </div>
		  </div>						  
		  <div class="form-group row">
		    <label for="photo" class="col-sm-4 col-form-label">Your picture</label>
		    <div class="col-sm-8">
    		<input  type="file" name="photo" class="form-control" id="photo">
		      {!!invalid_feedback('photo')!!}
		    </div>
		  </div>
		  <div class="form-group row">
		    <label for="father_name" class="col-sm-4 col-form-label">Father's Name</label>
		    <div class="col-sm-8">
		      <input class="form-control" name="father_name"  placeholder="Father's Name"  value="">
		      {!!invalid_feedback('father_name')!!}
		    </div>
		    
		  </div>
		  
		  <div class="form-group row">
		    <label for="mother_name" class="col-sm-4 col-form-label">Mother's Name</label>
		    <div class="col-sm-8">
		      <input class="form-control" name="mother_name" id="mothers_name" placeholder="Mother's Name"  value="">
		      {!!invalid_feedback('mother_name')!!}
		    </div>
		  </div>
		
		  <div class="form-group row">
			<label for="birth_date" class="col-sm-4 col-form-label">Date Of Birth</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control date" name="birth_date" id="birth_date" placeholder="Date Of Birth" value="">
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
			        <select name="gender" id="gender" class="form-control">
			          <option value="" >--Select--</option>
			          <option value="Male">Male</option>
			          <option value="Female">Female</option>

			         </select>
			      {!!invalid_feedback('gender')!!}
		    </div>
		  </div>

		  <div class="form-group row">
		    <label for="religion" class="col-sm-4 col-form-label">Religion</label>
			     <div class="col-sm-8">
			        <select  name="religion" id="religion" class="form-control">
			          <option value="" >--Select--</option>
			          <option value="Islam">Islam</option>
			          <option value="Hinduism">Hinduism</option>
			          <option value="Chirstian">Chirstian</option>
			          <option value="Buddhist">Buddhist</option>
			         </select>
			      {!!invalid_feedback('religion')!!}
		    </div>
		  </div>
	     <div class="form-group row">
		    <label for="student_mobile" class="col-sm-4 col-form-label">Mobile Number</label>
		    <div class="col-sm-8">
		      <input required="required" type="text"  class="form-control" name="student_mobile" id="student_mobile" value="" placeholder="01xxxxxxxxx" maxlength="11">
		      {!!invalid_feedback('student_mobile')!!}
		    </div>
		  </div>
	  </div>
	</div>
</div>

<div class="col-md-12">
	<div class="card border border-primary">
	  <div class="card-header bg-primary">
		Academic Information
	  </div>
	  <div class="card-block">
		
        <table class="table table-striped table-responsive table-bordered">
        	<thead>
                <tr class="warning">
                    <th width="10%"></th>
                    <th width="20%">Institution</th>
                    <th width="10%">Board</th>
                    <th width="10%">Roll</th>
                    <th width="15%"> Passing Year</th>
					<th width="10%">GPA</th>
                </tr>
        		
        	</thead>
            <tbody>
                <tr>
                    <td>S.S.C.</td>
                    <td> 
                      <input  class="input-xlarge focused" id="ssc_institution" name="ssc_institution" for="focusedInput" type="text" placeholder="Institution" value="">
                        <div class="ssc_institution_error"></div>
                    </td>

                    <td> 
                      <!-- <input class="input-medium focused" id="ssc_board" for="focusedInput" type="text" placeholder="Board" value=""> -->

                      {!! Form::select('ssc_board', selective_boards(), null, ['id'=> 'ssc_board']) !!}
                        <div class="ssc_board_error"></div>
                    </td>
                    
                    <td>
                      <input  class="input-medium focused" id="ssc_roll" name="ssc_roll" for="focusedInput" type="text" placeholder="Roll" value="">
                        <div class="ssc_roll_error"></div>
                    </td>
                    <td>
                      <!-- <input class="input-medium focused" id="ssc_passing_year" for="focusedInput" type="text" placeholder="Passing Year" value=""> -->

                      {!! Form::select('ssc_passing_year', selective_multiple_exam_year(), null, ['id'=>'ssc_passing_year', 'required'=> true]) !!}
                        <div class="ssc_passing_year_error"></div>
                    </td>
					 <td><input  class="input-small focused" id="ssc_gpa" for="focusedInput" type="text" placeholder="GPA" value="" name="ssc_gpa">
                      <div class="ssc_gpa_error"></div>
                    </td>
                </tr>

                <tr class="success">
                    <td>H.S.C</td>
                    <td> 
                      <input  class="input-xlarge focused" id="hsc_institution" for="focusedInput" type="text" placeholder="Institution" name="hsc_institution" value="">
                      <div class="hsc_institution_error"></div>
                    </td>

                    <td> 
                      <!-- <input class="input-medium focused" id="hsc_board" for="focusedInput" type="text" placeholder="Board" value=""> -->

                      {!! Form::select('hsc_board', selective_boards(), null, ['id' => 'hsc_board']) !!}
                      <div class="hsc_board_error"></div>
                    </td>
                    <td>
                      <input  name="hsc_roll" class="input-medium focused" id="hsc_roll" for="focusedInput" type="text" placeholder="Roll" value="">
                      <div class="hsc_roll_error"></div>
                    </td>
                    <td>
                      <!-- <input class="input-medium focused" id="hsc_passing_year" for="focusedInput" type="text" placeholder="Passing Year" value=""> -->
                      {!! Form::select('hsc_passing_error', selective_multiple_exam_year(), null, ['id'=>'hsc_passing_error', 'required'=> true]) !!}
                      <div class="hsc_passing_error"></div>
                    </td>
					  <td>
                      <input required="required" class="input-small focused" id="hsc_gpa" for="focusedInput" type="text" placeholder="GPA" value="" name="hsc_gpa">
                      <div class="hsc_gpa_error"></div>
                    </td>
                </tr>
                
            </tbody>
        </table>
	  </div>
	</div>
	<button type="submit" class="btn btn-danger navbar-btn" >Submit</button>
</div>
{!! Form::close() !!}

@endsection

@push('scripts')

@endpush