<div class="form-group row">
  {{ Form::label('full_name', 'Full Name', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('full_name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Full Name']) }}

    {!!invalid_feedback('full_name')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('username', 'Username', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('username', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Username']) }}

    {!!invalid_feedback('username')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('email', 'Email', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('email', NULL, ['class' => 'form-control', 'placeholder' => 'Email']) }}

    {!!invalid_feedback('email')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('roles', 'Role', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    @if(isset($user) && $user !='')
      <select name="roles[]" id="roles" class="form-control select2" multiple>
          @foreach ($roles as $role)
              <option value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
          @endforeach
      </select>
    @else
      {{ Form::select('roles[]', $roles, NULL, ['class' => 'form-control select2', 'multiple' => true]) }}
    @endif

    {!!invalid_feedback('roles')!!}

  </div>
</div>

@if($user=='')
  <div class='form-group row'>
    {{ Form::label('password', 'Password', ['class' => 'col-md-2 form-control-label']) }}

    <div class='col-md-10'>
      {{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Enter user password']) }}
      {!!invalid_feedback('password')!!}
    </div>
  </div> <!-- end form-group -->

  <div class='form-group row'>
    {{ Form::label('password_confirmation', 'Password Confirmation', ['class' => 'col-md-2  form-control-label']) }}

    <div class='col-md-10'>
      {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirm user password']) }}
      {!!invalid_feedback('password_confirmation')!!}
    </div>
  </div> <!-- end form-group -->
@endif

<div class='form-group row'>
    {{ Form::label('level_year', 'Level Year', ['class' => 'col-md-2  form-control-label']) }}

    <div class='col-md-10'>
      {{ Form::select('level_year[]',selective_multiple_level(), $level_year, ['class' => 'form-control select2', 'multiple' => true]) }}
      {!!invalid_feedback('level_year')!!}
    </div>
</div> <!-- end form-group -->

<div class='form-group row'>
    {{ Form::label('exam_year', 'Exam Year', ['class' => 'col-md-2  form-control-label']) }}
    <div class='col-md-10'>
      {{ Form::select('exam_year[]',selective_multiple_exam_year(), $exam_year, ['class' => 'form-control select2', 'multiple' => true]) }}
      {!!invalid_feedback('exam_year')!!}
    </div>
</div> <!-- end form-group -->

<div class='form-group row'>
    {{ Form::label('session', 'Session', ['class' => 'col-md-2  form-control-label']) }}

    <div class='col-md-10'>
      {{ Form::select('session[]',selective_multiple_session(), $session, ['class' => 'form-control select2', 'multiple' => true]) }}
      {!!invalid_feedback('session')!!}
    </div>
</div> <!-- end form-group -->

<div class='form-group row'>
    {{ Form::label('hsc_group', 'HSC Group', ['class' => 'col-md-2  form-control-label']) }}

    <div class='col-md-10'>
      {{ Form::select('hsc_group[]',selective_hsc_groups(), $hsc_group, ['class' => 'form-control select2', 'multiple' => true]) }}
      {!!invalid_feedback('hsc_group')!!}
    </div>
</div> <!-- end form-group -->

<div class='form-group row'>
    {{ Form::label('faculty', 'Faculty', ['class' => 'col-md-2  form-control-label']) }}

    <div class='col-md-10'>
      {{ Form::select('faculty[]',selective_multiple_faculty(), $faculty, ['class' => 'form-control select2', 'multiple' => true]) }}
      {!!invalid_feedback('faculty')!!}
    </div>
</div> <!-- end form-group -->

<div class='form-group row'>
    {{ Form::label('department', 'Department', ['class' => 'col-md-2  form-control-label']) }}

    <div class='col-md-10'>
      {{ Form::select('department[]',selective_departments(), $department, ['class' => 'form-control select2', 'multiple' => true]) }}
      {!!invalid_feedback('department')!!}
    </div>
</div> <!-- end form-group -->
<div class='form-group row'>
    {{ Form::label('hsc_subject', 'HSC Subject', ['class' => 'col-md-2  form-control-label']) }}

    <div class="col-md-10">
      <select name="hsc_subject[]" id="hsc_subject" class="form-control select2" multiple>
          <option value="">Select Subject</option>
          @foreach(\App\Models\Subject::all() as $val)
              <option value="{{ $val->name }}" {{ is_array(old('hsc_subject', $hsc_subject ?? [])) && in_array($val->name, old('hsc_subject', $hsc_subject ?? [])) ? 'selected' : '' }}>
                  {{ $val->name }} ({{$val->code}})
              </option>
          @endforeach
      </select>

    </div>
</div> <!-- end form-group -->

@if(isset($user->id))
  {{ Form::hidden('id', $user->id) }}
@endif

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('admin.faculty.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>