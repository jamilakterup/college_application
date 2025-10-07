<div class='form-group row'>
  {{ Form::label('department_id', 'Department', ['class' => 'col-sm-2 form-control-label']) }}

  <div class='col-sm-10'>
    {{ Form::select('department_id', App\Models\Department::pluck('dept_name', 'id'), NULL, ['class' => 'form-control']) }}
    {!!invalid_feedback('department_id')!!}
  </div>
</div> <!-- end form-group row -->

<div class='form-group row'>
  {{ Form::label('program_id', 'Program', ['class' => 'col-sm-2 form-control-label']) }}

  <div class='col-sm-10'>
    {{ Form::select('program_id', App\Models\Program::pluck('name', 'id'), NULL, ['class' => 'form-control']) }}
    {!!invalid_feedback('program_id')!!}
  </div>
</div> <!-- end form-group row -->

<div class='form-group row'>
  {{ Form::label('code', 'Course Code', ['class' => 'col-sm-2 form-control-label']) }}

  <div class='col-sm-10'>
    {{ Form::text('code', NULL, ['class' => 'form-control', 'placeholder' => 'Enter course code']) }}
    {!!invalid_feedback('code')!!}
  </div>
</div> <!-- end form-group row -->        

<div class='form-group row'>
  {{ Form::label('name', 'Course Name', ['class' => 'col-sm-2 form-control-label']) }}

  <div class='col-sm-10'>
    {{ Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter course name']) }}
    {!!invalid_feedback('name')!!}
  </div>
</div> <!-- end form-group row -->

<div class='form-group row'>
  {{ Form::label('mark', 'Total Mark', ['class' => 'col-sm-2 form-control-label']) }}

  <div class='col-sm-10'>
    {{ Form::text('mark', NULL, ['class' => 'form-control', 'placeholder' => 'Enter total mark']) }}
    {!!invalid_feedback('mark')!!}
  </div>
</div> <!-- end form-group row -->   

<div class='form-group row'>
  {{ Form::label('type', 'Course Type', ['class' => 'col-sm-2 form-control-label']) }}

  <div class='col-sm-10'>
    {{ Form::select('type', ['1' => 'Major', '0' => 'Optional'], NULL, ['class' => 'form-control']) }}
    {!!invalid_feedback('type')!!}
  </div>
</div> <!-- end form-group row -->   

<div class='form-group row'>
  {{ Form::label('level', 'Study Type', ['class' => 'col-sm-2 form-control-label']) }}

  <div class='col-sm-10'>
    <select name='level' class='form-control'>
      @foreach(range(1, 10) as $level)
        <option value={{ $level }}>{{ App\Libs\Study::level($level) . ' year' }}</option>
      @endforeach
    </select>

    {!!invalid_feedback('level')!!}
  </div>
</div> <!-- end form-group row -->    

<div class='form-group row'>
  {{ Form::label('session', 'Session', ['class' => 'col-sm-2 form-control-label']) }}

  <div class='col-sm-10'>
    <select name='session' class='form-control'>
      @foreach($sessions as $session)
        @if(isset($course->session))
          @if($course->session == $session)
            <option value="{{ $session }}" selected='selected'>{{ $session }}</option>
          @else
            <option value="{{ $session }}">{{ $session }}</option>
          @endif
        @else
          <option value="{{ $session }}">{{ $session }}</option>
        @endif
      @endforeach
    </select>
    {!!invalid_feedback('session')!!}
  </div>
</div> <!-- end form-group row --> 

@if(isset($course->id))
  {{ Form::hidden('id', $course->id) }}
@endif

<div class='form-group row'>
  <div class='offset-sm-2 col-sm-10'>
    {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
  </div>
</div> <!-- end form-group row -->   