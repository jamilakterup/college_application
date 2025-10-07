<div class="form-group row">
  {{ Form::label('title', 'PaySlip Header Title', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('title', NULL, ['class' => 'form-control', 'placeholder' => 'Enter payslip header title']) }}

    {!!invalid_feedback('title')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('code', 'Code', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('code', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Code']) }}

    {!!invalid_feedback('code')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('start_date', 'Start Date', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('start_date', NULL, ['class' => 'form-control date', 'placeholder' => 'Start Date', 'autocomplete'=> 'off']) }}

    {!!invalid_feedback('start_date')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('end_date', 'End Date', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('end_date', NULL, ['class' => 'form-control date', 'placeholder' => 'End Date','autocomplete'=> 'off']) }}

    {!!invalid_feedback('end_date')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('type', 'Type', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::select('type', selective_multiple_type(),null, ['class' => 'form-control show-tick','data-plugin'=> 'selectpicker']) }}

    {!!invalid_feedback('type')!!}

  </div>
</div>


<div class="form-group row">
  {{ Form::label('pro_group', 'Level', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::select('pro_group', selective_multiple_group(), null, ['class' => 'form-control show-tick','data-plugin'=> 'selectpicker']) }}

    {!!invalid_feedback('pro_group')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('level', 'Level Year', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {!! Form::select('level', selective_multiple_level(), null, ['class'=>'form-control show-tick', 'data-plugin'=> 'selectpicker']) !!}

    {!!invalid_feedback('level')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('group_dept', 'Faculty', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {!! Form::select('group_dept', create_option_array('faculties', 'faculty_code', 'faculty_name'), null, ['class'=>'form-control show-tick','placeholder', 'data-plugin'=> 'selectpicker']) !!}

    {!!invalid_feedback('group_dept')!!}

  </div>
</div>


<div class="form-group row">
  {{ Form::label('session', 'Session', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {!! Form::select('session[]', selective_multiple_session(), $selected_session, ['class'=>'form-control select2', 'multiple'=> 'multiple']) !!}

    {!!invalid_feedback('session')!!}
  </div>
</div>

<div class="form-group row">
  {{ Form::label('exam_year', 'Exam year', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {!! Form::select('exam_year', selective_multiple_exam_year(), null, ['class'=>'form-control show-tick', 'data-plugin'=> 'selectpicker']) !!}

    {!!invalid_feedback('exam_year')!!}
  </div>
</div>

<div class="form-group row">
  {{ Form::label('subject', 'Subject', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {!! Form::select('subject[]', selective_multiple_subject(), $selected_subject, ['class'=>'form-control select2','multiple' => 'multiple']) !!}

    {!!invalid_feedback('subject')!!}
  </div>
</div>

<div class="form-group row">
  {{ Form::label('formfillup_type', 'Form Fillup Type', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {!! Form::select('formfillup_type', selective_formfillup_type(), null, ['class'=>'form-control']) !!}

    {!!invalid_feedback('formfillup_type')!!}
  </div>
</div>

@if(isset($payslip_header->id))
	{{ Form::hidden('id', $payslip_header->id) }}
@endif

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('admin.payslip_header.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>