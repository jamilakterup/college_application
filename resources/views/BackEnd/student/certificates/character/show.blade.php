@extends('BackEnd.student.layouts.master')

@section('title', 'View Character Certificate')

@section('content')
    <div class="page-content">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-eye"></i> Character Certificate Details (প্রত্যয়ন পত্র)
                    <div class="pull-right">
                        <a href="{{ route('certificates.character.pdf', $certificate->id) }}" class="btn btn-primary btn-sm"
                            target="_blank">
                            <i class="fa fa-download"></i> Download PDF
                        </a>
                        <a href="{{ route('certificates.character.edit', $certificate->id) }}"
                            class="btn btn-warning btn-sm">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('certificates.character.index') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </h3>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h4 class="panel-title">Student Information</h4>
                            </div>
                            <div class="panel-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $certificate->student_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Father's Name:</strong></td>
                                        <td>{{ $certificate->father_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Mother's Name:</strong></td>
                                        <td>{{ $certificate->mother_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Roll No:</strong></td>
                                        <td>{{ $certificate->roll_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Registration No:</strong></td>
                                        <td>{{ $certificate->registration_no ?: 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Class:</strong></td>
                                        <td>{{ $certificate->class_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Academic Year:</strong></td>
                                        <td>{{ $certificate->academic_year }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h4 class="panel-title">Certificate Information</h4>
                            </div>
                            <div class="panel-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Certificate No:</strong></td>
                                        <td>{{ $certificate->certificate_no }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Issue Date:</strong></td>
                                        <td>{{ $certificate->issue_date->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Character Rating:</strong></td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $certificate->character_rating_text }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Study Period:</strong></td>
                                        <td>
                                            @if ($certificate->study_period_from && $certificate->study_period_to)
                                                {{ $certificate->study_period_from->format('d-m-Y') }} to
                                                {{ $certificate->study_period_to->format('d-m-Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Attendance Record:</strong></td>
                                        <td>{{ $certificate->attendance_record ?: 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $certificate->status == 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($certificate->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Issued By:</strong></td>
                                        <td>{{ $certificate->issuedBy->name ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('certificates.character.pdf', $certificate->id) }}" class="btn btn-primary btn-lg"
                        target="_blank">
                        <i class="fa fa-download"></i> Download PDF Certificate
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
