<div class="form-group row">
  {{ Form::label('name', 'Exam Name', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Exam Name']) }}

    {!!invalid_feedback('name')!!}

  </div>

  {{ Form::label('', '', ['class' => 'col-md-2 form-control-label']) }}

  <div class='col-md-10' style="margin: 10px 0 0 0;">
    {{ Form::checkbox('have_class_test',1,null, array('id'=>'have_class_test')) }} Have Class Test
    </div>
</div>

@if(isset($exam->id))
  {{ Form::hidden('id', $exam->id) }}
@endif

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('hsc_result.exam.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>