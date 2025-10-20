@extends('BackEnd.student.layouts.master')

@section('title', 'View Testimonial')

@section('content')
<div class="page-content">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-eye"></i> Testimonial Details - {{ $testimonial->ref_no }}
                <div class="pull-right">
                    <a href="{{ route('certificates.testimonial.pdf', $testimonial->id) }}" 
                       class="btn btn-primary btn-sm" target="_blank">
                        <i class="fa fa-download"></i> Download PDF
                    </a>
                    <a href="{{ route('certificates.testimonial.edit', $testimonial->id) }}" 
                       class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('certificates.testimonial.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </h3>
        </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Reference No:</th>
                            <td>{{ $testimonial->ref_no }}</td>
                        </tr>
                        <tr>
                            <th>Student Name:</th>
                            <td>{{ $testimonial->student_name }}</td>
                        </tr>
                        <tr>
                            <th>Father's Name:</th>
                            <td>{{ $testimonial->father_name }}</td>
                        </tr>
                        <tr>
                            <th>Mother's Name:</th>
                            <td>{{ $testimonial->mother_name }}</td>
                        </tr>
                        <tr>
                            <th>Class:</th>
                            <td>{{ $testimonial->class_name }}</td>
                        </tr>
                        <tr>
                            <th>Roll No:</th>
                            <td>{{ $testimonial->roll_no }}</td>
                        </tr>
                        <tr>
                            <th>Registration No:</th>
                            <td>{{ $testimonial->registration_no ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Academic Year:</th>
                            <td>{{ $testimonial->academic_year }}</td>
                        </tr>
                        <tr>
                            <th>Issue Date:</th>
                            <td>{{ $testimonial->issue_date->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Admission Date:</th>
                            <td>{{ $testimonial->admission_date ? $testimonial->admission_date->format('d-m-Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Study Period:</th>
                            <td>
                                @if($testimonial->study_period_from && $testimonial->study_period_to)
                                    {{ $testimonial->study_period_from->format('d-m-Y') }} to {{ $testimonial->study_period_to->format('d-m-Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Attendance:</th>
                            <td>{{ $testimonial->attendance_percentage ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge badge-{{ $testimonial->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($testimonial->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Issued By:</th>
                            <td>{{ $testimonial->issuedBy->name ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="text-center">
                        <a href="{{ route('certificates.testimonial.pdf', $testimonial->id) }}" 
                           class="btn btn-primary btn-lg" target="_blank">
                            <i class="fa fa-download"></i> Download PDF Certificate
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection