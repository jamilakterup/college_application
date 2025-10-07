@extends('layouts.student')

@section('title', 'Edit Profile')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Profile Information</h6>
                </div>
                
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('student.update.profile') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Profile Photo -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="text-center mb-3">
                                    <div class="position-relative d-inline-block">
                                        @if($student->profile_photo)
                                            <img src="{{ asset('storage/' . $student->profile_photo) }}" 
                                                 class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;" 
                                                 id="profile-photo-preview" alt="Profile Photo">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=4e73df&color=ffffff&size=150" 
                                                 class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;" 
                                                 id="profile-photo-preview" alt="Profile Photo">
                                        @endif
                                        <div class="position-absolute bottom-0 end-0">
                                            <label for="profile_photo" class="btn btn-sm btn-primary rounded-circle">
                                                <i class="bi bi-camera"></i>
                                            </label>
                                            <input type="file" name="profile_photo" id="profile_photo" class="d-none" accept="image/*">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <small class="text-muted">Click on the camera icon to change your profile photo</small>
                                    @error('profile_photo')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Personal Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name (English) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $student->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="name_in_bengali" class="form-label">Full Name (Bengali) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name_in_bengali') is-invalid @enderror" 
                                               id="name_in_bengali" name="name_in_bengali" value="{{ old('name_in_bengali', $student->name_in_bengali) }}" required>
                                        @error('name_in_bengali')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="fathers_name" class="form-label">Father's Name (English) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('fathers_name') is-invalid @enderror" 
                                               id="fathers_name" name="fathers_name" value="{{ old('fathers_name', $student->fathers_name) }}" required>
                                        @error('fathers_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="fathers_name_in_bengali" class="form-label">Father's Name (Bengali) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('fathers_name_in_bengali') is-invalid @enderror" 
                                               id="fathers_name_in_bengali" name="fathers_name_in_bengali" value="{{ old('fathers_name_in_bengali', $student->fathers_name_in_bengali) }}" required>
                                        @error('fathers_name_in_bengali')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="mothers_name" class="form-label">Mother's Name (English) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('mothers_name') is-invalid @enderror" 
                                               id="mothers_name" name="mothers_name" value="{{ old('mothers_name', $student->mothers_name) }}" required>
                                        @error('mothers_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="mothers_name_in_bengali" class="form-label">Mother's Name (Bengali) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('mothers_name_in_bengali') is-invalid @enderror" 
                                               id="mothers_name_in_bengali" name="mothers_name_in_bengali" value="{{ old('mothers_name_in_bengali', $student->mothers_name_in_bengali) }}" required>
                                        @error('mothers_name_in_bengali')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $student->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="mobile" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('mobile') is-invalid @enderror" 
                                               id="mobile" name="mobile" value="{{ old('mobile', $student->mobile) }}" required>
                                        @error('mobile')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                               id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}" required>
                                        @error('date_of_birth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address Information Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Address Information (Bengali)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="village_in_bengali" class="form-label">Village/Area <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('village_in_bengali') is-invalid @enderror" 
                                               id="village_in_bengali" name="village_in_bengali" value="{{ old('village_in_bengali', $student->village_in_bengali) }}" required>
                                        @error('village_in_bengali')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="post_office_in_bengali" class="form-label">Post Office <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('post_office_in_bengali') is-invalid @enderror" 
                                               id="post_office_in_bengali" name="post_office_in_bengali" value="{{ old('post_office_in_bengali', $student->post_office_in_bengali) }}" required>
                                        @error('post_office_in_bengali')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="thana_in_bengali" class="form-label">Thana/Upazila <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('thana_in_bengali') is-invalid @enderror" 
                                               id="thana_in_bengali" name="thana_in_bengali" value="{{ old('thana_in_bengali', $student->thana_in_bengali) }}" required>
                                        @error('thana_in_bengali')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="district_in_bengali" class="form-label">District <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('district_in_bengali') is-invalid @enderror" 
                                               id="district_in_bengali" name="district_in_bengali" value="{{ old('district_in_bengali', $student->district_in_bengali) }}" required>
                                        @error('district_in_bengali')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('student.profile') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back to Profile
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Preview profile photo before upload
    document.getElementById('profile_photo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-photo-preview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection