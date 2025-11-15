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
                        {!! Form::submit('Add Fees', ['class' => 'btn btn-primary w-100']) !!}
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>


        <div class="panel-body">
            <h2>All list </h2>

            @if ($fees_details)
                <form method="POST" action="{{ route('hsc_result.fees_details.generate') }}">
                    @csrf
                    <input type="hidden" name="year_type" id="yearInput" value="">

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Session</th>
                                <th scope="col">Current Year</th>
                                <th scope="col">Fees Header</th>
                                <th scope="col">Type</th>
                                <th scope="col">Science Amount</th>
                                <th scope="col">Humanities Amount</th>
                                <th scope="col">Business Amount</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fees_details as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->session }}</td>
                                    <td>{{ $item->current_year }}</td>
                                    <td>{{ $item->fees_header }}</td>
                                    <td>{{ $item->is_gov == '0' ? 'Government Fees' : 'Non-Gov Fees' }}</td>
                                    <td>{{ $item->science }}</td>
                                    <td>{{ $item->humanities }}</td>
                                    <td>{{ $item->business }}</td>
                                    <td>
                                        <a href="{{ route('hsc_result.fees_details.edit', $item->id) }}"
                                            class="btn btn-success pb-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path
                                                    d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                <path fill-rule="evenodd"
                                                    d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                            </svg>
                                        </a>


                                        <button data-toggle="modal" data-target="#exampleModal" type="button"
                                            class="btn btn-danger pb-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path
                                                    d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                <path
                                                    d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                            </svg></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary" onclick="setYear('HSC 1st Year')">
                        Generate HSC 1st Year Fees
                    </button>
                    <button type="submit" class="btn btn-primary" onclick="setYear('HSC 2nd Year')">
                        Generate HSC 2nd Year Fees
                    </button>

                    <script>
                        function setYear(year) {
                            document.getElementById('yearInput').value = year;
                        }
                    </script>
                </form>
            @endif

        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

@endsection
