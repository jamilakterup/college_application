@extends('fees-payment.layouts.masters')

@section('page-title', 'Online Fees Payment - Form Submission')
@section('section-title', 'Student Details & Confirmation')

@section('content')
    @if(isset($updatedLevel) && $updatedLevel && $feesApplication->invoice && $updatedLevel !== $feesApplication->invoice->level)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">
                <i class="fas fa-check-circle me-2"></i>Congratulations! You have been promoted!
            </h5>
            <p class="mb-0">
                Your payment has been processed successfully and you have been promoted from 
                <strong>{{ $feesApplication->invoice->level }}</strong> to 
                <strong>{{ $updatedLevel }}</strong>.
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
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

                        <div class="col-12 text-center">
                            <p>Your payment has been successfully processed.</p>
                            <a href="https://easycollegemate.com/ecmngdc/get-admit-card" class="btn btn-success">
                                <i class="fas fa-download me-2"></i> Download Admit Card
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
                                @if(!empty($feesApplication->name))
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $feesApplication->name }}</td>
                                    </tr>
                                @endif
                                
                                @if(!empty($feesApplication->father_name))
                                    <tr>
                                        <th>Father's Name</th>
                                        <td>{{ $feesApplication->father_name }}</td>
                                    </tr>
                                @endif
                                
                                @if(!empty($feesApplication->mother_name))
                                    <tr>
                                        <th>Mother's Name</th>
                                        <td>{{ $feesApplication->mother_name }}</td>
                                    </tr>
                                @endif
                                
                                @if(!empty($feesApplication->date_of_birth))
                                    <tr>
                                        <th>Date of Birth</th>
                                        <td>{{ date('d-m-Y', strtotime($feesApplication->date_of_birth)) }}</td>
                                    </tr>
                                @endif
                                
                                @if(!empty($feesApplication->gender))
                                    <tr>
                                        <th>Gender</th>
                                        <td>{{ $feesApplication->gender }}</td>
                                    </tr>
                                @endif
                                
                                @if(!empty($feesApplication->mobile))
                                    <tr>
                                        <th>Mobile</th>
                                        <td>{{ $feesApplication->mobile }}</td>
                                    </tr>
                                @endif
                                
                                @if(!empty($feesApplication->group_dept))
                                    <tr>
                                        <th>Group/Department</th>
                                        <td>{{ $feesApplication->group_dept }}</td>
                                    </tr>
                                @endif
                                
                                @if(!empty($feesApplication->registration_id))
                                    <tr>
                                        <th>Registration ID</th>
                                        <td>{{ $feesApplication->registration_id }}</td>
                                    </tr>
                                @endif
                                
                                @if($feesApplication->invoice && !empty($feesApplication->invoice->level))
                                    <tr>
                                        <th>Payment For Level</th>
                                        <td>{{ $feesApplication->invoice->level }}</td>
                                    </tr>
                                @endif
                                
                                @if($updatedLevel && $feesApplication->invoice && $updatedLevel !== $feesApplication->invoice->level)
                                    <tr>
                                        <th>Promoted To Level</th>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="fas fa-arrow-up me-1"></i> {{ $updatedLevel }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                                
                                @if($feesApplication->invoice && !empty($feesApplication->invoice->admission_session))
                                    <tr>
                                        <th>Academic Session</th>
                                        <td>{{ $feesApplication->invoice->admission_session }}</td>
                                    </tr>
                                @endif
                                
                                <tr>
                                    <th>Payment Status</th>
                                    <td>
                                        <span class="badge {{ $feesApplication->status == 'Paid' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $feesApplication->status }}
                                        </span>
                                    </td>
                                </tr>
                                
                                @if($feesApplication->invoice && !empty($feesApplication->invoice->total_amount))
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