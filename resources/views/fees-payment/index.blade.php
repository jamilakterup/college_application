
@extends('fees-payment.layouts.masters')

@section('page-title', 'Online Fees Payment - Eligibility Check')
@section('section-title', 'Check Eligibility for Fees Payment')
@section('content')
<div class="form-container">
    <form action="{{ route('fees.check-eligibility') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        
        <div class="mb-4">
            <label for="registration_id" class="form-label fw-bold">Registration ID</label>
            <input type="text" class="form-control @error('registration_id') is-invalid @enderror" 
                    id="registration_id" name="registration_id" 
                    placeholder="Enter your registration ID" 
                    value="{{ old('registration_id') }}" required>
            @error('registration_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
            <div class="form-text">Enter your student registration ID as provided by the institution</div>
        </div>
        
        <div class="mb-4">
            <label for="current_level" class="form-label fw-bold">Current Level</label>
            {!! Form::select('current_level', $courseLevelOptions, null, ['placeholder'=>'Select Level', 'class'=>'form-select' ,'required'=>true, 'id'=> 'current_level']) !!}

            @error('current_level')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="academic_session" class="form-label fw-bold">Session</label>
            {!! Form::select('academic_session', selective_multiple_session(), null, ['class'=>'form-select' ,'required'=>true, 'id'=> 'academic_session']) !!}

            @error('academic_session')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">Check Eligibility</button>
        </div>
        
        <div class="mt-4 text-center">
            <p class="text-muted">Need help? Contact support at <a href="mailto:support@rajit.net">support@rajit.net</a></p>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Form validation
    (function () {
        'use strict'
        
        // Fetch all forms that need validation
        var forms = document.querySelectorAll('.needs-validation')
        
        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>
@endpush