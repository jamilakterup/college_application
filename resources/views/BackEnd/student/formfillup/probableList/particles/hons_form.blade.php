<form class='form-horizontal' action="{{route('student.prblist.honours.store', ['type'=> 'honours_probable'])}}" data-form='postForm'>
    <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            {{ Form::label('student_id', 'Student ID', ['class' => 'col-form-label']) }}
              {{ Form::text('student_id', $student_id ?? null, ['class' => 'form-control', 'placeholder' => 'Enter ID']) }}
              <div class='invalid-feedback'></div>
          </div>

          <div class="form-group">
            {{ Form::label('name', 'Name', ['class' => 'col-form-label']) }}
              {{ Form::text('name', $name ?? null, ['class' => 'form-control', 'placeholder' => 'Enter Student Name']) }}
              <div class='invalid-feedback'></div>
          </div>

          <div class="form-group">
            {{ Form::label('session', 'Session', ['class' => 'col-form-label']) }}
              {!! Form::select('session', selective_multiple_session(), $session ?? null, ['class'=>'form-control selectize', 'data-placeholder'=> '<--Session-->']) !!}
              <div class='invalid-feedback'></div>
          </div>

          <div class="form-group">
            {{ Form::label('current_level', 'Current Level', ['class' => 'col-form-label']) }}
              {!! Form::select('current_level', selective_multiple_honours_level(), $current_level ?? null, ['class'=>'form-control selectize','placeholder', 'data-plugin'=> 'selectpicker']) !!}
              <div class='invalid-feedback'></div>
          </div>
          
        <div class="form-group">
            {{ Form::label('faculty_name', 'Faculty', ['class' => 'col-form-label']) }}
              {!! Form::select('faculty_name', create_option_array('faculties', 'faculty_code', 'faculty_name'), $faculty_name ?? null, ['class'=>'form-control selectize','placeholder', 'data-plugin'=> 'selectpicker']) !!}
              <div class='invalid-feedback'></div>
          </div>
      </div>
  
      <div class="col-sm-6">

  
        <div class="form-group">
            {{ Form::label('dept_name', 'Department', ['class' => 'col-form-label']) }}
              {{ Form::select('dept_name',selective_multiple_subject(), $dept_name ?? null, ['class' => 'form-control form-control-sm selectize', 'data-placeholder' => '<--Department-->', 'id'=> 'dept_name']) }}
    
              <div class='invalid-feedback'></div>
          </div>
  
          <div class="form-group">
            {{ Form::label('type', 'Registration Type', ['class' => 'col-form-label']) }}
            {{ Form::select('registration_type', selective_formfillup_type(),$registration_type ?? null, ['class' => 'form-control show-tick']) }}
            <div class='invalid-feedback'></div>
          </div>
          
          <div class="form-group">
            {{ Form::label('student_type', 'Student Type', ['class' => 'col-form-label']) }}
              {{ Form::select('student_type', selective_student_type(),$student_type ?? null, ['class' => 'form-control show-tick selectize']) }}
              <div class='invalid-feedback'></div>
          </div>

          <div class="form-group">
            {{ Form::label('total_amount', 'Total Amount', ['class' => 'col-form-label']) }}
              {{ Form::text('total_amount', $data->total_amount ?? null, ['class' => 'form-control', 'placeholder' => 'Total Amount']) }}
              <div class='invalid-feedback'></div>
          </div>

          <div class="form-group">
            {{ Form::label('status', 'Status', ['class' => 'col-form-label']) }}
              {{ Form::select('status', [''=>  '>--Select Status--<', '1'=> 'Active', '0'=> 'Inactive'],$status ?? null, ['class' => 'form-control show-tick selectize']) }}
              <div class='invalid-feedback'></div>
          </div>
  
        
      </div>
    </div>
  
    <div class="form-group">
        {!! Form::submit('Save Data', ['class'=> 'btn btn-primary','data-value'=> 'create', 'data-button'=> 'save']) !!}
    </div>
  </form>