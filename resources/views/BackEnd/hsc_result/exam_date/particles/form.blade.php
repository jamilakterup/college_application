<div class="form-group row">
    {{ Form::label('session', 'Session', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
  
      {{ Form::select('session', selective_multiple_session(), NULL, ['class' => 'form-control', 'id' => 'session']) }}
  
      {!!invalid_feedback('session')!!}
  
    </div>
  </div>

  <div class="form-group row">
    {{ Form::label('group', 'Group', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
  
      {{ Form::select('group', $group_lists, NULL, ['class' => 'form-control group']) }}
  
      {!!invalid_feedback('group')!!}
  
    </div>
  </div>
  
  <div class="form-group row">
    {{ Form::label('current_level', 'Current Level', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
  
      {{ Form::select('current_level', $current_yr_lists, NULL, ['class' => 'form-control year']) }}
  
      {!!invalid_feedback('current_level')!!}
  
    </div>
  </div>
  
  <div class="form-group row">
    {{ Form::label('exam_id', 'Exam', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
  
      {{ Form::select('exam_id', $exam_lists, NULL, ['class' => 'form-control exam', 'id' => 'exam']) }}
  
      {!!invalid_feedback('exam_id')!!}
  
    </div>
  </div>

  <div class="form-group row">
    {{ Form::label('exam_year', 'Exam Year', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
  
      {{ Form::select('exam_year', selective_multiple_exam_year(), NULL, ['class' => 'form-control exam', 'id' => 'exam_year']) }}
  
      {!!invalid_feedback('exam_year')!!}
  
    </div>
  </div>

  <div class="form-group row">
    <div class="col-md-10 offset-md-2">
      {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
      <a href="{{ route('hsc_result.exam_date.index') }}" class="btn btn-warning btn-outline">Back</a>
    </div>
  </div>