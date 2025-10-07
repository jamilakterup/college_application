@extends('BackEnd.student.layouts.master')

@section('title', 'View Transfer Certificate')

@section('content')
<div class="page-content">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-eye"></i> Transfer Certificate Details
                <div class="pull-right">
                    <a href="{{ route('certificates.transfer.pdf', $certificate->id) }}" 
                       class="btn btn-primary btn-sm" target="_blank">
                        <i class="fa fa-download"></i> Download PDF
                    </a>
                    <a href="{{ route('certificates.transfer.edit', $certificate->id) }}" 
                       class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('certificates.transfer.index') }}" class="btn btn-default btn-sm">
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
                                    <td><strong>TC No:</strong></td>
                                    <td>{{ $certificate->tc_no }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Issue Date:</strong></td>
                                    <td>{{ $certificate->issue_date->format('d-m-Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Admission Date:</strong></td>
                                    <td>{{ $certificate->admission_date->format('d-m-Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Leaving Date:</strong></td>
                                    <td>{{ $certificate->leaving_date->format('d-m-Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h4 class="panel-title">Attendance</h4>
                        </div>
                        <div class="panel-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $certificate->status == 'active' ? 'success' : 'danger' }}">
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

                <div class="col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4 class="panel-title">Additional Information</h4>
                        </div>
                        <div class="panel-body">
                            @if($certificate->permanent_address)
                            <div class="mb-3">
                                <h5><strong>Permanent Address:</strong></h5>
                                <p class="well">{{ $certificate->permanent_address }}</p>
                            </div>
                            @endif

                            @if($certificate->any_scholarship)
                            <div class="mb-3">
                                <h5><strong>Scholarship/Awards:</strong></h5>
                                <p class="well">{{ $certificate->any_scholarship }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">Assessment & Remarks</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><strong>Reason for Leaving:</strong></h5>
                                    <p class="well">{{ $certificate->reason_for_leaving }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="{{ route('certificates.transfer.pdf', $certificate->id) }}" 
                   class="btn btn-primary btn-lg" target="_blank">
                    <i class="fa fa-download"></i> Download PDF Certificate
                </a>
            </div>
        </div>
    </div>
</div>
@endsection