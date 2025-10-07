<form class='form-horizontal' action="{{route('admin.payslip_header.store')}}" data-form='postForm'>
  <div class="row">
      <div class="col-sm-6">
        <div class="form-group">
          {{ Form::label('title', 'Title', ['class' => 'col-form-label']) }}
            {{ Form::text('title', $title ?? null, ['class' => 'form-control', 'placeholder' => 'Enter payslip header title']) }}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('code', 'Code', ['class' => 'col-form-label']) }}
            {{ Form::text('code', $code ?? null, ['class' => 'form-control', 'placeholder' => 'Enter Code']) }}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('start_date', 'Start Date', ['class' => 'col-form-label']) }}
            {{ Form::text('start_date', $start_date ?? null, ['class' => 'form-control date', 'placeholder' => 'Start Date', 'autocomplete'=> 'off']) }}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('end_date', 'End Date', ['class' => 'col-form-label']) }}
            {{ Form::text('end_date', $end_date ?? null, ['class' => 'form-control date', 'placeholder' => 'End Date','autocomplete'=> 'off']) }}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('type', 'Type', ['class' => 'col-form-label']) }}
            {{ Form::select('type', selective_multiple_type(),$header_type ?? null, ['class' => 'form-control show-tick']) }}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('pro_group', 'Group', ['class' => 'col-form-label']) }}
            {!! Form::select('pro_group', selective_multiple_group(), $pro_group ?? null, ['class'=>'form-control show-tick', 'data-plugin'=> 'selectpicker']) !!}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('level', 'Level Year', ['class' => 'col-form-label']) }}
            {!! Form::select('level', selective_multiple_level(), $level ?? null, ['class'=>'form-control show-tick', 'data-plugin'=> 'selectpicker']) !!}
            <div class='invalid-feedback'></div>
        </div>

    </div>

    <div class="col-sm-6">
      <div class="form-group">
        {{ Form::label('group_dept', 'Faculty', ['class' => 'col-form-label']) }}
          {!! Form::select('group_dept[]', create_option_array('faculties', 'faculty_code', 'faculty_name'), $faculty ?? null, ['class'=>'form-control selectize','placeholder', 'data-plugin'=> 'selectpicker', 'multiple'=>true]) !!}
          <div class='invalid-feedback'></div>
      </div>

      <div class="form-group">
        {{ Form::label('session', 'Session', ['class' => 'col-form-label']) }}
          {!! Form::select('session[]', selective_multiple_session(), $session ?? null, ['class'=>'form-control selectize', 'multiple'=> 'multiple', 'data-placeholder'=> '<--Session-->']) !!}
          <div class='invalid-feedback'></div>
      </div>

      <div class="form-group">
        {{ Form::label('exam_year', 'Exam year', ['class' => 'col-form-label']) }}
          {!! Form::select('exam_year', selective_multiple_exam_year(), $exam_year ?? null, ['class'=>'form-control show-tick', 'data-plugin'=> 'selectpicker']) !!}
          <div class='invalid-feedback'></div>
      </div>

      <div class="form-group">
        {{ Form::label('subject', 'Department', ['class' => 'col-form-label']) }}
          {{ Form::select('subject[]',selective_multiple_subject(), $subject ?? null, ['class' => 'form-control form-control-sm selectize', 'data-placeholder' => '<--Subject-->', 'id'=> 'subject', 'multiple'=> true]) }}

          <div class='invalid-feedback'></div>
      </div>

      <div class="form-group">
        {{ Form::label('total_papers', 'Total Papers', ['class' => 'col-form-label']) }}
          {{ Form::text('total_papers', $total_papers ?? null, ['class' => 'form-control', 'placeholder' => 'Enter Total Papers']) }}
          <div class='invalid-feedback'></div>
      </div>


      <div class="form-group">
        {{ Form::label('student_type', 'Student Type', ['class' => 'col-form-label']) }}
          {!! Form::select('student_type', selective_student_type(), $student_type ?? null, ['class'=>'form-control']) !!}
          <div class='invalid-feedback'></div>
      </div>

      <div class="form-group">
        {{ Form::label('formfillup_type', 'Form Fillup Type', ['class' => 'col-form-label']) }}
          {!! Form::select('formfillup_type[]', selective_formfillup_type(), $formfillup_type ?? null, ['class'=>'form-control selectize', 'multiple'=> true]) !!}
          <div class='invalid-feedback'></div>
      </div>
    </div>
  </div>

  <div class="form-group">
      {!! Form::submit('Save Data', ['class'=> 'btn btn-primary','data-value'=> 'create', 'data-button'=> 'save']) !!}
  </div>
</form>