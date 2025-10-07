<div class="form-group row">
    {{ Form::label('teacher_id', 'Teacher ID', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

        {{ Form::text('teacher_id', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Teacher ID']) }}

        {!!invalid_feedback('teacher_id')!!}

    </div>
</div>

<div class="form-group row">
    {{ Form::label('name', 'Name', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

        {{ Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Name']) }}

        {!!invalid_feedback('name')!!}

    </div>
</div>

<div class="form-group row">
    {{ Form::label('personal_mobile', 'Mobile', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

        {{ Form::text('personal_mobile', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Mobile']) }}

        {!!invalid_feedback('personal_mobile')!!}

    </div>
</div>

<div class="form-group row">
    {{ Form::label('blood_group', 'Blood Group', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::select('blood_group', selective_blood_lists() ,null, ['class' => 'form-control']) }}
        {!!invalid_feedback('blood_group')!!}

    </div>
</div>

<div class="form-group row">
    {{ Form::label('district', 'District', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

        {{ Form::select('district', create_option_array('district_thana', 'district', 'district', 'District'),null, ['class' => 'form-control']) }}

        {!!invalid_feedback('district')!!}

    </div>
</div>

<div class="form-group row">
    {{ Form::label('incoming_college', 'Incoming College', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

        {{ Form::text('incoming_college', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Incoming College']) }}

        {!!invalid_feedback('incoming_college')!!}

    </div>
</div>

<div class="form-group row">
    {{ Form::label('reference_no', 'Reference No.', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

        {{ Form::text('reference_no', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Reference No.']) }}

        {!!invalid_feedback('reference_no')!!}

    </div>
</div>

<div class="form-group row">
    {{ Form::label('department', 'Department', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

        {{ Form::select('department', create_option_array('departments', 'dept_name', 'dept_name', 'Department'),null, ['class' => 'form-control']) }}

        {!!invalid_feedback('department')!!}

    </div>
</div>

<div class="form-group row">
    {{ Form::label('position', 'Position', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

        {{ Form::select('position', create_option_array('designation', 'name', 'name', 'Position'),null, ['class' => 'form-control']) }}

        {!!invalid_feedback('position')!!}

    </div>
</div>

<div class="form-group row">
    {{ Form::label('joining_date', 'Joining Date', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

        {{ Form::text('joining_date', NULL, ['class' => 'form-control datepickr', 'placeholder' => 'Enter Join Date']) }}

        {!!invalid_feedback('joining_date')!!}

    </div>
</div>

<div class="form-group row">
    <div class="col-md-10 offset-md-2">
      {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
      <a href="{{ route('teacher.index') }}" class="btn btn-warning btn-outline">Back</a>
    </div>
</div>