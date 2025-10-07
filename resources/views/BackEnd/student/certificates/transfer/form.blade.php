<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="student_id">Student <span class="text-danger">*</span></label>
            <select name="student_id" id="student_id" class="form-control select2" required>
                <option value="">Select Student</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" 
                            data-name="{{ $student->name }}"
                            data-father="{{ $student->father_name }}"
                            data-mother="{{ $student->mother_name }}"
                            data-roll="{{ $student->class_roll }}"
                            data-birth="{{ $student->birth_date }}"
                            data-religion="{{ $student->religion }}"
                            data-admission="{{ $student->admission_date }}"
                            {{ (isset($certificate) && $certificate->student_id == $student->id) ? 'selected' : '' }}>
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
                   value="{{ isset($certificate) ? $certificate->issue_date->format('Y-m-d') : old('issue_date', date('Y-m-d')) }}" required>
            @error('issue_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="admission_date">Admission Date <span class="text-danger">*</span></label>
            <input type="date" name="admission_date" id="admission_date" class="form-control datepickr" 
                   value="{{ isset($certificate) ? $certificate->admission_date->format('Y-m-d') : old('admission_date') }}" required>
            @error('admission_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="leaving_date">Leaving Date <span class="text-danger">*</span></label>
            <input type="text" name="leaving_date" id="leaving_date" class="form-control datepickr" 
                   value="{{ isset($certificate) ? $certificate->leaving_date->format('Y-m-d') : old('leaving_date') }}" required>
            @error('leaving_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="leaving_fees_upto">Leaving Fees Upto <span class="text-danger">*</span></label>
            <input type="text" name="leaving_fees_upto" id="leaving_fees_upto" class="form-control" 
                   value="{{ isset($certificate) ? $certificate->leaving_fees_upto : old('leaving_fees_upto') }}" required placeholder="e.g., June 2024">
            @error('leaving_fees_upto')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('tc_no', 'TC No', [], false) !!}
            {!! Form::text('tc_no', old('tc_no', isset($certificate) && isset($certificate->tc_no) ? $certificate->tc_no : null), ['class' => 'form-control', 'id' => 'tc_no']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="reason_for_leaving">Reason for Leaving <span class="text-danger">*</span></label>
            <textarea name="reason_for_leaving" id="reason_for_leaving" class="form-control" rows="2" 
                      placeholder="Reason for leaving the institution..." required>{{ isset($certificate) ? $certificate->reason_for_leaving : old('reason_for_leaving') }}</textarea>
            @error('reason_for_leaving')
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
            // Auto-fill birth date if available
            var birthDate = selectedOption.data('birth');
            if (birthDate && !$('#date_of_birth').val()) {
                $('#date_of_birth').val(birthDate);
            }
            
            // Auto-fill religion if available
            var religion = selectedOption.data('religion');
            if (religion && !$('#religion').val()) {
                $('#religion').val(religion);
            }
            
            // Auto-fill admission date if available
            var admissionDate = selectedOption.data('admission');
            if (admissionDate && !$('#admission_date').val()) {
                $('#admission_date').val(admissionDate);
            }
        }
    });

    // Show/hide dues amount based on dues paid status
    $('#dues_paid').on('change', function() {
        if ($(this).val() === 'no') {
            $('#dues_amount').closest('.form-group').show();
        } else {
            $('#dues_amount').closest('.form-group').hide();
            $('#dues_amount').val('0');
        }
    });

    // Initialize dues amount visibility
    if ($('#dues_paid').val() !== 'no') {
        $('#dues_amount').closest('.form-group').hide();
    }
});
</script>