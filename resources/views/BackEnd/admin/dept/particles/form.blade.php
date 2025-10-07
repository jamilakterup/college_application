<div class='form-group row'>
  {{ Form::label('faculty_id', 'Faculty', ['class' => 'col-md-2 form-control-label']) }}

  <div class='col-md-10'>
    {{ Form::select('faculty_id', App\Models\Faculty::pluck('faculty_name', 'id'), NULL, ['class' => 'form-control']) }}
    {!!invalid_feedback('faculty_id')!!}
  </div>
</div> <!-- end form-group row -->

<div class='form-group row'>
  {{ Form::label('dept_code', 'Department code', ['class' => 'col-md-2 form-control-label']) }}

  <div class='col-md-10'>
    {{ Form::text('dept_code', NULL, ['class' => 'form-control', 'placeholder' => 'Enter department code']) }}
    {!!invalid_feedback('dept_code')!!}
  </div>
</div> <!-- end form-group row -->

<div class='form-group row'>
  {{ Form::label('dept_name', 'Department name', ['class' => 'col-md-2 form-control-label']) }}

  <div class='col-md-10'>
    {{ Form::text('dept_name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter department name']) }}
    {!!invalid_feedback('dept_name')!!}
  </div>
</div> <!-- end form-group row -->

<div class='form-group row'>
  {{ Form::label('short_name', 'Short name', ['class' => 'col-md-2 form-control-label']) }}

  <div class='col-md-10'>
    {{ Form::text('short_name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter short name']) }}
    {!!invalid_feedback('short_name')!!}
  </div>
</div> <!-- end form-group row -->

<div class='form-group row'>
  {{ Form::label('seat', 'Total seat', ['class' => 'col-md-2 form-control-label']) }}

  <div class='col-md-10'>
    {{ Form::text('seat', NULL, ['class' => 'form-control', 'placeholder' => 'Enter total seat']) }}
    {!!invalid_feedback('seat')!!}
  </div>
</div> <!-- end form-group row -->

<div class='form-group row'>

  {{ Form::label('program', 'Programs Offered', ['class' => 'col-md-2 form-control-label']) }}

  <div class='col-md-10 checkbox-group'>

    @foreach($programs as $program)

      <?php

        $checked = '';

        if(isset($dept->id)) :  

          //check having the program in the department
          $has_program = App\Models\DeptProgram::whereDepartment_id($dept->id)->whereProgram_id($program->id)->where('status', 1)->get();                  
          if($has_program->count() > 0) :
            $checked = 'checked';
          endif;  

        endif;

      ?>

      @if($checked == '')
        <p>
          {{ Form::checkbox($program->id, $program->id) . ' ' . $program->name }}
        </p>
      @endif

      @if($checked == 'checked')
        <p>
          {{ Form::checkbox($program->id, $program->id, true) . ' ' . $program->name }}
        </p>
      @endif


    @endforeach

  </div>

</div> <!-- end form-group row -->

@if(isset($dept->id))
  {{ Form::hidden('id', $dept->id) }}
@endif

<div class='form-group row'>
  <div class='offset-md-2 col-md-10'>
    {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
  </div>
</div> <!-- end form-group row -->