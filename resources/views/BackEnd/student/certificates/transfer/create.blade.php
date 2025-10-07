@extends('BackEnd.student.layouts.master')

@section('title', 'Create Transfer Certificate')

@section('content')
<div class="page-content">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-plus"></i> Create New Transfer Certificate
                <a href="{{ route('certificates.transfer.index') }}" class="btn btn-default btn-sm pull-right">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </h3>
        </div>
        
        <div class="panel-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('certificates.transfer.store') }}" method="POST">
                @csrf
                
                @include('BackEnd.student.certificates.transfer.form')

                <div class="form-group text-right">
                    <button type="reset" class="btn btn-default">Reset</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Create Transfer Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select Student",
        allowClear: true
    });
    
    // Auto-fill student data when student is selected
    $('#student_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var birthDate = selectedOption.data('birth-date');
        var religion = selectedOption.data('religion');
        var admissionDate = selectedOption.data('admission-date');
        
        if (birthDate) {
            $('#date_of_birth').val(birthDate);
        }
        if (religion) {
            $('#religion').val(religion);
        }
        if (admissionDate) {
            $('#admission_date').val(admissionDate);
        }
    });
});
</script>
@endsection