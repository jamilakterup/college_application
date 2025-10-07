{!! Form::open(['route'=> 'students.degree.store', 'method' => 'post', 'files'=> true, 'data-form'=> 'postForm']) !!}


@php
	$query_present_ps = isset($student) ? ["district=" => $student->present_dist] : null;
	$query_permanent_ps = isset($student) ? ["district=" => $student->permanent_dist] : null;
@endphp


<h4>Personal Info</h4>
<div class="row mb-3">
	<div class="col-lg-6">
		<div class="form-group">
			<label for="name" class="col-form-label">Name</label>
			{!! Form::text('student_name', @$student->name?? null, ['class'=> 'form-control', 'placeholder'=> 'Student Name', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
		</div>
		<div class="form-group">
			<label for="admission_roll" class="col-form-label">Admission Roll</label>
			{!! Form::text('admission_roll', @$student->admission_roll ?? null, ['class'=> 'form-control', 'placeholder'=> 'Admission Roll', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
		</div>
		
		<div class="form-group">
			<label for="groups" class="col-form-label">Groups</label>
			{!! Form::select('groups', selective_degree_subjects(), $student->groups ?? null ,['class'=> 'form-control selectize', 'data-placeholder'=>'--Select Groups--','id'=> 'groups']) !!}
			<div class="invalid-feedback"></div>
		</div>
		
		<div class="form-group">
			<label for="religion" class="col-form-label">Current Level</label>
			{!! Form::select('current_level', selective_multiple_degree_level(), $student->current_level ?? null, ['class' => 'form-control', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
		</div>	
		<div class="form-group">
			<label for="session" class="col-form-label">Session</label>
			{!! Form::select('session', selective_multiple_session(), $student->session ?? null, ['class' => 'form-control', 'id'=> 'session', 'required' => true]) !!}
			<div class="invalid-feedback"></div>
		</div>						  
		<div class="form-group">
			<label for="photo" class="col-form-label">Your picture</label>
			<input  type="file" name="photo" class="form-control" id="photo">
			<div class="invalid-feedback"></div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="form-group">
			<label for="father_name" class="col-form-label">Father's Name</label>
			{!! Form::text('father_name', $student->father_name ?? null, ['class'=> 'form-control', 'placeholder'=> 'Father\'s Name', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
			
		</div>
		
		<div class="form-group">
			<label for="mother_name" class="col-form-label">Mother's Name</label>
			{!! Form::text('mother_name', $student->mother_name ?? null, ['class'=> 'form-control', 'placeholder'=> 'Mother\'s Name', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
		</div>
		
		<div class="form-group">
			<label for="birth_date" class="col-form-label">Date Of Birth</label>
			{!! Form::text('birth_date', $student->birth_date ?? null, ['class'=> 'form-control date', 'placeholder'=> 'Date of Birth', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
		</div>
		<div class="form-group">
			<label for="blood_group" class="col-form-label">Blood Group</label>
			{!! Form::select('blood_group', selective_blood_lists(), $student->blood_group ?? null, ['class' => 'form-control selectize', 'id'=> 'blood_group']) !!}
				<div class="invalid-feedback"></div>
		</div>
		<div class="form-group">
			<label for="gender" class="col-form-label">Gender</label>
			{!! Form::select('gender', selective_gender_list(), $student->gender ?? null, ['class' => 'form-control selectize', 'id'=> 'gender']) !!}
			<div class="invalid-feedback"></div>
		</div>
		
		<div class="form-group">
			<label for="religion" class="col-form-label">Religion</label>
			{!! Form::select('religion', selective_religion_list(), $student->religion ?? null, ['class' => 'form-control selectize', 'id'=> 'gender']) !!}
				<div class="invalid-feedback"></div>
		</div>
		<div class="form-group">
			<label for="student_mobile" class="col-form-label">Mobile Number</label>
			{!! Form::text('student_mobile', $student->contact_no ?? null, ['class'=> 'form-control', 'placeholder'=> 'Student Mobile', 'required'=> true, 'maxlength' => '11']) !!}
			<div class="invalid-feedback"></div>
		</div>
	</div>
</div>

<h4>Address Info</h4>
<div class="row">
	<div class="col-lg-6">
		<label></label>
		<div class="form-group">
			<label for="present_village" class="col-form-label">Present Village</label>
			{!! Form::text('present_village', $student->present_village ?? null, ['class'=> 'form-control', 'placeholder'=> 'Present Village', 'required'=> true, 'id'=> 'present_village']) !!}
			<div class="invalid-feedback"></div>
		</div>
		<div class="form-group">
			<label for="present_po" class="col-form-label">Present Post Office</label>
			{!! Form::text('present_po', $student->present_po ?? null, ['class'=> 'form-control', 'placeholder'=> 'Present Post Office', 'required'=> true, 'id' => 'present_po']) !!}
			<div class="invalid-feedback"></div>
		</div>
		
		<div class="form-group">
			<label for="present_dist" class="col-form-label">Present District</label>
			{!! Form::select('present_dist', create_option_array('district_thana', 'district', 'district', 'District'), $student->present_dist ?? null, ['class' => 'form-control', 'onchange'=> 'getThanaOption(this);' , 'data-for'=> 'present_ps', 'id'=> 'present_dist']) !!}
			<div class="invalid-feedback"></div>
		</div>

		<div class="form-group">
			<label for="present_ps" class="col-form-label">Present Thana</label>
			{!! Form::select('present_ps', create_option_array('district_thana', 'thana', 'thana', 'Thana', $query_present_ps), $student->present_ps ?? null, ['class' => 'form-control', 'id'=> 'present_ps', 'data-placeholder'=> '--Select Thana--']) !!}
			<div class="invalid-feedback"></div>
		</div>
	</div>

	<div class="col-lg-6">
		<input type="checkbox" name="same_as_present" id="same_as_present"> <label for="same_as_present">Same as Present Address</label>
		<div class="form-group">
			<label for="permanent_village" class="col-form-label">Permanent Village</label>
			{!! Form::text('permanent_village', $student->permanent_village ?? null, ['class'=> 'form-control', 'placeholder'=> 'Permanent Village', 'required'=> true, 'id'=> 'permanent_village']) !!}
			<div class="invalid-feedback"></div>
		</div>
		<div class="form-group">
			<label for="permanent_po" class="col-form-label">Permanent Post Office</label>
			{!! Form::text('permanent_po', $student->permanent_po ?? null, ['class'=> 'form-control', 'placeholder'=> 'Permanent Post Office', 'required'=> true, 'id' => 'permanent_po']) !!}
			<div class="invalid-feedback"></div>
		</div>
		
		<div class="form-group">
			<label for="permanent_dist" class="col-form-label">Permanent District</label>
			{!! Form::select('permanent_dist', create_option_array('district_thana', 'district', 'district', 'District'), $student->permanent_dist ?? null, ['class' => 'form-control','onchange'=> 'getThanaOption(this);' , 'data-for'=> 'permanent_ps', 'id' => 'permanent_dist']) !!}
			<div class="invalid-feedback"></div>
		</div>

		<div class="form-group">
			<label for="permanent_ps" class="col-form-label">Permanent Thana</label>
			{!! Form::select('permanent_ps', create_option_array('district_thana', 'thana', 'thana', 'Thana', $query_permanent_ps), $student->permanent_ps ?? null, ['class' => 'form-control', 'id'=> 'permanent_ps', 'data-placeholder'=> '--Select Thana--']) !!}
			<div class="invalid-feedback"></div>
		</div>
	</div>
</div>

<h4>Academic Info</h4>
<div class="row">
	<div class="col-12">
		<table class="table table-striped table-responsive table-bordered">
			<thead>
				<tr class="warning">
					<th width="10%"></th>
					<th width="20%">Institution</th>
					<th width="10%">Board</th>
					<th width="15%">Roll</th>
					<th width="15%">Reg No.</th>
					<th width="10%"> Passing Year</th>
					<th width="10%">GPA</th>
				</tr>
			</thead>
			<tbody>
				<tr class="success">
					<td>S.S.C.</td>
					<td>
						{!! Form::text('ssc_institution', $student->admitted_student->ssc_institute ?? null, ['class' => 'form-control form-control-sm', 'placeholder' => "Institution" , 'required' => true, 'id' => 'ssc_institution', 'for'=> 'focusedInput']) !!}
						<div class="invalid-feedback"></div>
					</td>

					<td> 
						{!! Form::select('ssc_board', selective_boards(), $student->admitted_student->ssc_board ?? null, ['id'=> 'ssc_board', 'required' => true, 'class'=> 'form-control form-control-sm']) !!}
						<div class="invalid-feedback"></div>
					</td>

					<td>
						{!! Form::text('ssc_roll', $student->admitted_student->ssc_roll ?? null, ['class' => 'form-control form-control-sm', 'placeholder' => "Roll" , 'required' => true, 'id' => 'ssc_roll', 'for'=> 'focusedInput']) !!}
						<div class="invalid-feedback"></div>
					</td>

					<td>
						{!! Form::text('ssc_reg', $student->admitted_student->ssc_reg ?? null, ['class' => 'form-control form-control-sm', 'placeholder' => "Reg No." , 'required' => true, 'id' => 'ssc_reg', 'for'=> 'focusedInput']) !!}
						<div class="invalid-feedback"></div>
					</td> 
					<td>
						{!! Form::select('ssc_passing_year', selective_multiple_passing_year(), $student->admitted_student->ssc_pass_year ?? null, ['id'=> 'ssc_passing_year', 'required' => true, 'class'=> 'form-control form-control-sm']) !!}
						<div class="invalid-feedback"></div>
					</td>
					<td>
						{!! Form::text('ssc_gpa', $student->admitted_student->ssc_gpa ?? null, ['class' => 'form-control form-control-sm', 'placeholder' => "GPA" , 'required' => true, 'id' => 'ssc_gpa', 'for'=> 'focusedInput']) !!}
						<div class="invalid-feedback"></div>
					</td>
				</tr>

				<tr class="success">
					<td>H.S.C</td>
					<td> 
						{!! Form::text('hsc_institution', $student->admitted_student->hsc_institute ?? null, ['class' => 'form-control form-control-sm', 'placeholder' => "Institution" , 'required' => true, 'id' => 'hsc_institution', 'for'=> 'focusedInput']) !!}
						<div class="invalid-feedback"></div>
					</td>

					<td> 
						{!! Form::select('hsc_board', selective_boards(),  $student->admitted_student->hsc_board ?? null, ['id'=> 'hsc_board', 'required' => true, 'class'=> 'form-control form-control-sm']) !!}
						<div class="invalid-feedback"></div>
					</td>
					<td>
						{!! Form::text('hsc_roll', $student->admitted_student->hsc_roll ?? null, ['class' => 'form-control form-control-sm', 'placeholder' => "Roll" , 'required' => true, 'id' => 'hsc_roll', 'for'=> 'focusedInput']) !!}
						<div class="invalid-feedback"></div>
					</td>

					<td>
						{!! Form::text('hsc_reg', $student->admitted_student->hsc_reg ?? null, ['class' => 'form-control form-control-sm', 'placeholder' => "Reg No." , 'required' => true, 'id' => 'hsc_reg', 'for'=> 'focusedInput']) !!}
						<div class="invalid-feedback"></div>
					</td>  
					<td>
						{!! Form::select('hsc_passing_year', selective_multiple_passing_year(), $student->admitted_student->hsc_pass_year ?? null, ['id'=> 'hsc_passing_year', 'required' => true, 'class'=> 'form-control form-control-sm']) !!}
						<div class="invalid-feedback"></div>
					</td>
					<td>
						{!! Form::text('hsc_gpa',$student->admitted_student->hsc_gpa ?? null, ['class' => 'form-control form-control-sm', 'placeholder' => "GPA" , 'required' => true, 'id' => 'hsc_gpa', 'for'=> 'focusedInput']) !!}
						<div class="invalid-feedback"></div>
					</td>
				</tr>
			</tbody>
		</table>

	</div>
</div>
<div class="float-right clear mb-2">
	{!! Form::submit('Save Data', ['class'=> 'btn btn-primary','data-value'=> 'create', 'data-button'=> 'save']) !!}
</div>
{!! Form::close() !!}