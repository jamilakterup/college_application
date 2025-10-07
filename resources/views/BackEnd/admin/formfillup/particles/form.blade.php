<form class='form-horizontal' action="{{route('admin.formfillup.config.store')}}" data-form='postForm'>

  <div class="form-group">
    {{ Form::label('course', 'Course', ['class' => 'col-form-label']) }}

      {!! Form::select('course', student_course_list(), $course ?? null, ['class' => 'form-control form-control-sm']) !!}

      <div class='invalid-feedback'></div>
  </div>

  <div class="form-group">
    {{ Form::label('current_level', 'Current Level', ['class' => 'col-form-label']) }}

      {!! Form::select('current_level', selective_multiple_level(), $current_level ?? null, ['class' => 'form-control form-control-sm']) !!}

      <div class='invalid-feedback'></div>
  </div>

  <div class="form-group">
    {{ Form::label('session', 'Session', ['class' => 'col-form-label']) }}

      {!! Form::select('session', selective_multiple_session(), $session ?? null, ['class' => 'form-control form-control-sm selectize']) !!}

      <div class='invalid-feedback'></div>
  </div>

  <div class="form-group">
    {{ Form::label('exam_year', 'Exam Year', ['class' => 'col-form-label']) }}

      {!! Form::select('exam_year', selective_multiple_exam_year(), $exam_year ?? null, ['class' => 'form-control form-control-sm selectize']) !!}

      <div class='invalid-feedback'></div>
  </div>

  <div class="form-group">
    {{ Form::label('opening_date', 'Opening Date', ['class' => 'col-form-label']) }}

      {!! Form::text('opening_date', $opening_date ?? null, ['class' => 'form-control form-control-sm date', 'placeholder' => 'Opening Date', 'autocomplete' => 'off']) !!}

      <div class='invalid-feedback'></div>
  </div>

  <div class="form-group">
    {{ Form::label('clossing_date', 'Opening Date', ['class' => 'col-form-label']) }}

      {!! Form::text('clossing_date', $clossing_date ?? null, ['class' => 'form-control form-control-sm date', 'placeholder' => 'Opening Date', 'autocomplete' => 'off']) !!}

      <div class='invalid-feedback'></div>
  </div>

  <div class="form-group">
    {{ Form::label('open', 'Status', ['class' => 'col-form-label']) }}

      {!! Form::select('open', [''=> '<--Select Status-->', '1'=> 'Open', '0'=> 'Closed'], $open ?? null, ['class' => 'form-control form-control-sm']) !!}

      <div class='invalid-feedback'></div>
  </div>

  

  <div class="form-group">
      {!! Form::submit('Save Data', ['class'=> 'btn btn-primary','data-value'=> 'create', 'data-button'=>'save']) !!}
  </div>
</form>