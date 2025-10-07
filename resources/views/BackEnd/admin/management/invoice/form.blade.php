<form class='form-horizontal' action="{{route('admin.invoice.store')}}" data-form='postForm'>
  <div class="row">
      <div class="col-sm-6">
        <div class="form-group">
          {{ Form::label('name', 'Name', ['class' => 'col-form-label']) }}
            {{ Form::text('name', $name ?? null, ['class' => 'form-control', 'placeholder' => 'Enter name']) }}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('roll', 'Roll', ['class' => 'col-form-label']) }}
            {{ Form::text('roll', $roll ?? null, ['class' => 'form-control', 'placeholder' => 'Enter Roll Number']) }}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('type', 'Invoice Type', ['class' => 'col-form-label']) }}
            {{ Form::select('type', getEnumValues('invoices', 'type') , $type ?? null, ['class' => 'form-control selectize', 'data-placeholder' => '>--Select Invoice Type--<']) }}
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
          {{ Form::label('level', 'Level Year', ['class' => 'col-form-label']) }}
            {!! Form::select('level', selective_multiple_level(), $level ?? null, ['class'=>'form-control show-tick', 'data-plugin'=> 'selectpicker']) !!}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
          {{ Form::label('session', 'Session', ['class' => 'col-form-label']) }}
            {!! Form::select('session', selective_multiple_session(), $session ?? null, ['class'=>'form-control selectize', 'data-placeholder'=> '<--Session-->']) !!}
            <div class='invalid-feedback'></div>
        </div>

        <div class="form-group">
        {{ Form::label('exam_year', 'Exam year', ['class' => 'col-form-label']) }}
          {!! Form::select('exam_year', selective_multiple_exam_year(), $exam_year ?? null, ['class'=>'form-control show-tick', 'data-plugin'=> 'selectpicker']) !!}
          <div class='invalid-feedback'></div>
      </div>

    </div>

    <div class="col-sm-6">
      <div class="form-group">
        {{ Form::label('pro_group', 'Pro Group', ['class' => 'col-form-label']) }}
          {!! Form::select('pro_group', create_option_array('faculties', 'faculty_code', 'faculty_name'), $pro_group ?? null, ['class'=>'form-control selectize','placeholder', 'data-plugin'=> 'selectpicker']) !!}
          <div class='invalid-feedback'></div>
      </div>

      <div class="form-group">
        {{ Form::label('subject', 'Subject', ['class' => 'col-form-label']) }}
          {{ Form::select('subject',selective_multiple_subject(), $subject ?? null, ['class' => 'form-control form-control-sm selectize', 'data-placeholder' => '<--Subject-->', 'id'=> 'subject']) }}

          <div class='invalid-feedback'></div>
      </div>

      <div class="form-group">
        {{ Form::label('slip_name', 'Slip Name', ['class' => 'col-form-label']) }}
          {{ Form::text('slip_name', $slip_name ?? null, ['class' => 'form-control', 'placeholder' => 'Enter Slip Name']) }}
          <div class='invalid-feedback'></div>
      </div>

      <div class="form-group">
        {{ Form::label('student_type', 'Student Type', ['class' => 'col-form-label']) }}
          {{ Form::select('student_type', selective_student_type(), $student_type ?? null, ['class' => 'form-control', 'data-placeholder' => '>--Select Student Type--<']) }}
          <div class='invalid-feedback'></div>
      </div>

      <div class="form-group">
        {{ Form::label('registration_type', 'Registration Type', ['class' => 'col-form-label']) }}
          {{ Form::select('registration_type', selective_formfillup_type(), $registration_type ?? null, ['class' => 'form-control selectize', 'data-placeholder' => '>--Select Registration Type--<']) }}
          <div class='invalid-feedback'></div>
      </div>

      <div class="form-group">
        {{ Form::label('pay_type', 'Pay Type', ['class' => 'col-form-label']) }}
          {{ Form::select('pay_type', selective_pay_type(), $pay_type ?? null, ['class' => 'form-control', 'data-placeholder' => '>--Select Pay Type--<']) }}
          <div class='invalid-feedback'></div>
      </div>

      <div class="form-group">
        {{ Form::label('total_papers', 'Total Papers', ['class' => 'col-form-label']) }}
          {{ Form::text('total_papers', $total_papers ?? null, ['class' => 'form-control', 'placeholder' => 'Enter Total Papers']) }}
          <div class='invalid-feedback'></div>
      </div>

      <div class="form-group">
        {{ Form::label('total_amount', 'Total Amount', ['class' => 'col-form-label font-weight-bold']) }}
          {{ Form::text('total_amount', $total_amount ?? null, ['class' => 'form-control', 'placeholder' => 'Enter Total Amount']) }}
          <div class='invalid-feedback'></div>
      </div>

    </div>
  </div>

  <div class="form-group">
      {!! Form::submit('Save Data', ['class'=> 'btn btn-primary','data-value'=> 'create', 'data-button'=> 'save']) !!}
  </div>
</form>