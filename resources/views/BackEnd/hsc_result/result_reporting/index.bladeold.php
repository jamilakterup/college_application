@php
use App\Libs\Study;
@endphp

@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Hsc Result Reporting Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

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
				{!! Form::close() !!}

			</div>
			<br>
			<?php if(count($resultgp)>0) { 
			 echo '<h3> Total Number Of Student: '.count($resultgp).'</h3>'; 
			} ?>
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
                <tr>
                    <th>Student Roll</th>	
                    <th>Student Name</th>		
                    <th>Admission Session</th>
                    <th>Department</th>				
                    <th>Group</th>
                    <th>GPA</th>
                    <th>Grade Point</th>
                    <th>Mark</th>
			    </tr>
            </thead>
            
            <tbody>

				@foreach($resultgp as $result)
                    <tr>
                        <td>{{$result->class_roll}}</td>
                        <td>{{$result->name}}</td>					
                        <td>{{$result->session}}</td>
                        <td>{{$result->subject_id}}</td>
                        <td>{{$result->group_id}}</td>					
                        <td>{{$result->grade}}</td>	
                        <td> 
                        <?php 
                        if(isset($result->point)){echo $result->point ;} 
                        else{echo $result->cgpa;} 
                        ?> </td>
                        <td>{{$result->total_mark}}</td>
                    </tr>	

				@endforeach
            </tbody>
          </table>
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush