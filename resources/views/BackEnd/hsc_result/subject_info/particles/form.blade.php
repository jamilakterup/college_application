<div class="form-group row">
  {{ Form::label('student_id_label', 'Student ID', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('student_id', NULL, ['class' => 'form-control', 'readonly' => 'true']) }}

    {!!invalid_feedback('student_id')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('current_level', 'Current Level', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('current_level', $subject_info->current_level, ['class' => 'form-control', 'readonly' => 'true']) }}

    {!!invalid_feedback('current_level')!!}

  </div>
</div>

<div class="form-group row">
  <?php $compulsary='101,107,275';?>
  {{ Form::label('compulsary_label', 'Compulsary Subject', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('compulsary_subject', $compulsary, ['class' => 'form-control', 'readonly' => 'true']) }}

    {!!invalid_feedback('compulsary_subject')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('compulsary_label', 'Selective Subject1', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('sel_subject1', $subject_info->sub4->code, ['class' => 'form-control']) }}

    {!!invalid_feedback('sel_subject1')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('compulsary_label', 'Selective Subject2', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('sel_subject2', $subject_info->sub5->code, ['class' => 'form-control']) }}

    {!!invalid_feedback('sel_subject2')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('compulsary_label', 'Selective Subject3', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('sel_subject3', $subject_info->sub6->code, ['class' => 'form-control']) }}

    {!!invalid_feedback('sel_subject3')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('fourth_label', 'Fourth Subject', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('fourth_subject', $subject_info->fourth->code, ['class' => 'form-control']) }}

    {!!invalid_feedback('fourth_subject')!!}

  </div>
</div>

@if(isset($subject_info->id))
  {{ Form::hidden('id', $subject_info->id) }}
@endif

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('hsc_result.subject_info.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>