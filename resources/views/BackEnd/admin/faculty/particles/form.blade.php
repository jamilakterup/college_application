<div class="form-group row">
  {{ Form::label('faculty_code', 'Faculty code', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('faculty_code', NULL, ['class' => 'form-control', 'placeholder' => 'Enter faculty code']) }}

    {!!invalid_feedback('faculty_code')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('faculty_name', 'Faculty name', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('faculty_name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter faculty name']) }}

    {!!invalid_feedback('faculty_name')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('short_name', 'Short name', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('short_name', NULL, ['class' => 'form-control', 'placeholder' => 'Short name']) }}

    {!!invalid_feedback('short_name')!!}

  </div>
</div>

@if(isset($faculty->id))
	{{ Form::hidden('id', $faculty->id) }}
@endif

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('admin.faculty.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>