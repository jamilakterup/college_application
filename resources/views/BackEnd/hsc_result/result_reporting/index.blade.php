{{-- View: BackEnd/hsc_result/result_reporting/index.blade.php --}}
@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Hsc Result Reporting Management')
@section('content')

<div class="panel">
    <header class="panel-heading">
        <h3 class="panel-title">Hsc Result Subject Summery Reporting</h3>
    </header>
    <div class="panel-body">
        
        <div class="col-md-12 d-flex justify-content-center mb-5">
            {!! Form::open(['route'=> 'hsc_result.result_reporting_subject_wise', 'method'=> 'post', 'class' => 'form-inline']) !!}
                <div class="form-group">
                    {!! Form::select('groups', create_option_array('groups', 'id', 'name', 'Group'), $groups, ['class'=> 'form-control']) !!}
                  </div>

                  <div class="form-group">
                    {!! Form::select('exams', create_option_array('exams', 'id', 'name', 'Exam'), $exams, ['class'=> 'form-control']) !!}
                  </div>

                  <div class="form-group">
                    {!! Form::select('session', selective_multiple_session(), $session, ['class'=>'form-control session', 'autocomplete'=> 'off']) !!}
                  </div>

                  <div class="form-group">
                    {!! Form::select('exam_year', selective_multiple_exam_year(), $exam_year ?? null, ['class'=>'form-control exam_year', 'autocomplete'=> 'off']) !!}
                  </div>
                
                <button type="submit" class="btn btn-info">Search</button>
                @if(count($resultgp) > 0)
                    <button type="submit" name="export" value="1" class="btn btn-success ml-1">Export to Excel</button>
                @endif
            {!! Form::close() !!}
        </div>
    </div>
</div>

<div class="panel">
    <header class="panel-heading">
        <h3 class="panel-title">Hsc Result Reporting</h3>
    </header>
    <div class="panel-body">
        <div class="col-md-12 d-flex justify-content-center">
            {!! Form::open(['route'=> 'hsc_result.result_reporting_search', 'method'=> 'post', 'class' => 'form-inline']) !!}
                <div class="form-group">
                    {!! Form::select('groups', create_option_array('groups', 'id', 'name', 'Group'), $groups, ['class'=> 'form-control']) !!}
                  </div>

                  <div class="form-group">
                    {!! Form::select('subjects', create_option_array('subjects', 'id', 'name', 'Subject'), $subjects, ['class'=> 'form-control']) !!}
                  </div>

                  <div class="form-group">
                    {!! Form::select('exams', create_option_array('exams', 'id', 'name', 'Exam'), $exams, ['class'=> 'form-control']) !!}
                  </div>

                  <div class="form-group">
                    {!! Form::select('session', selective_multiple_session(), $session, ['class'=>'form-control session', 'autocomplete'=> 'off']) !!}
                  </div>

                  <div class="form-group">
                    <select  name="grade_scales" class="form-control input-sm">
                        <option value="">Please select a grade </option>
                        <?php
                          $results = DB::select('select * from grade_scales');
                          foreach($results as $result){ ?>
                             <option  value="{{$result->point}}"
                                @if ($result->point == $grade_scales)
                                    {{'selected'}}
                                @endif
                                >{{$result->letter_grade}}</option>
                         <?php  } ?>
                        <option  value="p"
                            @if ('p' == $grade_scales)
                                {{'selected'}}
                            @endif
                        >pass</option>
                    </select>   
                  </div>
                
                <button type="submit" class="btn btn-info">Search</button>
                @if(count($resultgp) > 0)
                    <button type="submit" name="export" value="1" class="btn btn-success ml-1">Export to Excel</button>
                @endif
            {!! Form::close() !!}
        </div>
        
        @if(count($resultgp) > 0)
            <h3>Total Number Of Students: {{ count($resultgp) }}</h3>
            <table class="table table-hover defDTable w-full cell-border">
                <thead>
                    <tr>
                        <th>Student Roll</th>   
                        <th>Student Name</th>       
                        <th>Admission Session</th>
                        <th>Department</th>             
                        <th>Group</th>
                        <th>GPA</th>
                        <th>Without 4th</th>
                        <th>Grade Point</th>
                        <th>Mark</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($resultgp as $result)
                        <tr>
                            <td>{{ $result['Student Roll'] ?? '0' }}</td>
                            <td>{{ $result['Student Name'] ?? '0' }}</td>                  
                            <td>{{ $result['Admission Session'] ?? '0' }}</td>
                            <td>{{ $result['Department'] ?? '0' }}</td>
                            <td>{{ $result['Group'] ?? '0' }}</td>                  
                            <td>{{ $result['GPA'] ?? '0' }}</td> 
                            <td>{{ $result['Without 4th'] ?? '0' }}</td> 
                            <td>{{ $result['Grade Point'] ?? '0' }}</td>
                            <td>{{ $result['Mark'] ?? '0' }}</td>
                        </tr>   
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<div class="panel">
    <header class="panel-heading">
        <h3 class="panel-title">Hsc Result Chart Summery Reporting</h3>
    </header>
    <div class="panel-body">
        
        <div class="col-md-12 d-flex justify-content-center mb-5">
            {!! Form::open(['route' => ['hsc_result.process.student-charts'], 'method' => 'post', 'class' => 'form-inline']) !!}
                  <div class="form-group">
                    {!! Form::select('exams', create_option_array('exams', 'id', 'name', 'Exam'), $exams, ['class'=> 'form-control']) !!}
                  </div>

                  <div class="form-group">
                    {!! Form::select('session', selective_multiple_session(), $session, ['class'=>'form-control session', 'autocomplete'=> 'off']) !!}
                  </div>

                  <div class="form-group">
                    {!! Form::select('exam_year', selective_multiple_exam_year(), $exam_year ?? null, ['class'=>'form-control exam_year', 'autocomplete'=> 'off']) !!}
                  </div>
                
                <button type="submit" class="btn btn-info">Search</button>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection