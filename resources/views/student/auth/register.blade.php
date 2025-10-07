@extends('layouts.auth')

@section('title', 'Student Registration')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0 fw-bold">Student Registration</h3>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('student.register') }}" class="needs-validation" novalidate>
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Personal Information Section -->
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label">Name (English) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="name_bn" class="form-label">Name (Bengali) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name_bn') is-invalid @enderror" id="name_bn" name="name_bn" value="{{ old('name_bn') }}" required>
                                @error('name_bn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="fathers_name" class="form-label">Father's Name (English) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('fathers_name') is-invalid @enderror" id="fathers_name" name="fathers_name" value="{{ old('fathers_name') }}" required>
                                @error('fathers_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="fathers_name_bn" class="form-label">Father's Name (Bengali) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('fathers_name_bn') is-invalid @enderror" id="fathers_name_bn" name="fathers_name_bn" value="{{ old('fathers_name_bn') }}" required>
                                @error('fathers_name_bn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="mothers_name" class="form-label">Mother's Name (English) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('mothers_name') is-invalid @enderror" id="mothers_name" name="mothers_name" value="{{ old('mothers_name') }}" required>
                                @error('mothers_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="mothers_name_bn" class="form-label">Mother's Name (Bengali) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('mothers_name_bn') is-invalid @enderror" id="mothers_name_bn" name="mothers_name_bn" value="{{ old('mothers_name_bn') }}" required>
                                @error('mothers_name_bn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Address Section -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3">Address Information (Bengali)</h5>
                            </div>

                            <div class="col-md-4">
                                <label for="district_bn" class="form-label">District <span class="text-danger">*</span></label>

                                {!! Form::select('district_bn', create_option_array('district_thanas', 'district_bn', 'district_bn', 'District'), null, ['class' => 'form-select', 'id'=> 'district_bn']) !!}

                                @error('district_bn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="upazila_bn" class="form-label">Upazila <span class="text-danger">*</span></label>

                                {!! Form::select('upazila_bn', [], null, ['class' => 'form-select', 'id'=> 'upazila_bn', 'placeholder'=> '--Select Upazila--']) !!}

                                @error('upazila_bn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="post_office_bn" class="form-label">Post Office <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('post_office_bn') is-invalid @enderror" id="post_office_bn" name="post_office_bn" value="{{ old('post_office_bn') }}" required>
                                @error('post_office_bn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Academic Information Section -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3">Academic Information</h5>
                            </div>

                            <div class="col-md-6">
                                <label for="course" class="form-label">Course <span class="text-danger">*</span></label>
                                <select class="form-select @error('course') is-invalid @enderror" id="course" name="course" required>
                                    <option value="" selected disabled>Select Course</option>
                                    <option value="hsc" {{ old('course') == 'hsc' ? 'selected' : '' }}>HSC</option>
                                    <option value="honours" {{ old('course') == 'honours' ? 'selected' : '' }}>Honours</option>
                                    <option value="masters" {{ old('course') == 'masters' ? 'selected' : '' }}>Masters</option>
                                    <option value="degree" {{ old('course') == 'degree' ? 'selected' : '' }}>Degree</option>
                                </select>
                                @error('course')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="session" class="form-label">Session <span class="text-danger">*</span></label>

                                {!! Form::select('session', selective_multiple_session(), null, ['class' => 'form-select selectize', 'id'=> 'session', 'data-placeholder'=> '--Select Session--', 'required'=> true]) !!}

                                @error('session')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="level" class="form-label">Level/Year <span class="text-danger">*</span></label>

                                {!! Form::select('level', selective_multiple_level(), null, ['class' => 'form-select', 'id'=> 'level']) !!}

                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="class_roll" class="form-label">Class Roll <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('class_roll') is-invalid @enderror" id="class_roll" name="class_roll" value="{{ old('class_roll') }}" required>
                                @error('class_roll')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="registration_no" class="form-label">Registration No <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('registration_no') is-invalid @enderror" id="registration_no" name="registration_no" value="{{ old('registration_no') }}" required>
                                @error('registration_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Account Information Section -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3">Account Information</h5>
                            </div>

                            <div class="col-md-6">
                                <label for="mobile" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">+880</span>
                                    <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" value="{{ old('mobile') }}" placeholder="1XXXXXXXXX" required>
                                    @error('mobile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Enter your 10-digit mobile number without country code</small>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Minimum 6 characters</small>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="#" class="text-decoration-none">Terms and Conditions</a>
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">Register</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3 bg-light">
                    <div class="small">
                        Already have an account? <a href="{{ route('student.login') }}" class="text-decoration-none">Login here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Form validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
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
@endsection