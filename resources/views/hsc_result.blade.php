<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>HSC Result</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <style>
        .form-group {
            margin-bottom: 10px;
        }

        .help-block {
            color: red;
        }

        .panel-heading.small-heading {
            background-color: #f7f7f7;
            font-weight: 600;
        }

        .row {
            margin: 0;
        }
    </style>
</head>

<body>

    <div class="row">
        <div class="col-sm-9 center-block" style="float:none; border-radius:6px;">
            <div class="panel panel-primary">
                <div class="panel-heading text-center">
                    <img src="{{ asset('img/rc.jpg') }}" alt="Logo" style="max-height:100px;">
                    <h3>HSC Result</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="padding:0 20px; margin-top: -20px;">
        <div class="col-sm-9 center-block" style="float:none; background-color:#79797931; border-radius:6px">
            <div class="row" style="padding-top: 20px;">
                <div class="col-sm-10 center-block" style="float:none;">

                    <div class="panel panel-default">
                        <div class="panel-heading small-heading">
                            Please provide the following information
                        </div>

                        <div class="panel-body">

                            @if (Session::get('error'))
                                <div class="alert alert-danger text-center">
                                    {!! Session::get('error') !!}
                                </div>
                            @endif

                            @if (!$show_transcript)

                                {{-- SEARCH FORM --}}
                                <form action="{{ route('hsc_result.search') }}" method="POST" class="form-horizontal">
                                    @csrf

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Student ID</label>
                                        <div class="col-sm-7">
                                            {!! Form::text('student_id', $student_id, ['class' => 'form-control', 'placeholder' => 'Enter Student ID']) !!}
                                            <div class="help-block">{!! invalid_feedback('student_id') !!}</div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Level</label>
                                        <div class="col-sm-7">
                                            {!! Form::select('level', $current_yr_lists, $current_level, ['class' => 'form-control']) !!}
                                            <div class="help-block">{!! invalid_feedback('level') !!}</div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Exam</label>
                                        <div class="col-sm-7">
                                            {!! Form::select('exam_id', $exam_lists, $exam_id, ['class' => 'form-control']) !!}
                                            <div class="help-block">{!! invalid_feedback('exam_id') !!}</div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-3 col-sm-7">
                                            <button class="btn btn-primary">Search</button>
                                        </div>
                                    </div>

                                </form>
                            @else
                                @php
                                    $exam = App\Models\Exam::find($exam_id);
                                    $info = App\Models\StudentInfoHsc::find($student_id);

                                    $sub_gpa = App\Models\StudentSubMarkGp::whereStudent_id($info->id)
                                        ->whereSession($info->session)
                                        ->where('exam_year', $exam_year)
                                        ->whereGroup_id($group_id)
                                        ->whereExam_id($exam_id)
                                        ->get();

                                    $cgpa_tot = App\Models\HscGpa::whereStudent_id($info->id)
                                        ->whereSession($info->session)
                                        ->where('exam_year', $exam_year)
                                        ->whereGroup_id($group_id)
                                        ->whereExam_id($exam_id)
                                        ->get();
                                @endphp

                                {{-- STUDENT INFO --}}
                                <table class="table table-bordered">
                                    <tr>
                                        <td><b>Exam</b></td>
                                        <td>{{ $exam->name }} - {{ $exam_year }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Name</b></td>
                                        <td>{{ $info->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Father</b></td>
                                        <td>{{ $info->father_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Mother</b></td>
                                        <td>{{ $info->mother_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Student ID</b></td>
                                        <td>{{ $info->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Session</b></td>
                                        <td>{{ $info->session }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Group</b></td>
                                        <td>{{ $info->groups }}</td>
                                    </tr>
                                </table>

                                {{-- MARKS --}}
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Subject</th>
                                            <th>CQ</th>
                                            <th>MCQ</th>
                                            <th>PR</th>
                                            <th>Total</th>
                                            <th>Grade</th>
                                            <th>Point</th>
                                            <th>GPA</th>
                                            <th>CGPA</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @php $k=1; @endphp
                                        @foreach ($sub_gpa as $sub)
                                            @php
                                                $particle_mark = App\Models\Mark::whereStudent_id($info->id)
                                                    ->whereSession($info->session)
                                                    ->whereGroup_id($group_id)
                                                    ->whereExam_id($exam_id)
                                                    ->whereSubject_id($sub->subject->id)
                                                    ->where('exam_year', $exam_year)
                                                    ->get();
                                            @endphp

                                            <tr>
                                                <td>{{ $sub->subject->code }}</td>
                                                <td>{{ $sub->subject->name }}</td>
                                                <td>{{ $particle_mark[0]->converted_mark ?? '-' }}</td>

                                                @if (count($particle_mark) == 3)
                                                    <td>{{ $particle_mark[1]->converted_mark }}</td>
                                                    <td>{{ $particle_mark[2]->converted_mark }}</td>
                                                @elseif(count($particle_mark) == 2)
                                                    <td>{{ $particle_mark[1]->converted_mark }}</td>
                                                    <td>-</td>
                                                @else
                                                    <td>-</td>
                                                    <td>-</td>
                                                @endif

                                                <td>{{ $sub->absent ? 'Absent' : $sub->total_mark }}</td>
                                                <td>{{ $sub->grade }}</td>
                                                <td>{{ $sub->point }}</td>

                                                @if ($k == 1)
                                                    <td rowspan="{{ $sub_gpa->count() }}">
                                                        {{ $cgpa_tot[0]->without_4th ?? '' }}</td>
                                                    <td rowspan="{{ $sub_gpa->count() }}">
                                                        {{ $cgpa_tot[0]->cgpa ?? '' }}</td>
                                                @endif

                                            </tr>
                                            @php $k++; @endphp
                                        @endforeach

                                    </tbody>
                                </table>

                                {{-- ACTIONS --}}
                                <div class="row">
                                    <div class="col-sm-6">
                                        {!! Form::open(['route' => 'hsc_result.result-pdf', 'method' => 'post']) !!}
                                        {!! Form::hidden('student_id', $student_id) !!}
                                        {!! Form::hidden('exam_id', $exam_id) !!}
                                        {!! Form::hidden('group_id', $group_id) !!}
                                        {!! Form::hidden('exam_year', $exam_year) !!}
                                        {!! Form::hidden('publish_id', $publish_id) !!}
                                        <button class="btn btn-danger">Download Transcript</button>
                                        {!! Form::close() !!}
                                    </div>

                                    <div class="col-sm-6 text-right">
                                        {{ link_to_route('hsc_result.result', 'Search Again', null, ['class' => 'btn btn-success']) }}
                                    </div>
                                </div>

                            @endif

                        </div>
                    </div>
                </div>

                <p style="text-align: right;">Developed and maintained by <a href="https://rajit.net/"
                        target="_blank">rajIT Solutions Ltd.</a></p>
            </div>
        </div>
    </div>

</body>

</html>
