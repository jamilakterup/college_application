@extends('BackEnd.student.layouts.master')
@section('page-title', 'CSV Eligibility Upload')

@section('content')
<div class="container-fluid">
    <!-- Instructions Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i> Upload Instructions
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><strong>CSV Format Requirements:</strong></h6>
                    <ul class="mb-0">
                        <li>The CSV file should contain the following columns: <code>student_id, academic_year, level, is_active</code></li>
                        <li>The first row should contain column headers</li>
                        <li><code>student_id</code> is required and must exist in the selected student type</li>
                        <li><code>is_active</code> can be 1 (active) or 0 (inactive). Default is 1 if not provided</li>
                        <li>Maximum file size: 2MB</li>
                        <li>Supported formats: .csv, .txt</li>
                    </ul>
                </div>
                
                <h6><strong>Sample CSV Format:</strong></h6>
                <div class="bg-light p-3 border rounded">
                    <pre>student_id,academic_year,level,is_active
1,2023-24,HSC 1st Year,1
2,2023-24,HSC 1st Year,1
3,2023-24,HSC 1st Year,0</pre>
                </div>
            </div>
        </div>

        <!-- Upload Form Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-upload mr-2"></i> Upload CSV File
                </h3>
            </div>
            <div class="card-body">
                <form id="csv-upload-form" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="student_type">Student Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="student_type" name="student_type" required>
                                    <option value="">Select Student Type</option>
                                    <option value="App\Models\StudentInfoHsc">HSC</option>
                                    <option value="App\Models\StudentInfoHons">Honours</option>
                                    <option value="App\Models\StudentInfoDegree">Degree</option>
                                    <option value="App\Models\StudentInfoMasters">Masters</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="session">Session <span class="text-danger">*</span></label>
                                {!! Form::select('session', selective_multiple_session(), null, ['class' => 'form-control selectize', 'id'=> 'session', 'required' => true, 'data-placeholder' => '--Select Session--']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label for="csv_file">CSV File <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                                    <label class="custom-file-label" for="csv_file">Choose file</label>
                                </div>
                                <small class="form-text text-muted">Maximum file size: 2MB. Supported formats: CSV, TXT</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload mr-1"></i> Upload CSV
                            </button>
                            <a href="{{ route('student.fees-eligibility.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Eligibility Management
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Progress Card (hidden initially) -->
        <div class="card" id="progress-card" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Processing Upload
                </h3>
            </div>
            <div class="card-body">
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
                <p class="text-muted">Please wait while we process your CSV file...</p>
            </div>
        </div>

        <!-- Results Card (hidden initially) -->
        <div class="card" id="results-card" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-check-circle mr-2"></i> Upload Results
                </h3>
            </div>
            <div class="card-body">
                <div id="results-content">
                    <!-- Results will be populated here -->
                </div>
            </div>
        </div>
</div>
<!-- End container-fluid -->
@endsection

@push('styles')
<style>
.alert-info {
    background-color: #e3f2fd;
    border-color: #2196f3;
    color: #1565c0;
}

.bg-light {
    background-color: #f8f9fa !important;
}

pre {
    margin: 0;
    font-size: 0.9rem;
    color: #495057;
}

.progress {
    height: 1.5rem;
}

.custom-file-label::after {
    content: "Browse";
}

.results-summary {
    display: flex;
    justify-content: space-around;
    margin-bottom: 20px;
}

.result-stat {
    text-align: center;
    padding: 15px;
    border-radius: 8px;
    margin: 0 10px;
    flex: 1;
}

.result-stat h4 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: bold;
}

.result-stat p {
    margin: 5px 0 0 0;
    font-size: 0.9rem;
}

.success-stat {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.error-stat {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.total-stat {
    background-color: #e2e3e5;
    border: 1px solid #d6d8db;
    color: #383d41;
}

.error-list {
    max-height: 300px;
    overflow-y: auto;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 15px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Handle file input change to show selected file name
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    });

    // Handle form submission
    $('#csv-upload-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var $form = $(this);
        var $submitBtn = $form.find('[type="submit"]');
        var originalText = $submitBtn.html();
        
        // Validate file
        var fileInput = $('#csv_file')[0];
        if (!fileInput.files.length) {
            showNotification('Please select a CSV file', 'error');
            return;
        }
        
        var file = fileInput.files[0];
        if (file.size > 2 * 1024 * 1024) { // 2MB
            showNotification('File size must be less than 2MB', 'error');
            return;
        }
        
        if (!file.name.match(/\.(csv|txt)$/)) {
            showNotification('Please select a valid CSV or TXT file', 'error');
            return;
        }

        // Start upload process
        $submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Uploading...').prop('disabled', true);
        $('#progress-card').show();
        $('#results-card').hide();
        
        // Animate progress bar
        $('.progress-bar').css('width', '30%');
        
        $.ajax({
            url: '{{ route("student.fees-eligibility.csv-upload.process") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = (evt.loaded / evt.total) * 100;
                        $('.progress-bar').css('width', percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                $('.progress-bar').css('width', '100%');
                
                setTimeout(function() {
                    $('#progress-card').hide();
                    $('#results-card').show();
                    
                    if (response.success) {
                        displayResults(response);
                        showNotification(response.message, 'success');
                        
                        // Reset form if successful
                        if (response.details.failed === 0) {
                            $form[0].reset();
                            $('.custom-file-label').removeClass('selected').html('Choose file');
                        }
                    } else {
                        displayError(response.message);
                        showNotification(response.message, 'error');
                    }
                }, 1000);
            },
            error: function(xhr) {
                $('.progress-bar').css('width', '100%').removeClass('progress-bar-animated');
                
                setTimeout(function() {
                    $('#progress-card').hide();
                    $('#results-card').show();
                    
                    var message = 'Error processing CSV file';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    
                    displayError(message);
                    showNotification(message, 'error');
                }, 1000);
            },
            complete: function() {
                $submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    function displayResults(response) {
        var details = response.details;
        var total = details.successful + details.failed;
        
        var html = `
            <div class="results-summary">
                <div class="result-stat total-stat">
                    <h4>${total}</h4>
                    <p>Total Records</p>
                </div>
                <div class="result-stat success-stat">
                    <h4>${details.successful}</h4>
                    <p>Successful</p>
                </div>
                <div class="result-stat error-stat">
                    <h4>${details.failed}</h4>
                    <p>Failed</p>
                </div>
            </div>
        `;
        
        if (details.errors && details.errors.length > 0) {
            html += `
                <h6><strong>Error Details:</strong></h6>
                <div class="error-list">
                    <ul class="mb-0">
            `;
            
            details.errors.forEach(function(error) {
                html += `<li>${error}</li>`;
            });
            
            html += `
                    </ul>
                </div>
            `;
        }
        
        if (details.successful > 0) {
            html += `
                <div class="mt-3">
                    <a href="{{ route('student.fees-eligibility.index') }}" class="btn btn-primary">
                        <i class="fas fa-eye mr-1"></i> View Eligibility Records
                    </a>
                </div>
            `;
        }
        
        $('#results-content').html(html);
    }

    function displayError(message) {
        var html = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                ${message}
            </div>
        `;
        
        $('#results-content').html(html);
    }

    // Notification function
    function showNotification(message, type = 'info') {
        const alertClass = {
            info: 'alert-info',
            success: 'alert-success',
            error: 'alert-danger',
            warning: 'alert-warning'
        }[type] || 'alert-info';

        const notification = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" role="alert" style="top: 20px; right: 20px; z-index: 9999;">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        $('body').append(notification);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }
});
</script>
@endpush