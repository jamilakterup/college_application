@extends('layouts.student')

@section('title', 'My Documents')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Available Documents</h4>
                </div>
                <div class="card-body">
                    @if($groupedDocuments->isEmpty())
                        <div class="alert alert-info">
                            No documents are available for your course ({{ $student->course }}) and session ({{ $student->session }}).
                        </div>
                    @else
                        @foreach($groupedDocuments as $type => $documents)
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2 text-capitalize">{{ str_replace('_', ' ', $type) }}</h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Title</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($documents as $document)
                                                <tr>
                                                    <td>{{ $document->title }}</td>
                                                    <td>{{ number_format($document->price, 2) }} BDT</td>
                                                    <td>
                                                        @if(in_array($document->id, $paidDocumentTypeIds))
                                                            <span class="badge bg-success">Paid</span>
                                                        @else
                                                            <span class="badge bg-warning">Not Purchased</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(in_array($document->id, $paidDocumentTypeIds))
                                                            @php
                                                                $payment = $paidDocuments->where('document_type_id', $document->id)->first();
                                                                $studentDocument = $payment->studentDocument ?? null;
                                                            @endphp
                                                            
                                                            @if($studentDocument)
                                                                <a href="{{ route('student.document.show', $payment->studentDocument->id) }}" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-eye"></i> Details
                                                                </a>
                                                                <a href="{{ route('student.document.download', $studentDocument->id) }}" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-download"></i> Download
                                                                </a>
                                                            @else
                                                                <span class="badge bg-info">Processing</span>
                                                            @endif
                                                        @else
                                                            <a href="{{ route('student.payment.create', $document->id) }}" class="btn btn-sm btn-success">
                                                                <i class="fas fa-shopping-cart"></i> Pay Now
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>My Purchased Documents</h4>
                </div>
                <div class="card-body">
                    @if($paidDocuments->isEmpty())
                        <div class="alert alert-info">
                            You haven't purchased any documents yet.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Document</th>
                                        <th>Type</th>
                                        <th>Amount Paid</th>
                                        <th>Payment Date</th>
                                        <th>Downloads</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paidDocuments as $payment)
                                        <tr>
                                            <td>{{ $payment->documentType->title }}</td>
                                            <td class="text-capitalize">{{ str_replace('_', ' ', $payment->documentType->type) }}</td>
                                            <td>{{ number_format($payment->amount, 2) }} BDT</td>
                                            <td>{{ $payment->paid_at->format('d M Y, h:i A') }}</td>
                                            <td>{{ $payment->download_count }}</td>
                                            <td>
                                                @if($payment->studentDocument)
                                                    <a href="{{ route('student.document.show', $payment->studentDocument->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Details
                                                    </a>
                                                    <a href="{{ route('student.document.download', $payment->studentDocument->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                @else
                                                    <span class="badge bg-info">Processing</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection