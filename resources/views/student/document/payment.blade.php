@extends('layouts.student')

@section('title', 'Pay for Document')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Payment for {{ $documentType->title }}</h4>
                </div>
                <div class="card-body">
                    @php
                        $biller_id = config('settings.college_biller_id');
                        $college_name_bn = config('settings.college_name_bn');
                        $payment_guideline = get_config('document_payment_guideline');

                        if($payment_guideline){
                            echo @configTempleteToBody($payment_guideline,['student_id'=> $student->class_roll, 'college_name_bn'=> $college_name_bn, 'biller_id'=> $biller_id,'total_amount'=>$documentType->price]);
                        }
                    @endphp
                    
                    <form action="{{ route('student.payment.store', $documentType->id) }}" method="POST">
                        @csrf
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Confirm</button>
                            <a href="{{ route('student.document.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const paymentInfoDivs = document.querySelectorAll('.payment-info');
        
        paymentMethodSelect.addEventListener('change', function() {
            // Hide all payment info divs
            paymentInfoDivs.forEach(div => div.classList.add('d-none'));
            
            // Show the selected payment method info
            const selectedMethod = this.value;
            if (selectedMethod) {
                const infoDiv = document.getElementById(selectedMethod + '_info');
                if (infoDiv) {
                    infoDiv.classList.remove('d-none');
                }
            }
        });
    });
</script>
@endpush
@endsection