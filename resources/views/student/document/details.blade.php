@extends('layouts.student')
    
@section('title', 'Document Details')
    
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $documentType->title }}</h5>
                    <a href="{{ route('student.document.index') }}" class="btn btn-sm btn-light text-dark">
                        <i class="fas fa-arrow-left"></i> Back to Documents
                    </a>
                </div>
                
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h4>{{ $documentType->title }}</h4>
                            <p class="text-muted">{{ $documentType->description }}</p>
                            
                            <div class="mt-3">
                                <h6>Document Details:</h6>
                                <table class="table table-bordered table-sm">
                                    <tr>
                                        <th width="30%">Type</th>
                                        <td>{{ ucfirst(str_replace('_', ' ', $documentType->type)) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Price</th>
                                        <td>{{ number_format($documentType->price, 2) }} BDT</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Payment Status</h5>
                                    @if($payment && $payment->status == 'paid')
                                        <div class="alert alert-success mb-3">
                                            <i class="fas fa-check-circle"></i> Paid
                                        </div>
                                        <p class="mb-1"><strong>Paid on:</strong> {{ $payment->paid_at->format('d M, Y') }}</p>
                                        <p><strong>Transaction ID:</strong> {{ $payment->transaction_id }}</p>
                                    @else
                                        <div class="alert alert-warning mb-3">
                                            <i class="fas fa-exclamation-circle"></i> Not Paid
                                        </div>
                                        <a href="{{ route('student.payment.create', $documentType->id) }}" class="btn btn-primary">
                                            <i class="fas fa-credit-card"></i> Pay Now
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($payment && $payment->status == 'paid')
                        <div class="card mt-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">Download Document</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> Your payment has been confirmed. You can now download your document.
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <p class="mb-1"><strong>Payment Date:</strong> {{ $payment->paid_at->format('d M, Y') }}</p>
                                        <p class="mb-0"><strong>Downloads:</strong> {{ $payment->download_count ?? 0 }} times</p>
                                    </div>
                                    @if($document)
                                        <a href="{{ route('student.document.download', $document->id) }}" class="btn btn-success">
                                            <i class="fas fa-download"></i> Download Document
                                        </a>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i> Document file not found. Please contact support.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="card mt-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Instructions</h5>
                        </div>
                        <div class="card-body">
                            <ol>
                                <li>Make the payment for the document.</li>
                                <li>After successful payment, you can immediately download your document.</li>
                                <li>You can download the document multiple times as needed.</li>
                                <li>For any issues, please contact the administration office.</li>
                            </ol>
                            
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-info-circle"></i> Note: The document is for official purposes only. Any misuse is punishable under university regulations.
                            </div>
                        </div>
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
        // You can add any JavaScript functionality here
        // For example, a confirmation dialog before payment
        $('.btn-pay').on('click', function(e) {
            if (!confirm('Are you sure you want to proceed with payment?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush