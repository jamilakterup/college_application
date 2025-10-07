@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">Welcome, {{ $student->name }}!</h4>
                            <p class="mb-0">Registration No: {{ $student->registration_no }} | Class Roll: {{ $student->class_roll }}</p>
                        </div>
                        <div class="text-end">
                            <p class="mb-0">{{ ucfirst($student->course) }} | {{ $student->session }}</p>
                            <p class="mb-0">Level: {{ $student->level }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Available Documents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $availableDocuments->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-text fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Downloaded Documents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $downloadedDocumentsCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-download fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Pending Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingPaymentsCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Notifications</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $notificationsCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions and Documents Section -->
    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('student.profile') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-person-circle me-2"></i> View Profile
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                        <a href="{{ route('student.edit.profile') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-pencil-square me-2"></i> Edit Profile
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                        <a href="{{ route('student.change.password') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-key me-2"></i> Change Password
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                        <a href="{{ route('student.payment.history') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-credit-card me-2"></i> Payment History
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-question-circle me-2"></i> Help & Support
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Documents -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Available Documents</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Document Options:</div>
                            <a class="dropdown-item" href="{{ route('student.document.index') }}">View All Documents</a>
                            <a class="dropdown-item" href="{{ route('student.payment.history') }}">Download History</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($availableDocuments->isEmpty())
                        <div class="alert alert-info">
                            No documents are available for your course ({{ $student->course }}) and session ({{ $student->session }}).
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availableDocuments->take(4) as $document)
                                        <tr>
                                            <td>{{ $document->title }}</td>
                                            <td>৳ {{ number_format($document->price, 0) }}</td>
                                            <td>
                                                @if(in_array($document->id, $paidDocumentIds))
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif(in_array($document->id, $pendingDocumentIds))
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">Not Paid</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(in_array($document->id, $paidDocumentIds))
                                                    @php
                                                        $payment = $recentPayments->where('document_type_id', $document->id)->first();
                                                        $studentDocument = $payment ? $payment->studentDocument : null;
                                                    @endphp
                                                    
                                                    @if($studentDocument)
                                                        <a href="{{ route('student.document.download', $studentDocument->id) }}" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-download"></i> Download
                                                        </a>
                                                    @else
                                                        <span class="badge bg-info">Processing</span>
                                                    @endif
                                                @else
                                                    <a href="{{ route('student.payment.create', $document->id) }}" class="btn btn-sm btn-success">
                                                        <i class="bi bi-credit-card"></i> Pay Now
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            @if($availableDocuments->count() > 4)
                                <div class="text-center mt-3">
                                    <a href="{{ route('student.documents') }}" class="btn btn-outline-primary">View All Documents</a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection