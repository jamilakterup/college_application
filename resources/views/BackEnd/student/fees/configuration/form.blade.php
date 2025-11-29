@extends('BackEnd.student.layouts.master')
@section('page-title', isset($configuration) ? 'Edit Fees Configuration' : 'Create Fees Configuration')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Form Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas {{ isset($configuration) ? 'fa-edit' : 'fa-plus' }} mr-2"></i>
                        {{ isset($configuration) ? 'Edit' : 'Create' }} Fees Configuration
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('student.fees-configuration.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <form action="{{ isset($configuration) ? route('student.fees-configuration.update', $configuration->id) : route('student.fees-configuration.store') }}" 
                      method="POST" id="configuration-form">
                    @csrf
                    @if(isset($configuration))
                        @method('PATCH')
                    @endif

                    <div class="card-body">
                        <!-- Display validation errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <div class="form-group">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $configuration->title ?? '') }}" 
                                           placeholder="Enter configuration title"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label for="type">Payment Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type') is-invalid @enderror" 
                                            id="type" 
                                            name="type" 
                                            required>
                                        <option value="">Select Type</option>
                                        @foreach(\App\Models\FeesConfiguration::getPaymentTypes() as $key => $label)
                                            <option value="{{ $key }}" {{ old('type', $configuration->type ?? 'general') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Select "Promotion" to auto-promote students after payment</small>
                                </div>
                            </div>
                        </div>

                        <!-- Course and Academic Info -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label for="course">Course</label>
                                    <select class="form-control @error('course') is-invalid @enderror" 
                                            id="course" 
                                            name="course">
                                        <option value="">Select Course (Optional for Open System)</option>
                                        <option value="App\Models\StudentInfoHsc" {{ old('course', $configuration->course ?? '') == 'App\Models\StudentInfoHsc' ? 'selected' : '' }}>HSC</option>
                                        <option value="App\Models\StudentInfoHons" {{ old('course', $configuration->course ?? '') == 'App\Models\StudentInfoHons' ? 'selected' : '' }}>Honours</option>
                                        <option value="App\Models\StudentInfoDegree" {{ old('course', $configuration->course ?? '') == 'App\Models\StudentInfoDegree' ? 'selected' : '' }}>Degree</option>
                                        <option value="App\Models\StudentInfoMasters" {{ old('course', $configuration->course ?? '') == 'App\Models\StudentInfoMasters' ? 'selected' : '' }}>Masters</option>
                                    </select>
                                    @error('course')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave empty for open system, or select specific course for restricted access</small>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label for="level">Level</label>
                                    <input type="text" 
                                           class="form-control @error('level') is-invalid @enderror" 
                                           id="level" 
                                           name="level" 
                                           value="{{ old('level', $configuration->level ?? '') }}" 
                                           placeholder="e.g., 1st Year, 2nd Year">
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label for="academic_session">Academic Session</label>
                                    {!! Form::select('academic_session', 
                                        selective_multiple_session(), 
                                        old('academic_session', $configuration->academic_session ?? null), 
                                        ['class' => 'form-control selectize', 
                                         'id'=> 'academic_session', 
                                         'data-placeholder' => '--Select Session--']) !!}
                                    @error('academic_session')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Date Range -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="opening_date">Opening Date <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('opening_date') is-invalid @enderror" 
                                           id="opening_date" 
                                           name="opening_date" 
                                           value="{{ old('opening_date', isset($configuration) ? $configuration->opening_date->format('Y-m-d') : '') }}" 
                                           required>
                                    @error('opening_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="clossing_date">Closing Date <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('clossing_date') is-invalid @enderror" 
                                           id="clossing_date" 
                                           name="clossing_date" 
                                           value="{{ old('clossing_date', isset($configuration) ? $configuration->clossing_date->format('Y-m-d') : '') }}" 
                                           required>
                                    @error('clossing_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="mb-2">Status Settings</label>
                                    @php
                                        $statusChecked = false;
                                        if (old('status') !== null) {
                                            $statusChecked = old('status') == 1;
                                        } elseif (isset($configuration)) {
                                            $statusChecked = $configuration->status == 1;
                                        } else {
                                            $statusChecked = true; // Default checked for new
                                        }
                                    @endphp
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="status" 
                                               name="status" 
                                               value="1"
                                               {{ $statusChecked ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status">Active Status</label>
                                    </div>
                                    <small class="form-text text-muted">Enable to allow payments for this configuration</small>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="mb-2">Eligibility Check</label>
                                    @php
                                        $checkEligibleChecked = false;
                                        if (old('check_eligible_list') !== null) {
                                            $checkEligibleChecked = old('check_eligible_list') == 1;
                                        } elseif (isset($configuration)) {
                                            $checkEligibleChecked = $configuration->check_eligible_list == 1;
                                        }
                                    @endphp
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="check_eligible_list" 
                                               name="check_eligible_list" 
                                               value="1"
                                               {{ $checkEligibleChecked ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="check_eligible_list">Check Eligible List</label>
                                    </div>
                                    <small class="form-text text-muted">Enable to verify students against eligibility list</small>
                                </div>
                            </div>
                        </div>

                        <!-- Form Fields Configuration -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Form Fields Configuration</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label>Form Fields (Optional)</label>
                                                <textarea class="form-control" 
                                                          name="form_fields_json" 
                                                          id="form_fields_json" 
                                                          rows="5"
                                                          placeholder='["field1", "field2", "field3"]'>{{ old('form_fields_json', isset($configuration) && $configuration->form_fields ? json_encode($configuration->form_fields) : '') }}</textarea>
                                                <small class="form-text text-muted">Enter as JSON array, e.g., ["name", "email", "mobile"]</small>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Required Fields (Optional)</label>
                                                <textarea class="form-control" 
                                                          name="required_fields_json" 
                                                          id="required_fields_json" 
                                                          rows="5"
                                                          placeholder='["field1", "field2"]'>{{ old('required_fields_json', isset($configuration) && $configuration->required_fields ? json_encode($configuration->required_fields) : '') }}</textarea>
                                                <small class="form-text text-muted">Enter as JSON array, e.g., ["name", "email"]</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> {{ isset($configuration) ? 'Update' : 'Create' }} Configuration
                                </button>
                                <a href="{{ route('student.fees-configuration.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/selectize@0.12.6/dist/css/selectize.bootstrap4.css">
<style>
    .card {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    }
    .custom-control {
        position: relative;
        display: block;
        min-height: 1.5rem;
        padding-left: 1.5rem;
    }
    .custom-switch {
        padding-left: 2.25rem;
    }
    .custom-switch .custom-control-label::before {
        left: -2.25rem;
        width: 1.75rem;
        pointer-events: all;
        border-radius: 0.5rem;
    }
    .custom-switch .custom-control-label::after {
        top: calc(0.25rem + 2px);
        left: calc(-2.25rem + 2px);
        width: calc(1rem - 4px);
        height: calc(1rem - 4px);
        background-color: #adb5bd;
        border-radius: 0.5rem;
        transition: transform 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .custom-switch .custom-control-input:checked ~ .custom-control-label::after {
        background-color: #fff;
        transform: translateX(0.75rem);
    }
    .custom-control-input {
        position: absolute;
        left: 0;
        z-index: -1;
        width: 1rem;
        height: 1.25rem;
        opacity: 0;
    }
    .custom-control-label {
        position: relative;
        margin-bottom: 0;
        vertical-align: top;
    }
    .custom-control-label::before {
        position: absolute;
        top: 0.25rem;
        left: -1.5rem;
        display: block;
        width: 1rem;
        height: 1rem;
        pointer-events: none;
        content: "";
        background-color: #fff;
        border: 1px solid #adb5bd;
    }
    .custom-control-input:checked ~ .custom-control-label::before {
        color: #fff;
        border-color: #007bff;
        background-color: #007bff;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/selectize@0.12.6/dist/js/standalone/selectize.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize selectize for session dropdown
    $('#academic_session').selectize({
        create: false,
        sortField: 'text'
    });

    // Form validation
    $('#configuration-form').on('submit', function(e) {
        // Validate date range
        const openingDate = new Date($('#opening_date').val());
        const closingDate = new Date($('#clossing_date').val());
        
        if (closingDate < openingDate) {
            e.preventDefault();
            alert('Closing date must be after or equal to opening date');
            return false;
        }

        // Parse JSON fields if not empty
        const formFieldsText = $('#form_fields_json').val().trim();
        const requiredFieldsText = $('#required_fields_json').val().trim();

        if (formFieldsText) {
            try {
                const formFields = JSON.parse(formFieldsText);
                // Create hidden input with array values
                formFields.forEach((field, index) => {
                    $('<input>').attr({
                        type: 'hidden',
                        name: `form_fields[${index}]`,
                        value: field
                    }).appendTo(this);
                });
            } catch (error) {
                e.preventDefault();
                alert('Invalid JSON format in Form Fields');
                return false;
            }
        }

        if (requiredFieldsText) {
            try {
                const requiredFields = JSON.parse(requiredFieldsText);
                // Create hidden input with array values
                requiredFields.forEach((field, index) => {
                    $('<input>').attr({
                        type: 'hidden',
                        name: `required_fields[${index}]`,
                        value: field
                    }).appendTo(this);
                });
            } catch (error) {
                e.preventDefault();
                alert('Invalid JSON format in Required Fields');
                return false;
            }
        }
    });
});
</script>
@endpush
