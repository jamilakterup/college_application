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
                    <div class="info-label">Student ID/Reference ID:</div>
                    <div class="info-value">{{ $eligibleQueryData['reference_id'] }}</div>
                </div>
            </div>
            
            @if($eligibleRecord)
                @php
                    $studentInfo = isset($eligibleRecord->student_info) ? $eligibleRecord->student_info : null;
                    $displayFields = [
                        'name' => 'Name',
                        'father_name' => 'Father Name',
                        'mother_name' => 'Mother Name',
                        'dept_name' => 'Department',
                        'faculty_name' => 'Faculty',
                        'contact_no' => 'Mobile',
                    ];
                @endphp
                @foreach($displayFields as $key => $label)
                    @php
                        // Handle both object and array access
                        $value = null;
                        if ($studentInfo) {
                            if (is_object($studentInfo)) {
                                $value = $studentInfo->$key ?? null;
                            } elseif (is_array($studentInfo)) {
                                $value = $studentInfo[$key] ?? null;
                            }
                        }
                    @endphp
                    @if(!is_null($value) && $value !== '' && $value !== 'N/A')
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ $label }}:</div>
                                <div class="info-value">{{ $value }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach
                
                {{-- Show level, session, registration from student_info or eligibleRecord --}}
                @if($studentInfo)
                    @php
                        // Current Level
                        $currentLevel = is_object($studentInfo) ? ($studentInfo->current_level ?? null) : ($studentInfo['current_level'] ?? null);
                        if (!$currentLevel || $currentLevel === 'N/A') {
                            $currentLevel = $eligibleRecord->current_level ?? $eligibleQueryData['current_level'] ?? null;
                        }
                        
                        // Academic Session
                        $session = is_object($studentInfo) ? ($studentInfo->session ?? null) : ($studentInfo['session'] ?? null);
                        if (!$session || $session === 'N/A') {
                            $session = $eligibleRecord->academic_session ?? $eligibleQueryData['academic_session'] ?? null;
                        }
                        
                        // Registration ID
                        $regId = is_object($studentInfo) ? ($studentInfo->registration_id ?? null) : ($studentInfo['registration_id'] ?? null);
                    @endphp
                    
                    @if($currentLevel && $currentLevel !== 'N/A')
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Current Level:</div>
                                <div class="info-value">{{ $currentLevel }}</div>
                            </div>
                        </div>
                    @endif
                    
                    @if($session && $session !== 'N/A')
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Academic Session:</div>
                                <div class="info-value">{{ $session }}</div>
                            </div>
                        </div>
                    @endif
                    
                    @if($regId && $regId !== 'N/A')
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">Registration ID:</div>
                                <div class="info-value">{{ $regId }}</div>
                            </div>
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </div>

    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Validation Errors</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('fees-payment.submit') }}" id="payment-form">
        @csrf
        
        <!-- Hidden fields to pass eligibility data -->
        <input type="hidden" name="reference_id" value="{{ $eligibleQueryData['reference_id'] }}">
        <input type="hidden" name="current_level" value="{{ $eligibleQueryData['current_level'] ?? '' }}">
        
        @php
            // Check if this is new student registration (student_type is null)
            $isNewStudentRegistration = !$eligibleRecord || !isset($eligibleRecord->student_type) || empty($eligibleRecord->student_type);
            
            $personalFields = collect($fieldConfig)->filter(function ($field) {
                return in_array($field['name'], ['name', 'father_name', 'mother_name', 'date_of_birth', 'gender', 'mobile']);
            })->count();

            $academicFields = collect($fieldConfig)->filter(function ($field) {
                return in_array($field['name'], ['group_dept', 'registration_id']);
            })->count();
        @endphp

        {{-- Show alert if this is new student registration --}}
        @if($isNewStudentRegistration)
            <input type="hidden" name="is_new_student" value="1">
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>New Student Registration:</strong> Please fill in all required information below. Your data will be saved for future use.
            </div>
        @endif

        @if($personalFields > 0)
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
                                        @php
                                            $fieldValue = $eligibleRecord && isset($eligibleRecord->student_info) ? ($eligibleRecord->student_info->{$field['name']} ?? '') : '';
                                            $genderValue = old($field['name'], $fieldValue);
                                            $isGenderReadonly = $eligibleRecord && !empty($fieldValue);
                                        @endphp
                                        <select name="{{ $field['name'] }}" id="{{ $field['name'] }}" class="form-select @error($field['name']) is-invalid @enderror" @if($field['required']) required @endif @if($isGenderReadonly) disabled style="background-color: #f0f0f0;" title="This field is pre-filled from eligibility records" @endif>
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{ $genderValue === 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ $genderValue === 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Other" {{ $genderValue === 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @if($isGenderReadonly)
                                            <input type="hidden" name="{{ $field['name'] }}" value="{{ $genderValue }}">
                                        @endif
                                    @elseif($field['type'] === 'date')
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                            @php
                                                $fieldValue = $eligibleRecord && isset($eligibleRecord->student_info) ? ($eligibleRecord->student_info->{$field['name']} ?? '') : '';
                                            @endphp
                                            <input 
                                                type="{{ $field['type'] }}" 
                                                class="form-control @error($field['name']) is-invalid @enderror" 
                                                id="{{ $field['name'] }}" 
                                                name="{{ $field['name'] }}" 
                                                value="{{ old($field['name'], $fieldValue) }}"
                                                @if($field['name'] === 'date_of_birth') max="{{ date('Y-m-d', strtotime('-10 years')) }}" @endif
                                                @if($field['required']) required @endif
                                                @if($eligibleRecord && !empty($fieldValue)) readonly style="background-color: #f0f0f0;" title="This field is pre-filled from eligibility records" @endif
                                            >
                                        </div>
                                    @else
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            @php
                                                $fieldValue = $eligibleRecord && isset($eligibleRecord->student_info) ? ($eligibleRecord->student_info->{$field['name']} ?? '') : '';
                                            @endphp
                                            <input 
                                                type="{{ $field['type'] }}" 
                                                class="form-control @error($field['name']) is-invalid @enderror" 
                                                id="{{ $field['name'] }}" 
                                                name="{{ $field['name'] }}" 
                                                value="{{ old($field['name'], $fieldValue) }}"
                                                placeholder="Enter {{ $field['label'] }}"
                                                @if($field['name'] === 'mobile') pattern="01[0-9]{9}" title="Enter valid mobile number (e.g., 01766228895)" @endif
                                                @if($field['required']) required @endif
                                                @if($eligibleRecord && !empty($fieldValue)) readonly style="background-color: #f0f0f0;" title="This field is pre-filled from eligibility records" @endif
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
        @endif

        @if($academicFields > 0)
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="fas fa-book me-2"></i> Academic Information
                </h4>
                
                <div class="row">
                    @foreach($fieldConfig as $field)
                        @if(in_array($field['name'], ['group_dept', 'registration_id']))
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
                                    @else
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-{{ $field['name'] === 'registration_id' ? 'id-card' : 'user' }}"></i>
                                            </span>
                                            @php
                                                // For registration_id, get from student_info if eligible
                                                $fieldValue = '';
                                                if ($field['name'] === 'registration_id' && $eligibleRecord && isset($eligibleRecord->student_info)) {
                                                    $studentInfo = $eligibleRecord->student_info;
                                                    if (is_object($studentInfo)) {
                                                        $fieldValue = $studentInfo->registration_id ?? '';
                                                    } elseif (is_array($studentInfo)) {
                                                        $fieldValue = $studentInfo['registration_id'] ?? '';
                                                    }
                                                }
                                                // ONLY make readonly if we have a value
                                                $isFieldReadonly = $field['name'] === 'registration_id' && !empty($fieldValue);
                                            @endphp
                                            <input 
                                                type="{{ $field['type'] }}" 
                                                class="form-control @error($field['name']) is-invalid @enderror" 
                                                id="{{ $field['name'] }}" 
                                                name="{{ $field['name'] }}" 
                                                value="{{ old($field['name'], $fieldValue) }}"
                                                placeholder="Enter {{ $field['label'] }}"
                                                @if($field['required']) required @endif
                                                @if($isFieldReadonly) readonly style="background-color: #f0f0f0;" title="This field is pre-filled from eligibility records" @endif
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
        @endif

        {{-- Additional Fields (for student table: course, session, level, etc.) --}}
        @php
            $additionalFields = collect($fieldConfig)->filter(function ($field) {
                return !in_array($field['name'], ['name', 'father_name', 'mother_name', 'date_of_birth', 'gender', 'mobile', 'group_dept', 'registration_id']);
            });
        @endphp

        @if($additionalFields->count() > 0)
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="fas fa-info-circle me-2"></i> Additional Information
                </h4>
                
                <div class="row">
                    @foreach($additionalFields as $field)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="{{ $field['name'] }}" class="form-label @if($field['required']) required-field @endif">
                                    {{ $field['label'] }}
                                </label>
                                
                                @if($field['name'] === 'course')
                                    {{-- Course dropdown --}}
                                    <select name="{{ $field['name'] }}" id="{{ $field['name'] }}" class="form-select @error($field['name']) is-invalid @enderror" @if($field['required']) required @endif>
                                        <option value="">Select Course</option>
                                        <option value="hsc" {{ old($field['name']) === 'hsc' ? 'selected' : '' }}>HSC</option>
                                        <option value="honours" {{ old($field['name']) === 'honours' ? 'selected' : '' }}>Honours</option>
                                        <option value="degree" {{ old($field['name']) === 'degree' ? 'selected' : '' }}>Degree</option>
                                        <option value="masters" {{ old($field['name']) === 'masters' ? 'selected' : '' }}>Masters</option>
                                    </select>
                                @elseif($field['type'] === 'date')
                                    {{-- Date input --}}
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                        <input 
                                            type="date" 
                                            class="form-control @error($field['name']) is-invalid @enderror" 
                                            id="{{ $field['name'] }}" 
                                            name="{{ $field['name'] }}" 
                                            value="{{ old($field['name']) }}"
                                            @if($field['name'] === 'date_of_birth') max="{{ date('Y-m-d', strtotime('-10 years')) }}" @endif
                                            @if($field['required']) required @endif
                                        >
                                    </div>
                                @else
                                    {{-- Text input --}}
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-{{ $field['name'] === 'session' ? 'calendar-alt' : ($field['name'] === 'level' ? 'layer-group' : ($field['name'] === 'mobile' ? 'mobile-alt' : 'text-width')) }}"></i>
                                        </span>
                                        <input 
                                            type="{{ $field['type'] }}" 
                                            class="form-control @error($field['name']) is-invalid @enderror" 
                                            id="{{ $field['name'] }}" 
                                            name="{{ $field['name'] }}" 
                                            value="{{ old($field['name']) }}"
                                            placeholder="Enter {{ $field['label'] }}"
                                            @if($field['name'] === 'mobile') pattern="01[0-9]{9}" title="Enter valid mobile number (e.g., 01766228895)" @endif
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
                    @endforeach
                </div>
            </div>
        @endif
        
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

@push('styles')
<style>
    /* Ensure error messages are visible */
    .invalid-feedback {
        display: block !important;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545 !important;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .form-control.is-invalid:focus,
    .form-select.is-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }
    
    .alert {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scroll to first error if exists
        const firstError = document.querySelector('.is-invalid');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }

        // Mobile number validation
        const mobileInputs = document.querySelectorAll('input[name="mobile"]');
        mobileInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                const value = this.value;
                const pattern = /^01[0-9]{9}$/;
                const feedback = this.parentElement.nextElementSibling;
                
                if (value && !pattern.test(value)) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.style.display = 'block';
                        feedback.textContent = 'Enter valid mobile number (e.g., 01766228895)';
                    }
                } else if (value) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.style.display = 'none';
                    }
                } else {
                    this.classList.remove('is-invalid', 'is-valid');
                }
            });
        });
    });
</script>
@endpush