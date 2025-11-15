@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Fees Details')

@push('styles')
    <style>
        .form-group label {
            font-weight: 600;
        }
    </style>
@endpush

@section('content')

    <div class="panel">
        <header class="panel-heading">
            <h3 class="panel-title">Fees Details</h3>
        </header>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 m-auto">
                    {{ Form::open(['route' => 'hsc_result.fees_details.store', 'method' => 'post', 'class' => 'form-horizontal']) }}

                    <div class="row">
                        {{-- Session --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::label('session', 'Session', ['class' => 'form-control-label']) }}
                                {{ Form::select('session', selective_multiple_session(), null, ['class' => 'form-control', 'id' => 'session']) }}
                                {!! invalid_feedback('session') !!}
                            </div>
                        </div>

                        {{-- Current Year --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::label('current_year', 'Current Year', ['class' => 'form-control-label']) }}
                                {{ Form::select('current_year', selective_multiple_hsc_level(), null, ['class' => 'form-control year']) }}
                                {!! invalid_feedback('current_year') !!}
                            </div>
                        </div>

                        {{-- Is Gov --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::label('is_gov', 'Is Government?', ['class' => 'form-control-label']) }}
                                {{ Form::select('is_gov', ['' => '<--Select Type-->', '1' => 'Yes', '0' => 'No'], null, ['class' => 'form-control']) }}
                                {!! invalid_feedback('is_gov') !!}
                            </div>

                        </div>

                        {{-- Head --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                {{ Form::label('fees_header', 'Fees Header', ['class' => 'form-control-label']) }}
                                {{ Form::text('fees_header', null, ['class' => 'form-control', 'id' => 'fees_header', 'placeholder' => 'বিভিন্ন খাতের বিবরণ']) }}
                                {!! invalid_feedback('fees_header') !!}
                            </div>
                        </div>
                    </div>

                    <h3 class="panel-title px-0">Fees Amount (Every Group)</h3>

                    <div class="row">
                        {{-- Science Fees --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('science', 'Fees For Science', ['class' => 'form-control-label']) }}
                                {{ Form::text('science', null, ['class' => 'form-control', 'id' => 'science', 'placeholder' => 'Amount']) }}
                                {!! invalid_feedback('science') !!}
                            </div>
                        </div>

                        {{-- Humanities Fees --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('humanities', 'Fees For Humanities', ['class' => 'form-control-label']) }}
                                {{ Form::text('humanities', null, ['class' => 'form-control', 'id' => 'humanities', 'placeholder' => 'Amount']) }}
                                {!! invalid_feedback('humanities') !!}
                            </div>
                        </div>

                        {{-- Business Fees --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                {{ Form::label('business', 'Fees For Business', ['class' => 'form-control-label']) }}
                                {{ Form::text('business', null, ['class' => 'form-control', 'id' => 'business', 'placeholder' => 'Amount']) }}
                                {!! invalid_feedback('business') !!}
                            </div>
                        </div>
                    </div>
                    {{-- Submit --}}
                    <div class="col-md-12 d-flex justify-content-end align-items-end">
                        {!! Form::submit('Update Fees', ['class' => 'btn btn-primary w-100']) !!}
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>

    </div>
@endsection
