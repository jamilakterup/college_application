<div class='form-group row'>
	{{ Form::label('college_name', 'College Name', ['class' => 'col-sm-2  form-control-label']) }}

	<div class='col-sm-10'>
		{{ Form::text('college_name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter college name']) }}
		{!!invalid_feedback('college_name')!!}
	</div>
</div> <!-- end form-group -->

<div class='form-group row'>
	{{ Form::label('college_name_bengali', 'College Name (Bengali)', ['class' => 'col-sm-2  form-control-label']) }}

	<div class='col-sm-10'>
		{{ Form::text('college_name_bengali', NULL, ['class' => 'form-control', 'placeholder' => 'Enter college name in Bengali']) }}
		{!!invalid_feedback('college_name_bengali')!!}
	</div>
</div> <!-- end form-group -->

<div class='form-group row'>
	{{ Form::label('college_code', 'College Code', ['class' => 'col-sm-2  form-control-label']) }}

	<div class='col-sm-10'>
		{{ Form::text('college_code', NULL, ['class' => 'form-control', 'placeholder' => 'Enter college code']) }}
		{!!invalid_feedback('college_code')!!}
	</div>
</div> <!-- end form-group -->

<div class='form-group row'>
	{{ Form::label('biller_id', 'Biller Id (optional)', ['class' => 'col-sm-2  form-control-label']) }}

	<div class='col-sm-10'>
		{{ Form::text('biller_id', NULL, ['class' => 'form-control', 'placeholder' => 'Enter biller id']) }}
		{!!invalid_feedback('biller_id')!!}
	</div>
</div> <!-- end form-group -->

<div class='form-group row'>
	{{ Form::label('logo', 'Logo', ['class' => 'col-sm-2  form-control-label']) }}

	<div class='col-sm-10'>
		{{ Form::file('logo') }}
		{!!invalid_feedback('logo')!!}
	</div>
</div> <!-- end form-group -->

<div class='form-group row'>
	{{ Form::label('website', 'Website', ['class' => 'col-sm-2  form-control-label']) }}

	<div class='col-sm-10'>
		{{ Form::text('website', NULL, ['class' => 'form-control', 'placeholder' => 'Enter college website http://example.com/']) }}
		{!!invalid_feedback('website')!!}
	</div>
</div> <!-- end form-group -->

<div class='form-group row'>
	{{ Form::label('area_name', 'Area Name', ['class' => 'col-sm-2  form-control-label']) }}

	<div class='col-sm-10'>
		{{ Form::text('area_name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter area name']) }}
		{!!invalid_feedback('area_name')!!}
	</div>
</div> <!-- end form-group -->

<div class='form-group row'>
	{{ Form::label('area_name_bengali', 'Area Name (Bengali)', ['class' => 'col-sm-2  form-control-label']) }}

	<div class='col-sm-10'>
		{{ Form::text('area_name_bengali', NULL, ['class' => 'form-control', 'placeholder' => 'Enter area name in Bengali']) }}
		{!!invalid_feedback('area_name_bengali')!!}
	</div>
</div> <!-- end form-group -->

<div class='form-group row'>
	{{ Form::label('phone', 'Phone No.', ['class' => 'col-sm-2  form-control-label']) }}

	<div class='col-sm-10'>
		{{ Form::text('phone', NULL, ['class' => 'form-control', 'placeholder' => 'Enter college phone no']) }}
		{!!invalid_feedback('phone')!!}
	</div>
</div> <!-- end form-group -->

<div class='form-group row'>
	{{ Form::label('establish_date', 'Establish Date', ['class' => 'col-sm-2  form-control-label']) }}

	<div class='col-sm-10'>
		{{ Form::text('establish_date', NULL, ['class' => 'form-control date', 'placeholder' => 'Enter establish date']) }}
		{!!invalid_feedback('establish_date')!!}
	</div>
</div> <!-- end form-group -->

@if(isset($college->id))
	{{ Form::hidden('id', $college->id) }}
@endif

<div class='form-group row'>
	<div class='offset-sm-2 col-sm-10'>
		{{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
	</div>
</div>