@extends('BackEnd.student.layouts.master')

@section('title', 'Create Testimonial')

@section('content')
<div class="page-content">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-plus"></i> Create New Testimonial
                <a href="{{ route('certificates.testimonial.index') }}" class="btn btn-default btn-sm pull-right">
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

            <form action="{{ route('certificates.testimonial.store') }}" method="POST">
                @csrf
                @include('BackEnd.student.certificates.testimonial.form')
                <div class="form-group text-right">
                    <button type="reset" class="btn btn-default">Reset</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Create Testimonial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection