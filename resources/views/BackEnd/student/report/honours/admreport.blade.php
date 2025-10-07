@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Honours Admission Report Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <div class="panel-body">

				@can('honours.admission.index')
				{!! Form::open(['route'=> 'report.honours.admission', 'method'=> 'post', 'class' => 'form-inline d-flex justify-content-center']) !!}

        <div class="form-group">
          {!! Form::text('id', $id ?? null, ['class'=> 'form-control form-control-sm', 'placeholder' => 'Student ID']) !!}
        </div>

        <div class="form-group">
          {!! Form::text('admission_roll', $admission_roll ?? null, ['class'=> 'form-control form-control-sm', 'placeholder' => 'Admission Roll']) !!}
        </div>

        @if($faculty != '')
				  @php
				  	$faculties = DB::table('faculties')->where('faculty_name', $faculty)->first();
				  	$query = DB::table('departments')->where('faculty_id', $faculties->id);
				  	query_has_permissions($query, ['dept_name']);
				  	$departments = $query->pluck('dept_name', 'dept_name')->toArray();
				  @endphp
			  	<div class="form-group">
				    {!! Form::select('faculty', selective_faculties(), $faculty ?? null, ['class'=>'form-control facult form-control-sm','id'=> 'faculty', 'autocomplete'=> 'off']) !!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('dept_name', $departments, $dept_name ?? null, ['class'=>'form-control dept form-control-sm', 'autocomplete'=> 'off', 'id'=> 'depti']) !!}
				  </div>
			  @else
				  <div class="form-group">
				    {!! Form::select('faculty', selective_faculties(), $faculty ?? null, ['class'=>'form-control facult form-control-sm','id'=> 'faculty', 'autocomplete'=> 'off']) !!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('dept_name', [''=>'>--Select Department--<'], $dept_name ?? null, ['class'=>'form-control dept form-control-sm', 'autocomplete'=> 'off', 'id'=> 'depti']) !!}
				  </div>
			  @endif

        <div class="form-group">
				    {!! Form::select('current_level', selective_multiple_honours_level(), $current_level ?? null, ['class'=>'form-control form-control-sm current_level selectize', 'autocomplete'=> 'off']) !!}
        </div>

        <div class="form-group">
          {!! Form::select('session', selective_multiple_session(), $session ?? null, ['class'=>'form-control form-control-sm session selectize', 'autocomplete'=> 'off', 'data-placeholder'=> '>--Select Session--<']) !!}
							{!!invalid_feedback('session')!!}
        </div>

        <div class="form-group">
				  <div class="input-daterange" data-plugin="datepicker">
            <div class="input-group mb-2">
              <span class="input-group-addon">
                <i class="icon wb-calendar" aria-hidden="true"></i>
              </span>
              {!! Form::text('from_date', $from_date ?? null, ['class'=> 'form-control', 'placeholder' => 'From Date', 'autocomplete'=> 'off']) !!}
            </div>
            <div class="input-group">
              <span class="input-group-addon">to</span>
              {!! Form::text('to_date', $to_date ?? null, ['class'=> 'form-control', 'placeholder' => 'To Date', 'autocomplete'=> 'off']) !!}
            </div>
          </div>
			  </div>

        <div class="form-group">
            {!! Form::submit('Search', ['class' => 'btn btn-sm btn-primary']) !!}
        </div>
      {!! Form::close() !!}

      @if($id != '' || $faculty !='' || $dept_name !='' || $current_level!='' || $session!='' || $from_date !='' || $to_date !='' || $admission_roll!='')
			<table class="table input-mark mb-0">
				<caption class="mb-0">
					Student Id: <span>{{ $id }}</span> 
					Admission Roll: <span>{{ $admission_roll }}</span> 
					Faculty: <span>{{ $faculty }}</span> 
					Department: <span>{{ $dept_name }}</span> 
					Current Level: <span>{{ $current_level }}</span> 					
					Session: <span>{{ $session }}</span>
					From Date: <span>{{ $from_date }}</span>
					To Date: <span>{{ $to_date }}</span>
				</caption>
			</table>
			@endif

        	<div class="d-flex justify-content-end">
        		{!! Form::open(['route' => 'report.honours.admission.generate', 'method'=> 'post', 'target' => '_blank']) !!}
						{!! Form::hidden('id', $id) !!}
						{!! Form::hidden('admission_roll', $admission_roll) !!}
						{!! Form::hidden('session', $session) !!}
						{!! Form::hidden('faculty', $faculty) !!}
						{!! Form::hidden('dept_name', $dept_name) !!}
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
              <button class="dropdown-item" type="submit" name="type" value="report_excel"><i class="fas fa-file-excel"></i> Generate Department Wise Report</button>
            </div>
          </div>
				{!! Form::close() !!}
        </div>

			@if($num_rows>0)
			 <h3> Total Number Of Student: {{$num_rows}}</h3> 
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
								<th>Faculty</th>		
								<th>Department</th>					
								<th>Current Level</th>	
								<th>Transaction ID</th>
								<th>Total Amount</th>
								<th>Payment Date</th>
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
						<td>{{ $college->faculty_name }}</td>				
						<td>{{ $college->dept_name }}</td>					
						<td>{{ $college->current_level }}</td>	
						<td>{{ $college->transaction_id }}</td>
						<td>{{ $college->total_amount }}</td>
						<td>{{ $college->date }}</td>
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $students->appends(Request::except('page'))->links() }}

          {{-- end hsc_admission_index permission--}}
          @endcan
          
        </div>
      </div>
@endsection

@push('scripts')
@include('BackEnd.common.drop_down_js')
	<script>
		
	</script>
@endpush