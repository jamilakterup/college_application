<div class="form-group row">
  {{ Form::label('certificate_full_name', 'Certificate Full Name', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('certificate_full_name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Certificate Full Name']) }}

    {!!invalid_feedback('certificate_full_name')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('certificate_short_name', 'Certificate Short Name', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('certificate_short_name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Certificate Short Name']) }}

    {!!invalid_feedback('certificate_short_name')!!}

  </div>
</div>

@if(isset($requirement->id))
	{{ Form::hidden('id', $requirement->id) }}
@endif

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('admin.payslip_header.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>