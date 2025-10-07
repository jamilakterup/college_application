<form class='form-horizontal' action="{{route('student.meritlist.honours.store', ['type'=> 'honours_probable'])}}" data-form='postForm'>
    <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            {{ Form::label('name', 'Name', ['class' => 'col-form-label']) }}
              {{ Form::text('name', $name ?? null, ['class' => 'form-control', 'placeholder' => 'Enter Student Name']) }}
              <div class='invalid-feedback'></div>
          </div>

          <div class="form-group">
            {{ Form::label('admission_roll', 'Admission Roll', ['class' => 'col-form-label']) }}
              {{ Form::text('admission_roll', $admission_roll ?? null, ['class' => 'form-control', 'placeholder' => 'Enter Admission Roll']) }}
              <div class='invalid-feedback'></div>
          </div>

          <div class="form-group">
            {{ Form::label('faculty', 'Faculty', ['class' => 'col-form-label']) }}
              {!! Form::select('faculty', create_option_array('faculties', 'faculty_code', 'faculty_name'), $faculty ?? null, ['class'=>'form-control selectize','placeholder', 'data-plugin'=> 'selectpicker']) !!}
              <div class='invalid-feedback'></div>
          </div>
  
          <div class="form-group">
            {{ Form::label('subject', 'Department', ['class' => 'col-form-label']) }}
              {!! Form::select('subject', filter_empty_array(selective_multiple_subject()), $subject ?? null, ['class'=>'form-control selectize','placeholder', 'data-plugin'=> 'selectpicker']) !!}
              <div class='invalid-feedback'></div>
          </div>

          <div class="form-group">
            {{ Form::label('merit_status', 'Merit Status', ['class' => 'col-form-label']) }}
              {!! Form::text('merit_status', $merit_status ?? null, ['class'=>'form-control selectize','placeholder'=> 'Enter Merit Status', 'data-plugin'=> 'selectpicker']) !!}
              <div class='invalid-feedback'></div>
          </div>
      </div>
  
      <div class="col-sm-6">

        <div class="form-group">
          {{ Form::label('merit_pos', 'Merit Position', ['class' => 'col-form-label']) }}
            {!! Form::text('merit_pos', $merit_pos ?? null, ['class'=>'form-control selectize','placeholder'=> 'Enter Merit Position', 'data-plugin'=> 'selectpicker']) !!}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('session', 'Session', ['class' => 'col-form-label']) }}
            {!! Form::select('session', selective_multiple_session(), $session ?? null, ['class'=>'form-control selectize', 'data-placeholder'=> '<--Session-->']) !!}
            <div class='invalid-feedback'></div>
        </div>
        
        <div class="form-group">
            {{ Form::label('password', 'Password', ['class' => 'col-form-label']) }}
              {{ Form::text('password', $password ?? null, ['class' => 'form-control form-control-sm', 'placeholder' => 'Enter Password', 'id'=> 'password']) }}
              <div class='invalid-feedback'></div>
          </div>
  
          
          <div class="form-group">
            {{ Form::label('admission_status', 'Admission Status', ['class' => 'col-form-label']) }}
            {{ Form::select('admission_status',selective_admission_status(),$admission_status ?? null, ['class' => 'form-control show-tick']) }}
            <div class='invalid-feedback'></div>
          </div>
        
      </div>
    </div>
  
    <div class="form-group">
        {!! Form::submit('Save Data', ['class'=> 'btn btn-primary','data-value'=> 'create', 'data-button'=> 'save']) !!}
    </div>
  </form>