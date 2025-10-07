@extends('fees-payment.layouts.masters')

@section('page-title', 'Online Fees Payment - Form Submission')

@section('content')
    <h2 class="form-title">
        <i class="fas fa-file-invoice me-2"></i> Student Information Form
    </h2>

    <!-- Student Information Card -->
    <div class="student-info-card">
        <h3 class="student-info-title">
            <i class="fas fa-user-graduate"></i> Student Details
        </h3>
        <div class="row">
            <div class="col-md-6">
                <div class="info-item">
                    <div class="info-label">Registration ID:</div>
                    <div class="info-value">{{ $eligibleData['registration_id'] }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-item">
                    <div class="info-label">Current Level:</div>
                    <div class="info-value">{{ $eligibleData['current_level'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('fees-payment.submit') }}" class="needs-validation" novalidate>
        @csrf
        
        <!-- Hidden fields to pass eligibility data -->
        <input type="hidden" name="registration_id" value="{{ $eligibleData['registration_id'] }}">
        <input type="hidden" name="current_level" value="{{ $eligibleData['current_level'] }}">
        
        <div class="form-section">
            <h4 class="form-section-title">
                <i class="fas fa-user me-2"></i> Personal Information
            </h4>
            
            <div class="row">
                @foreach($fieldConfig as $field)
                    @if(in_array($field['name'], ['name', 'father_name', 'mother_name', 'date_of_birth', 'gender', 'mobile']))
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="{{ $field['name'] }}" class="form-label @if($field['required']) required-field @endif">
                                    {{ $field['label'] }}
                                </label>
                                
                                @if($field['type'] === 'select' && $field['name'] === 'gender')
                                    <select name="{{ $field['name'] }}" id="{{ $field['name'] }}" class="form-select @error($field['name']) is-invalid @enderror" @if($field['required']) required @endif>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old($field['name']) === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old($field['name']) === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old($field['name']) === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                @elseif($field['type'] === 'date')
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        <input 
                                            type="{{ $field['type'] }}" 
                                            class="form-control @error($field['name']) is-invalid @enderror" 
                                            id="{{ $field['name'] }}" 
                                            name="{{ $field['name'] }}" 
                                            value="{{ old($field['name']) }}"
                                            @if($field['required']) required @endif
                                        >
                                    </div>
                                @else
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input 
                                            type="{{ $field['type'] }}" 
                                            class="form-control @error($field['name']) is-invalid @enderror" 
                                            id="{{ $field['name'] }}" 
                                            name="{{ $field['name'] }}" 
                                            value="{{ old($field['name']) }}"
                                            placeholder="Enter {{ $field['label'] }}"
                                            @if($field['required']) required @endif
                                        >
                                    </div>
                                @endif
                                
                                @error($field['name'])
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        
        <div class="form-section">
            <h4 class="form-section-title">
                <i class="fas fa-book me-2"></i> Academic Information
            </h4>
            
            <div class="row">
                @foreach($fieldConfig as $field)
                    @if(in_array($field['name'], ['group_dept']))
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="{{ $field['name'] }}" class="form-label @if($field['required']) required-field @endif">
                                    {{ $field['label'] }}
                                </label>
                                
                                @if($field['type'] === 'select' && $field['name'] === 'group_dept')
                                    <select name="{{ $field['name'] }}" id="{{ $field['name'] }}" class="form-select select2 @error($field['name']) is-invalid @enderror" @if($field['required']) required @endif>
                                        @foreach($groupSubjectOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old($field['name']) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                
                                @error($field['name'])
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('fees-payment.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i> Submit Information
            </button>
        </div>
        
        <div class="mt-4 text-center">
            <p class="text-muted">
                <i class="fas fa-lock me-1"></i> Your information is secure and will only be used for payment processing
            </p>
        </div>
    </form>
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
