@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Degree Admission Report Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <div class="panel-body">

				@can('hsc.admission.index')
        	<div class="d-flex justify-content-between">
        		<button class="btn btn-primary" data-target="#filter-modal" data-toggle="modal" type="button"><i class="fas fa-filter"></i> Filter Degree Admission Report</button>
        		{!! Form::open(['route' => 'report.degree.admission.generate', 'method'=> 'post', 'target' => '_blank']) !!}
						{!! Form::hidden('session', $session) !!}
						{!! Form::hidden('groups', $groups) !!}
						{!! Form::hidden('current_level', $current_level) !!}
						{!! Form::hidden('from_date', $from_date) !!}
						{!! Form::hidden('to_date', $to_date) !!}
						
					<div class="dropdown">
            <button type="button" class="btn btn-primary dropdown-toggle" id="reportDropdown"
              data-toggle="dropdown" aria-expanded="false">
              Download Report
            </button>
            <div class="dropdown-menu" aria-labelledby="reportDropdown" role="menu">
              <button class="dropdown-item" type="submit" name="type" value="pdf"><i class="fas fa-file-pdf"></i> Generate PDF</button>
              <button class="dropdown-item" type="submit" name="type" value="csv"><i class="fas fa-file-csv"></i> Generate CSV</button>
              <button class="dropdown-item" type="submit" name="type" value="csv_dept_report"><i class="fas fa-file-csv"></i> Generate Departmental Report</button>
            </div>
          </div>
				{!! Form::close() !!}
        </div>
			<br>

			@if($id != '' || $groups !='' || $current_level!='' || $session!='' || $from_date !='' || $to_date !='' || $admission_roll !='')
			<table class="table input-mark mb-0">
				<caption class="mb-0">
					Student Id: <span>{{ $id }}</span> 
					Admission Roll: <span>{{ $admission_roll }}</span> 
					Groups: <span>{{ $groups }}</span>
					Current Level: <span>{{ $current_level }}</span> 					
					Session: <span>{{ $session }}</span>
					From Date: <span>{{ $from_date }}</span>
					To Date: <span>{{ $to_date }}</span>
				</caption>
			</table>
			@endif

			@if($num_rows>0)
			 <h3> Total Number Of Student: {{$num_rows}}</h3> 
			@endif

			@if($admission_fee > 0 && $groups !='')
				<strong style="font-size: 16px;">Admission Fee : {{$admission_fee}}</strong><br/>
			@endif

			@if ($total_amount > 0)
				<strong style="font-size: 16px;">Total Amount : {{$total_amount}}</strong><br/>
			@endif

      	<table class="table table-hover defDTable table-striped w-full cell-border">
            <thead>
              <tr>
								<th>Student ID</th>
								<th>Session</th>
								<th>Class Roll</th>				
								<th>Name</th>				
								<th>Admission Roll</th>	
								<th>Groups</th>				
								<th>Current Level</th>	
								<th>Status</th>
							</tr>
            </thead>
            
            <tbody>

				@foreach($students as $college)

					<tr class="">
						<td>{{ $college->id }}</td>
						<td>{{ $college->session }}</td>
						<td>{{ $college->class_roll }}</td>					
						<td>{{ $college->name }}</td>
						<td>{{ $college->admission_roll }}</td>	
						<td>{{ $college->groups }}</td>
						<td>{{ $college->current_level }}</td>	
						<td>{{ $college->status }}</td>
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $students->appends(Request::except('page'))->links() }}

          {{-- end hsc_admission_index permission--}}
          @endcan
          
        </div>
      </div>

{{-- modal --}}
<div class="modal fade" id="filter-modal" aria-hidden="true" aria-labelledby="examplePositionSidebar" role="dialog" tabindex="-1">
  <div class="modal-dialog modal-simple">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title">Filter Degree Admission Report</h4>
      </div>
      <div class="modal-body">
        {!! Form::open(['route'=> 'report.degree.admission', 'method'=> 'post', 'class' => 'form-horizontal']) !!}
		  		<div class="form-group">
				    {!! Form::text('id', $id, ['class'=> 'form-control', 'placeholder' => 'Student ID']) !!}
				  </div>

				  <div class="form-group">
				    {!! Form::text('admission_roll', $admission_roll, ['class'=> 'form-control', 'placeholder' => 'Admission Roll']) !!}
				  </div>

			  	<div class="form-group">
				    {!! Form::select('groups', selective_degree_subjects(), $groups, ['class'=>'form-control group','id'=> 'groups', 'autocomplete'=> 'off']) !!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('current_level', selective_multiple_masters_level(), $current_level, ['class'=>'form-control group', 'autocomplete'=> 'off']) !!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('session', selective_multiple_session(), $session, ['class'=>'form-control session', 'autocomplete'=> 'off']) !!}
				  </div>

				  <div class="form-group">
					  <div class="input-daterange" data-plugin="datepicker">
              <div class="input-group mb-2">
                <span class="input-group-addon">
                  <i class="icon wb-calendar" aria-hidden="true"></i>
                </span>
                {!! Form::text('from_date', $from_date, ['class'=> 'form-control', 'placeholder' => 'From Date', 'autocomplete'=> 'off']) !!}
              </div>
              <div class="input-group">
                <span class="input-group-addon">to</span>
                {!! Form::text('to_date', $to_date, ['class'=> 'form-control', 'placeholder' => 'To Date', 'autocomplete'=> 'off']) !!}
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
@include('BackEnd.common.drop_down_js')
	<script>
		
	</script>
@endpush