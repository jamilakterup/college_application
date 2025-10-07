<div class="form-group row">
  {{ Form::label('course', 'Course', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {!! Form::select('course', [''=>'--Select One--','hsc'=> 'HSC', 'honours'=> 'Honours', 'masters_1' => 'Masters Previous', 'masters_2'=> 'Masters Final', 'degree' => 'Degree Pass'], null, ['class'=>'form-control']) !!}

    {!!invalid_feedback('course')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('session', 'Session', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {!! Form::select('session', selective_multiple_session(), null, ['class'=>'form-control']) !!}

    {!!invalid_feedback('session')!!}
  </div>
</div>

<div class="form-group row">
  {{ Form::label('faculty', 'Faculty', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {!! Form::select('faculty', create_option_array('faculties', 'faculty_code', 'faculty_name'), null, ['class'=>'form-control','placeholder']) !!}

    {!!invalid_feedback('faculty')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('dept_name', 'Department', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {!! Form::select('dept_name', create_option_array('departments', 'dept_name', 'dept_name'), null, ['class'=>'form-control select2','placeholder']) !!}

    {!!invalid_feedback('dept_name')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('start_digit', 'Start Digit', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('start_digit', '1', ['class' => 'form-control', 'placeholder' => 'Enter Start Digit']) }}

    {!!invalid_feedback('start_digit')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('end_digit', 'End Digit', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('end_digit', '999', ['class' => 'form-control', 'placeholder' => 'Enter End Digit']) }}

    {!!invalid_feedback('end_digit')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('last_digit_used', 'Last Digit Used', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('last_digit_used', '0', ['class' => 'form-control', 'placeholder' => 'Last Digit']) }}

    {!!invalid_feedback('last_digit_used')!!}

  </div>
</div>

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('admin.id_roll.create') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>