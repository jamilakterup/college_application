<div class="form-group row">
    {{ Form::label('session', 'Session', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

      {{ Form::select('session', selective_multiple_session(),Null, ['class' => 'form-control']) }}
  
      {!!invalid_feedback('session')!!}
  
    </div>
</div>

<div class="form-group row">
  {{ Form::label('level', 'Level', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::select('level', $level_list, $class->level ?? null, ['class' => 'form-control year']) }}

    {!!invalid_feedback('level')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('exam_id', 'Exam', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::select('exam_id', create_option_array('exams', 'id', 'name', 'Exam'), NULL, ['class' => 'form-control exam', 'id' => 'exam']) }}

    {!!invalid_feedback('exam_id')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('exam_year', 'Exam Year', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::select('exam_year', selective_multiple_exam_year(), NULL, ['class' => 'form-control', 'id' => 'exam_year']) }}

    {!!invalid_feedback('exam_year')!!}

  </div>
</div>

<div class="form-group row">
    {{ Form::label('open', 'Status', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
      {{ Form::select('open', $status_list, null , ['class' => 'form-control']) }}
  
      {!!invalid_feedback('open')!!}
  
    </div>
</div>

@if(isset($class->id))
  {{ Form::hidden('id', $class->id) }}
@endif

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('hsc_result.result_publish.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>