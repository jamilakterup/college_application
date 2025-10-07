<div class="form-group row">
  {{ Form::label('session', 'Session', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::select('session', selective_multiple_session(), NULL, ['class' => 'form-control', 'id' => 'session']) }}

    {!!invalid_feedback('session')!!}

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
  {{ Form::label('exam_id', 'Exam Lists', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::select('exam_id', $exam_lists, NULL, ['class' => 'form-control', 'id' => 'exam_id']) }}

    {!!invalid_feedback('exam_id')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('exp_date', 'Expire Date', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('exp_date', NULL, ['class' => 'form-control date', 'placeholder' => 'Exp Date']) }}

    {!!invalid_feedback('exp_date')!!}

  </div>
</div>

@if(isset($exam->id))
  {{ Form::hidden('id', $exam->id) }}
@endif


<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('hsc_result.marks_input_config.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>