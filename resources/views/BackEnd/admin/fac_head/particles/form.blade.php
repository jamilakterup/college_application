<div class="form-group row">
  {{ Form::label('name', 'Name', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('name', NULL, ['class' => 'form-control','placeholder' => 'Enter faculty head name']) }}

    {!!invalid_feedback('name')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('faculty_id', 'Faculty', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::select('faculty_id', App\Models\Faculty::pluck('faculty_name', 'id'), NULL, ['class' => 'form-control']) }}

    {!!invalid_feedback('faculty_id')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('status', 'Status', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::select('status', ['1' => 'active', '0' => 'inactive'], NULL, ['class' => 'form-control']) }}

    {!!invalid_feedback('status')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('starting_date', 'Starting date', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('starting_date', NULL, ['class' => 'form-control date', 'placeholder' => 'Starting date', 'autocomplete'=> 'off']) }}

    {!!invalid_feedback('starting_date')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('end_date', 'End Date', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('end_date', NULL, ['class' => 'form-control date', 'placeholder' => 'End Date','autocomplete'=> 'off']) }}

    {!!invalid_feedback('end_date')!!}

  </div>
</div>

@if(isset($faculty_head->id))
	{{ Form::hidden('id', $faculty_head->id) }}
@endif

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('admin.fac_head.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>