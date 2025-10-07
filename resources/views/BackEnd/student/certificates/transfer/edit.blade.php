@extends('BackEnd.student.layouts.master')

@section('title', 'Edit Transfer Certificate')

@section('content')
<div class="page-content">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-edit"></i> Edit Transfer Certificate
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

            <form action="{{ route('certificates.transfer.update', $certificate->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                @include('BackEnd.student.certificates.transfer.form')

                <div class="form-group text-right">
                    <a href="{{ route('certificates.transfer.show', $certificate->id) }}" class="btn btn-info">
                        <i class="fa fa-eye"></i> View
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Update Transfer Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection