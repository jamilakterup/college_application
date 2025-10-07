@extends('fees-payment.layouts.masters')

@section('page-title', 'Online Fees Payment - Form Submission')
@section('section-title', 'Student Details & Confirmation')

@section('content')
    <div class="row">
        @if($feesApplication->status == 'Paid' && $feesApplication->invoice)
            <div class="col-md-6">
                <div class="form-section">
                    <h4 class="form-section-title">
                        <i class="fas fa-info-circle me-2"></i> Download Slip
                    </h4>

                    <div class="row">
                        <div class="col-12 text-center">
                            <p>Your payment has been successfully processed.</p>
                            <a href="{{ route('fees-payment.download-slip', ['application_id' => $feesApplication->id]) }}" class="btn btn-success">
                                <i class="fas fa-download me-2"></i> Download Confirmation Slip
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($feesApplication->invoice)
            <div class="col-md-6">
                <div class="form-section">
                    <h4 class="form-section-title">
                        <i class="fas fa-info-circle me-2"></i> Follow The Payment Guideline
                    </h4>

                    <div class="row">
                        @php
                            $biller_id = config('settings.college_biller_id');
                            $college_name_bn = config('settings.college_name_bn');
                            $student_id = $feesApplication->invoice->roll;
                            $payment_guideline = get_config('fees_payment_guideline');

                            if($payment_guideline){
                                echo @configTempleteToBody($payment_guideline,['student_id'=> $student_id, 'college_name_bn'=> $college_name_bn, 'biller_id'=> $biller_id,'total_amount'=>round($feesApplication->invoice->total_amount)]);
                            }
                        @endphp
                        <div class="row text-center">
                            <button class="btn btn-primary text-center" onClick="window.location.reload();">Click After Payment</button>
                        </div> 
                    </div>
                </div>
            </div>
        @endif

        <div class="col-md-6">
            <div class="form-section">
                <h4 class="form-section-title d-flex justify-content-between">
                    <i class="fas fa-info-circle me-2"></i> View Student Details

                    <a href="{{ route('fees-payment.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </a>
                </h4>

                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $feesApplication->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Father's Name</th>
                                    <td>{{ $feesApplication->father_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Mother's Name</th>
                                    <td>{{ $feesApplication->mother_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Birth</th>
                                    <td>{{ $feesApplication->date_of_birth ? date('d-m-Y', strtotime($feesApplication->date_of_birth)) : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td>{{ $feesApplication->gender ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile</th>
                                    <td>{{ $feesApplication->mobile ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Group/Department</th>
                                    <td>{{ $feesApplication->group_dept ?? 'N/A' }}</td>
                                </tr>
                                @php
                                    $referenceData = json_decode($feesApplication->reference_data, true);
                                @endphp
                                <tr>
                                    <th>Registration ID</th>
                                    <td>{{ $referenceData['registration_id'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Current Level</th>
                                    <td>{{ $referenceData['current_level'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Academic Session</th>
                                    <td>{{ $referenceData['academic_session'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Status</th>
                                    <td>
                                        <span class="badge {{ $feesApplication->status == 'Paid' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $feesApplication->status }}
                                        </span>
                                    </td>
                                </tr>
                                @if($feesApplication->invoice)
                                    <tr>
                                        <th>Total Amount</th>
                                        <td>{{ round($feesApplication->invoice->total_amount) }} {{ config('settings.currency', 'BDT') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@push('scripts')
    <script>
        // Optional: Add any JavaScript for dynamic behavior, e.g., confirmation alerts
        document.querySelectorAll('.btn-primary').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to refresh the page?')) {
                    window.location.reload();
                }
            });
        });
    </script>
@endpush