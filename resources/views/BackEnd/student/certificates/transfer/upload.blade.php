@extends('BackEnd.student.layouts.master')

@section('title', 'Upload Transfer Certificates CSV')

@section('content')
<div class="page-content">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-upload"></i> Batch Upload Transfer Certificates
                <a href="{{ route('certificates.transfer.index') }}" class="btn btn-default btn-sm pull-right">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </h3>
        </div>
        
        <div class="panel-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {!! nl2br(e(session('success'))) !!}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {!! nl2br(e(session('warning'))) !!}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h4 class="panel-title">Upload CSV File</h4>
                        </div>
                        <div class="panel-body">
                            <form action="{{ route('certificates.transfer.upload.csv') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="form-group">
                                    <label for="csv_file">Select CSV File <span class="text-danger">*</span></label>
                                    <input type="file" name="csv_file" id="csv_file" class="form-control" 
                                           accept=".csv,.txt" required>
                                    <small class="help-block">
                                        <i class="fa fa-info-circle"></i> 
                                        Only CSV files are allowed. Maximum file size: 2MB
                                    </small>
                                </div>

                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-upload"></i> Upload CSV
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h4 class="panel-title">Download Sample</h4>
                        </div>
                        <div class="panel-body">
                            <p>Download the sample CSV file to see the required format:</p>
                            <a href="{{ route('certificates.transfer.sample') }}" class="btn btn-success btn-block">
                                <i class="fa fa-download"></i> Download Sample CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h4 class="panel-title">CSV Format Instructions</h4>
                </div>
                <div class="panel-body">
                    <h5>Required CSV Columns (in exact order):</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ol>
                                <li><strong>student_id</strong> - Student ID from database</li>
                                <li><strong>issue_date</strong> - Issue date (YYYY-MM-DD)</li>
                                <li><strong>admission_date</strong> - Admission date (YYYY-MM-DD)</li>
                                <li><strong>leaving_date</strong> - Leaving date (YYYY-MM-DD)</li>
                                <li><strong>reason_for_leaving</strong> - Reason for leaving</li>
                                <li><strong>conduct_behavior</strong> - Conduct and behavior</li>
                                <li><strong>last_attended_class</strong> - Last attended class</li>
                                <li><strong>session</strong> - Academic session</li>
                                <li><strong>dues_paid</strong> - Dues paid (yes/no)</li>
                                <li><strong>date_of_birth</strong> - Date of birth (YYYY-MM-DD)</li>
                                <li><strong>nationality</strong> - Nationality</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <ol start="12">
                                <li><strong>religion</strong> - Religion</li>
                                <li><strong>academic_performance</strong> - Academic performance</li>
                                <li><strong>promoted_class</strong> - Promoted to class</li>
                                <li><strong>group_subject</strong> - Group/Subject</li>
                                <li><strong>dues_amount</strong> - Dues amount (number)</li>
                                <li><strong>any_scholarship</strong> - Any scholarship</li>
                                <li><strong>caste_category</strong> - Caste/Category</li>
                                <li><strong>permanent_address</strong> - Permanent address</li>
                                <li><strong>working_days</strong> - Total working days</li>
                                <li><strong>present_days</strong> - Present days</li>
                                <li><strong>remarks</strong> - Additional remarks</li>
                            </ol>
                        </div>
                    </div>

                    <h5>Important Notes:</h5>
                    <ul>
                        <li>The first row must contain the column headers exactly as listed above</li>
                        <li>Student ID must exist in the database</li>
                        <li>Date format should be YYYY-MM-DD (e.g., 2024-01-15)</li>
                        <li>Dues paid must be either <code>yes</code> or <code>no</code></li>
                        <li>Leaving date must be after or equal to admission date</li>
                        <li>Required fields: student_id, issue_date, admission_date, leaving_date</li>
                        <li>Empty cells are allowed for optional fields</li>
                        <li>TC numbers will be auto-generated</li>
                        <li>All certificates will be marked as issued by the current user</li>
                    </ul>

                    <div class="alert alert-info">
                        <i class="fa fa-lightbulb-o"></i>
                        <strong>Tip:</strong> Download the sample CSV file first to ensure your data is in the correct format.
                    </div>

                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Important:</strong> Transfer certificates have many required fields. Make sure all mandatory data is provided to avoid upload errors.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // File validation
    $('#csv_file').on('change', function() {
        var file = this.files[0];
        if (file) {
            var fileSize = file.size / 1024 / 1024; // Convert to MB
            var fileName = file.name;
            var fileExtension = fileName.split('.').pop().toLowerCase();
            
            if (fileExtension !== 'csv' && fileExtension !== 'txt') {
                alert('Please select a CSV file.');
                $(this).val('');
                return;
            }
            
            if (fileSize > 2) {
                alert('File size must be less than 2MB.');
                $(this).val('');
                return;
            }
        }
    });
});
</script>
@endpush