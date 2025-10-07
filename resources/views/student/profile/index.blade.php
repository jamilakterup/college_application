@extends('layouts.student')

@section('title', 'Student Profile')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <!-- Profile Card -->
            <div class="card shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=4e73df&color=ffffff&size=128" 
                             class="rounded-circle img-fluid mx-auto d-block" style="width: 150px; height: 150px;" alt="Profile Image">
                    </div>
                    <h4 class="mb-1">{{ $student->name }}</h4>
                    <p class="text-muted mb-3">{{ $student->name_in_bengali }}</p>
                    <p class="mb-2"><i class="bi bi-telephone me-2"></i> {{ $student->mobile }}</p>
                    <p class="mb-3"><i class="bi bi-card-list me-2"></i> {{ $student->registration_no }}</p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.edit.profile') }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-2"></i> Edit Profile
                        </a>
                        <a href="{{ route('student.change.password') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-key me-2"></i> Change Password
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats Card -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Account Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>Profile Completion</span>
                            <span>85%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>Account Status</span>
                            <span class="badge bg-success">Approved</span>
                        </div>
                    </div>
                    
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>Joined On</span>
                            <span>{{ \Carbon\Carbon::parse($student->created_at)->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <!-- Personal Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Full Name (English)</label>
                            <p class="mb-0 fw-medium">{{ $student->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Full Name (Bengali)</label>
                            <p class="mb-0 fw-medium">{{ $student->name_in_bengali }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Father's Name (English)</label>
                            <p class="mb-0 fw-medium">{{ $student->fathers_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Father's Name (Bengali)</label>
                            <p class="mb-0 fw-medium">{{ $student->fathers_name_in_bengali }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Mother's Name (English)</label>
                            <p class="mb-0 fw-medium">{{ $student->mothers_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Mother's Name (Bengali)</label>
                            <p class="mb-0 fw-medium">{{ $student->mothers_name_in_bengali }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Mobile Number</label>
                            <p class="mb-0 fw-medium">{{ $student->mobile }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Address Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Address Information (Bengali)</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Post Office</label>
                            <p class="mb-0 fw-medium">{{ $student->post_office_in_bengali }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Thana/Upazila</label>
                            <p class="mb-0 fw-medium">{{ $student->thana_in_bengali }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">District</label>
                            <p class="mb-0 fw-medium">{{ $student->district_in_bengali }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Academic Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Academic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Course</label>
                            <p class="mb-0 fw-medium">{{ ucfirst($student->course) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Session</label>
                            <p class="mb-0 fw-medium">{{ $student->session }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Level/Year</label>
                            <p class="mb-0 fw-medium">{{ $student->level }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Class Roll</label>
                            <p class="mb-0 fw-medium">{{ $student->class_roll }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Registration No</label>
                            <p class="mb-0 fw-medium">{{ $student->registration_no }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection