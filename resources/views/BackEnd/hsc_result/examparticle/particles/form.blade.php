<div class="form-group row">
  {{ Form::label('name', 'Exam Particle Name', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Exam Particle Name']) }}

    {!!invalid_feedback('name')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('short_name', 'Exam Particle Short Name', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('short_name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Exam Particle Short Name']) }}

    {!!invalid_feedback('short_name')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('total', 'Total Mark', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('total', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Exam Particle Total Mark']) }}

    {!!invalid_feedback('total')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('pass', 'Pass Mark', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('pass', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Exam Particle Pass Mark']) }}

    {!!invalid_feedback('pass')!!}

  </div>
</div>

@if(isset($xmparticle->id))
  {{ Form::hidden('id', $xmparticle->id) }}
@endif

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('hsc_result.examparticle.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>