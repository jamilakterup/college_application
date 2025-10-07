<form class='form-horizontal' action="{{route('student.prblist.hsc.store', ['type'=> 'hsc_probable'])}}" data-form='postForm'>
  <div class="row">
      <div class="col-sm-6">
        <div class="form-group">
          {{ Form::label('student_id', 'ID', ['class' => 'col-form-label']) }}
            {{ Form::text('student_id', $student_id ?? null, ['class' => 'form-control', 'placeholder' => 'Enter ID']) }}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('class_roll', 'Student ID', ['class' => 'col-form-label']) }}
            {{ Form::text('class_roll', $class_roll ?? null, ['class' => 'form-control', 'placeholder' => 'Enter ID']) }}
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
            {!! Form::select('current_level', selective_multiple_hsc_level(), $current_level ?? null, ['class'=>'form-control selectize','placeholder', 'data-plugin'=> 'selectpicker']) !!}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('groups', 'Groups', ['class' => 'col-form-label']) }}
            {!! Form::select('groups', selective_multiple_faculty(), $groups ?? null, ['class'=>'form-control selectize','placeholder', 'data-plugin'=> 'selectpicker']) !!}
            <div class='invalid-feedback'></div>
        </div>

    </div>

    <div class="col-sm-6">
        
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
          {{ Form::label('papers', 'Papers Code', ['class' => 'col-form-label']) }}
            {{ Form::text('papers', $papers ?? null, ['class' => 'form-control', 'placeholder' => 'Enter Papers Code']) }}
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

        <div class="form-group">
          {{ Form::label('promotion_status', 'Promotion Status', ['class' => 'col-form-label']) }}
            {{ Form::select('promotion_status', getEnumValues('student_info_hsc_formfillup', 'promotion_status'),$promotion_status ?? null, ['class' => 'form-control show-tick selectize']) }}
            <div class='invalid-feedback'></div>
        </div>

      
    </div>
  </div>

  <div class="form-group">
      {!! Form::submit('Save Data', ['class'=> 'btn btn-primary','data-value'=> 'create', 'data-button'=> 'save']) !!}
  </div>
</form>