<div class="form-group row">
  {{ Form::label('name', 'Subject Name', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Subject Name']) }}

    {!!invalid_feedback('name')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('code', 'Subject Code', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('code', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Subject Code']) }}

    {!!invalid_feedback('code')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('name', 'Can be taken as fourth subject?', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">
    <div class="border p-2 checkbox-group">
      <label class="checkbox-inline">
        <p>
          {{ Form::radio('optional', 1) }} Yes
        </p>

        <p>
          {{ Form::radio('optional', 0) }} No
        </p>
      </label>
    </div>

  </div>
</div>

@if(isset($subject->id))
  {{ Form::hidden('id', $subject->id) }}
@endif

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('hsc_result.subject.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>