<div class='form-group row'>
  {{ Form::label('code', 'Program Code', ['class' => 'col-sm-2 form-control-label']) }}

  <div class='col-sm-10'>
    {{ Form::text('code', NULL, ['class' => 'form-control', 'placeholder' => 'Enter program code']) }}
    {!!invalid_feedback('code')!!}
  </div>
</div> <!-- end form-group row -->

<div class='form-group row'>
  {{ Form::label('name', 'Program Name', ['class' => 'col-sm-2 form-control-label']) }}

  <div class='col-sm-10'>
    {{ Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter program name']) }}
    {!!invalid_feedback('name')!!}
  </div>
</div> <!-- end form-group row -->

<div class='form-group row'>
  {{ Form::label('short_name', 'Short Name', ['class' => 'col-sm-2 form-control-label']) }}

  <div class='col-sm-10'>
    {{ Form::text('short_name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter short name']) }}
    {!!invalid_feedback('short_name')!!}
  </div>
</div> <!-- end form-group row -->

<div class='form-group row'>
  {{ Form::label('timeline', 'Timeline (year)', ['class' => 'col-sm-2 form-control-label']) }}

  <div class='col-sm-10'>
    {{ Form::text('timeline', NULL, ['class' => 'form-control', 'placeholder' => 'Enter timeline']) }}
    {!!invalid_feedback('timeline')!!}
  </div>
</div> <!-- end form-group row -->

@if(isset($program->id))
  {{ Form::hidden('id', $program->id) }}
@endif

<div class='form-group row'>
  <div class='offset-sm-2 col-sm-10'>
    {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
  </div>
</div> <!-- end form-group row -->