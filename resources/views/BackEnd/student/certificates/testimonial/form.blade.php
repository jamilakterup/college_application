<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('ref_no', 'Serial', [], false) !!}
            {!! Form::text(
                'ref_no',
                old('ref_no', isset($testimonial) && isset($testimonial->ref_no) ? $testimonial->ref_no : null),
                ['class' => 'form-control', 'id' => 'ref_no'],
            ) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('student_id', 'Select Student <span class="text-danger">*</span>', [], false) !!}
            {!! Form::select(
                'student_id',
                $students->pluck('name', 'id'),
                old('student_id', isset($testimonial) ? $testimonial->student_id : null),
                ['class' => 'form-control select2', 'id' => 'student_id', 'placeholder' => 'Select Student', 'required'],
            ) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('issue_date', 'Issue Date <span class="text-danger">*</span>', [], false) !!}
            {!! Form::text(
                'issue_date',
                old(
                    'issue_date',
                    isset($testimonial) && isset($testimonial->issue_date) ? $testimonial->issue_date->format('Y-m-d') : null,
                ),
                ['class' => 'form-control datepickr', 'id' => 'issue_date', 'required'],
            ) !!}
        </div>
    </div>


    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('student_type', 'Student Type <span class="text-danger">*</span>', [], false) !!}
            {!! Form::select(
                'student_type',
                ['regular' => 'Regular', 'irregular' => 'Irregular'],
                old('student_type', isset($testimonial) ? $testimonial->student_type : null),
                ['class' => 'form-control', 'id' => 'student_type', 'required'],
            ) !!}
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('academic_year', 'Academic Year <span class="text-danger">*</span>', [], false) !!}
            {!! Form::select(
                'academic_year',
                selective_multiple_exam_year(),
                old('academic_year', isset($testimonial) ? $testimonial->academic_year : null),
                ['class' => 'form-control select2', 'id' => 'academic_year', 'required'],
            ) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('class_name', 'Class/Groups <span class="text-danger">*</span>', [], false) !!}
            {!! Form::select(
                'class_name',
                selective_multiple_study_group() + selective_multiple_hsc_level(),
                old('class_name', isset($testimonial) ? $testimonial->class_name : null),
                ['class' => 'form-control', 'id' => 'class_name', 'required'],
            ) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('admission_date', 'Admission Date') !!}
            {!! Form::text(
                'admission_date',
                old(
                    'admission_date',
                    isset($testimonial) && isset($testimonial->admission_date) ? $testimonial->admission_date->format('Y-m-d') : '',
                ),
                ['class' => 'form-control datepickr', 'id' => 'admission_date'],
            ) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('registration_no', 'Registration No') !!}
            {!! Form::text(
                'registration_no',
                old('registration_no', isset($testimonial) ? $testimonial->registration_no : null),
                ['class' => 'form-control', 'id' => 'registration_no', 'placeholder' => 'Registration Number'],
            ) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('study_period_from', 'Study Period From') !!}
            {!! Form::text(
                'study_period_from',
                old(
                    'study_period_from',
                    isset($testimonial) && isset($testimonial->study_period_from)
                        ? $testimonial->study_period_from->format('Y-m-d')
                        : '',
                ),
                ['class' => 'form-control datepickr', 'id' => 'study_period_from'],
            ) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('study_period_to', 'Study Period To') !!}
            {!! Form::text(
                'study_period_to',
                old(
                    'study_period_to',
                    isset($testimonial) && isset($testimonial->study_period_to)
                        ? $testimonial->study_period_to->format('Y-m-d')
                        : '',
                ),
                ['class' => 'form-control datepickr', 'id' => 'study_period_to'],
            ) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('gpa', 'GPA') !!}
            {!! Form::text('gpa', old('gpa', isset($testimonial) ? $testimonial->gpa : null), [
                'class' => 'form-control',
                'id' => 'gpa',
                'placeholder' => 'e.g., 5.00',
            ]) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('exam_year', 'Exam Year') !!}
            {!! Form::select(
                'exam_year',
                selective_multiple_exam_year(),
                old('exam_year', isset($testimonial) ? $testimonial->exam_year : null),
                ['class' => 'form-control select2', 'id' => 'exam_year'],
            ) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('attendance_percentage', 'Attendance Percentage') !!}
            {!! Form::text(
                'attendance_percentage',
                old('attendance_percentage', isset($testimonial) ? $testimonial->attendance_percentage : null),
                ['class' => 'form-control', 'id' => 'attendance_percentage', 'placeholder' => 'e.g., 95%'],
            ) !!}
        </div>
    </div>
</div>
