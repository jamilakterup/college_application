@extends('BackEnd.student.layouts.master')

@section('title', 'Upload Character Certificates CSV')

@section('content')
    <div class="page-content">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-upload"></i> Batch Upload Character Certificates (প্রত্যয়ন পত্র)
                    <a href="{{ route('certificates.character.index') }}" class="btn btn-default btn-sm pull-right">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </h3>
            </div>

            <div class="panel-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {!! nl2br(e(session('success'))) !!}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {!! nl2br(e(session('warning'))) !!}
                    </div>
                @endif

                @if (session('error'))
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
                                <form action="{{ route('certificates.character.upload.csv') }}" method="POST"
                                    enctype="multipart/form-data">
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
                                <a href="{{ route('certificates.character.sample') }}" class="btn btn-success btn-block">
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
                        <ol>
                            <li><strong>student_id</strong> - Student ID from database</li>
                            <li><strong>issue_date</strong> - Issue date (YYYY-MM-DD format)</li>
                            <li><strong>academic_year</strong> - Academic year (e.g., 2023-2024)</li>
                            <li><strong>class_name</strong> - Class name (e.g., HSC 1st Year)</li>
                            <li><strong>character_rating</strong> - Character rating (excellent, very_good, good,
                                satisfactory)</li>
                            <li><strong>moral_character</strong> - Moral character description</li>
                            <li><strong>conduct_behavior</strong> - Conduct and behavior details</li>
                            <li><strong>academic_performance</strong> - Academic performance details</li>
                            <li><strong>attendance_record</strong> - Attendance record</li>
                            <li><strong>extracurricular_activities</strong> - Extracurricular activities</li>
                            <li><strong>special_qualities</strong> - Special qualities</li>
                            <li><strong>recommendations</strong> - Recommendations</li>
                            <li><strong>remarks</strong> - Additional remarks</li>
                        </ol>

                        <h5>Important Notes:</h5>
                        <ul>
                            <li>The first row must contain the column headers exactly as listed above</li>
                            <li>Student ID must exist in the database</li>
                            <li>Date format should be YYYY-MM-DD (e.g., 2024-01-15)</li>
                            <li>Character rating must be one of: <code>excellent</code>, <code>very_good</code>,
                                <code>good</code>, <code>satisfactory</code></li>
                            <li>Moral character and conduct behavior are required fields</li>
                            <li>Empty cells are allowed for optional fields</li>
                            <li>Certificate numbers will be auto-generated</li>
                            <li>All certificates will be marked as issued by the current user</li>
                        </ul>

                        <div class="alert alert-info">
                            <i class="fa fa-lightbulb-o"></i>
                            <strong>Tip:</strong> Download the sample CSV file first to ensure your data is in the correct
                            format.
                        </div>

                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>Character Rating Values:</strong>
                            <ul class="mb-0">
                                <li><code>excellent</code> = অত্যন্ত উত্তম</li>
                                <li><code>very_good</code> = অতি উত্তম</li>
                                <li><code>good</code> = উত্তম</li>
                                <li><code>satisfactory</code> = সন্তোষজনক</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
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
@endsection
