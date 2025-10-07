@extends('fees-payment.layouts.masters')

@section('page-title', 'Online Fees Payment - Form Submission')

@section('content')
<div class="form-container">
    <h2 class="form-title">
        <i class="fas fa-file-invoice me-2"></i> Select Payslip Information
    </h2>

    <form method="POST" action="{{ route('fees-payment.payment-information.submit') }}" class="needs-validation" novalidate>
        @csrf
        
        <input type="hidden" name="fees_application_id" value="{{$feesApplication->id}}">

        <div class="form-section">
            
            <div class="form-group">
                <label for="header_id" class="form-label required-field">
                    Payslip Header
                </label>
                
                <select name="header_id" id="header_id" class="form-select @error('header_id') is-invalid @enderror" required>
                    <option value="">Select Payslip</option>
                    @foreach($headers as $header)
                        <option value="{{$header->id}}" {{ old('header_id') === $header->id ? 'selected' : '' }} @if(count($headers) == 1) selected @endif>{{$header->title}} - {{$header->payslipgenerators->sum('fees')}}</option>
                    @endforeach
                </select>

                {!!invalid_feedback('header_id')!!}
            </div>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('fees-payment.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i> Next
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')

@endpush
