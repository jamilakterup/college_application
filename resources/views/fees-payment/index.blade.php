
@extends('fees-payment.layouts.masters')

@section('page-title', 'Online Fees Payment - Eligibility Check')
@section('section-title', 'Check Eligibility for Fees Payment')
@section('content')
<div class="form-container">
    <!-- Configuration Details Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Fees Header Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Title:</strong> {{ $config->title }}</p>
                    <p><strong>Course:</strong> {{ $config->getCourseName() }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Level:</strong> {{ $config->level }}</p>
                    <p><strong>Session:</strong> {{ $config->academic_session }}</p>
                </div>
            </div>
            <a href="{{ route('fees-payment.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form action="{{ route('fees.check-eligibility') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        
        <div class="mb-4">
            <label for="reference_id" class="form-label fw-bold">Student/Reg ID</label>
            <input type="text" class="form-control @error('reference_id') is-invalid @enderror" 
                    id="reference_id" name="reference_id" 
                    placeholder="Enter your Student/Reg ID" 
                    value="{{ old('reference_id') }}" required>
            @error('reference_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
            <div class="form-text">Enter your student/reg ID as provided by the institution</div>
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