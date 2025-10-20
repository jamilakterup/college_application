@extends('BackEnd.student.layouts.master')

@section('title', 'Edit Testimonial')

@section('content')
<div class="page-content">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-edit"></i> Edit Testimonial - {{ $testimonial->ref_no }}
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

            <form action="{{ route('certificates.testimonial.update', $testimonial->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                @include('BackEnd.student.certificates.testimonial.form')

                <div class="form-group text-right">
                    <a href="{{ route('certificates.testimonial.show', $testimonial->id) }}" class="btn btn-info">
                        <i class="fa fa-eye"></i> View
                    </a>
                    <button type="reset" class="btn btn-default">Reset</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Update Testimonial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection