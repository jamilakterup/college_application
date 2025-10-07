<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="student_id">Student <span class="text-danger">*</span></label>
            <select name="student_id" id="student_id" class="form-control select2" required>
                <option value="">Select Student</option>
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" data-name="{{ $student->name }}"
                        data-father="{{ $student->father_name }}" data-mother="{{ $student->mother_name }}"
                        data-roll="{{ $student->class_roll }}"
                        {{ isset($certificate) && $certificate->student_id == $student->id ? 'selected' : '' }}>
                        {{ $student->name }} - Roll: {{ $student->class_roll }} ({{ $student->session }})
                    </option>
                @endforeach
            </select>
            @error('student_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="issue_date">Issue Date <span class="text-danger">*</span></label>
            <input type="text" name="issue_date" id="issue_date" class="form-control datepickr"
                value="{{ isset($certificate) ? $certificate->issue_date->format('Y-m-d') : old('issue_date', date('Y-m-d')) }}"
                required>
            @error('issue_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('academic_year', 'Academic Year <span class="text-danger">*</span>', [], false) !!}
            {!! Form::select(
                'academic_year',
                selective_multiple_session(),
                old('academic_year', isset($certificate) ? $certificate->academic_year : null),
                ['class' => 'form-control select2', 'id' => 'academic_year', 'required'],
            ) !!}
            @error('academic_year')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('class_name', 'Class/Groups <span class="text-danger">*</span>', [], false) !!}
            {!! Form::select(
                'class_name',
                selective_multiple_study_group() + selective_multiple_hsc_level(),
                old('class_name', isset($certificate) ? $certificate->class_name : null),
                ['class' => 'form-control', 'id' => 'class_name', 'required'],
            ) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="certificate_no">Memo No</label>
            <input type="text" name="certificate_no" id="certificate_no" class="form-control"
                value="{{ isset($certificate) ? $certificate->certificate_no : old('certificate_no') }}">
            @error('certificate_no')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="registration_no">Registration No</label>
            <input type="text" name="registration_no" id="registration_no" class="form-control"
                value="{{ isset($certificate) ? $certificate->registration_no : old('registration_no') }}">
            @error('registration_no')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="study_period_from">Study Period From</label>
            <input type="date" name="study_period_from" id="study_period_from" class="form-control datepickr"
                value="{{ isset($certificate) && $certificate->study_period_from ? $certificate->study_period_from->format('Y-m-d') : old('study_period_from') }}">
            @error('study_period_from')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="study_period_to">Study Period To</label>
            <input type="date" name="study_period_to" id="study_period_to" class="form-control datepickr"
                value="{{ isset($certificate) && $certificate->study_period_to ? $certificate->study_period_to->format('Y-m-d') : old('study_period_to') }}">
            @error('study_period_to')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Auto-fill student details when student is selected
        $('#student_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                // You can auto-fill other fields here if needed
                console.log('Selected student:', selectedOption.data('name'));
            }
        });
    });
</script>
