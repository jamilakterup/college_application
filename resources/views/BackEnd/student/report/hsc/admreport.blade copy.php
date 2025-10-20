@php
    use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Hsc Report Management')

@push('styles')
    <style type="text/css">

    </style>
@endpush

@section('content')

    <div class="panel">
        <div class="panel-body">

            @can('hsc.admission.index')
                <div class="d-flex justify-content-between">
                    <button class="btn btn-primary" data-target="#filter-modal" data-toggle="modal" type="button"><i
                            class="fas fa-filter"></i> Filter HSC Admission Report</button>
                    {!! Form::open(['route' => 'report.hsc.admission.generate', 'method' => 'post', 'target' => '_blank']) !!}
                    {!! Form::hidden('session', $session) !!}
                    {!! Form::hidden('groups', $groups) !!}
                    {!! Form::hidden('gender', $gender) !!}
                    {!! Form::hidden('current_level', $current_level) !!}
                    {!! Form::hidden('from_date', $from_date) !!}
                    {!! Form::hidden('to_date', $to_date) !!}
                    {{-- <button class="btn btn-primary" type="submit" value><i class="fas fa-file-pdf"></i> Generate Report</button> --}}

                    <div class="dropdown">
                        <button type="button" class="btn btn-primary dropdown-toggle" id="reportDropdown"
                            data-toggle="dropdown" aria-expanded="false">
                            Download Report
                        </button>
                        <div class="dropdown-menu" aria-labelledby="reportDropdown" role="menu">
                            <button class="dropdown-item" name="type" type="submit" value="pdf"><i
                                    class="fas fa-file-pdf"></i> Generate PDF</button>
                            <button class="dropdown-item" name="type" type="submit" value="csv"><i
                                    class="fas fa-file-csv"></i> Generate CSV</button>
                            <button class="dropdown-item" name="type" type="submit" value="csv_dept_report"><i
                                    class="fas fa-file-csv"></i> Generate Departmental</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
                <br>

                @if (
                    $id != '' ||
                        $groups != '' ||
                        $gender != '' ||
                        $current_level != '' ||
                        $session != '' ||
                        $from_date != '' ||
                        $to_date != '')
                    <table class="table input-mark mb-0">
                        <caption class="mb-0">
                            Student Id: <span>{{ $id }}</span>
                            Group: <span>{{ $groups }}</span>
                            Gender: <span>{{ $gender }}</span>
                            Current Level: <span>{{ $current_level }}</span>
                            Session: <span>{{ $session }}</span>
                            From Date: <span>{{ $from_date }}</span>
                            To Date: <span>{{ $to_date }}</span>
                        </caption>
                    </table>
                @endif

                @if ($num_rows > 0)
                    <h3> Total Number Of Student: {{ $num_rows }}</h3>
                @endif

                @if ($total_amount > 0)
                    <strong style="font-size: 16px;">Total Amount : {{ $total_amount }}</strong><br />
                @endif

                <table class="table table-hover defDTable table-striped w-full cell-border">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>SSC Roll</th>
                            <th>Ref. ID</th>
                            <th>Session</th>
                            <th>Class Roll</th>
                            <th>Name</th>
                            <th>Groups</th>
                            <th>Current Level</th>
                            <th>Status</th>
                            <th>Payment Date</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($hscstudents as $college)
                            <tr class="">
                                <td>{{ $college->id }}</td>
                                <td>{{ $college->ssc_roll }}</td>
                                <td>{{ str_pad(str_pad($college->refference_id, 4, '0', STR_PAD_LEFT), 6, '11', STR_PAD_LEFT) }}
                                </td>
                                <td>{{ $college->session }}</td>
                                <td>{{ $college->class_roll }}</td>
                                <td>{{ $college->name }}</td>
                                <td>{{ $college->groups }}</td>
                                <td>{{ $college->current_level }}</td>
                                <td>{{ $college->status }}</td>
                                <td>{{ $college->payment_date }}</td>
                                <td>{{ $college->total_amount }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $hscstudents->appends(Request::except('page'))->links() }}

                {{-- end hsc_admission_index permission --}}
            @endcan

        </div>
    </div>

    {{-- modal --}}
    <div class="modal fade" id="filter-modal" aria-hidden="true" aria-labelledby="examplePositionSidebar" role="dialog"
        tabindex="-1">
        <div class="modal-dialog modal-simple">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Filter HSC Admission Report</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['route' => 'report.hsc.admission', 'method' => 'post', 'class' => 'form-horizontal']) !!}
                    <div class="form-group">
                        {!! Form::text('id', $id, ['class' => 'form-control', 'placeholder' => 'Student ID']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::text('ssc_roll', $ssc_roll, ['class' => 'form-control', 'placeholder' => 'SSC Roll']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::select('groups', selective_hsc_groups(), $groups, [
                            'class' => 'form-control group',
                            'autocomplete' => 'off',
                        ]) !!}
                        {!! invalid_feedback('groups') !!}
                    </div>

                    <div class="form-group">
                        {!! Form::select('gender', selective_gender_list(), $gender, [
                            'class' => 'form-control group',
                            'autocomplete' => 'off',
                        ]) !!}
                        {!! invalid_feedback('gender') !!}
                    </div>

                    <div class="form-group">
                        {!! Form::select('current_level', $current_level_lists, $current_level, [
                            'class' => 'form-control group',
                            'autocomplete' => 'off',
                        ]) !!}
                        {!! invalid_feedback('current_level') !!}
                    </div>

                    <div class="form-group">
                        {!! Form::select('session', selective_multiple_session(), $session, [
                            'class' => 'form-control session',
                            'autocomplete' => 'off',
                        ]) !!}
                        {!! invalid_feedback('session') !!}
                    </div>

                    <div class="form-group">
                        <div class="input-daterange" data-plugin="datepicker">
                            <div class="input-group mb-2">
                                <span class="input-group-addon">
                                    <i class="icon wb-calendar" aria-hidden="true"></i>
                                </span>
                                {!! Form::text('from_date', $from_date, [
                                    'class' => 'form-control',
                                    'placeholder' => 'From Date',
                                    'autocomplete' => 'off',
                                ]) !!}
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">to</span>
                                {!! Form::text('to_date', $to_date, [
                                    'class' => 'form-control',
                                    'placeholder' => 'To Date',
                                    'autocomplete' => 'off',
                                ]) !!}
                            </div>

                            <div class="input-group">
                                <input type="date" name="to_date" id="to_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-info btn-block">Search</button>
                    <button type="button" class="btn btn-default btn-block" data-dismiss="modal">Close</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script></script>
@endpush
