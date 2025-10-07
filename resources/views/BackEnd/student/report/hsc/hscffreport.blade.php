@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'HSC Formfillup Report Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <div class="panel-body">

				@can('hsc.admission.index')
			{!! Form::open(['route'=> 'report.hsc.ff', 'method'=> 'post', 'class' => 'form-inline d-flex justify-content-center']) !!}

        <div class="form-group">
          {!! Form::text('registraion_id', $registraion_id, ['class'=> 'form-control form-control-sm', 'placeholder' => 'Registration ID']) !!}
        </div>

        <div class="form-group">
          {!! Form::select('groups', selective_faculties(), $groups, ['class'=>'form-control form-control-sm group selectize', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('groups')!!}
        </div>

        <div class="form-group">
				    {!! Form::select('current_level', selective_multiple_hsc_level(), $current_level, ['class'=>'form-control form-control-sm current_level selectize', 'autocomplete'=> 'off', 'required' => true]) !!}
        </div>

        <div class="form-group">
          {!! Form::select('session', selective_multiple_session(), $session, ['class'=>'form-control form-control-sm session selectize', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('session')!!}
        </div>

        <div class="form-group">
					{!! Form::select('formfillup_type', selective_formfillup_type(), $formfillup_type, ['class'=>'form-control form-control-sm formfillup_type selectize', 'autocomplete'=> 'off']) !!}
					{!!invalid_feedback('formfillup_type')!!}
        </div>

        <div class="form-group">
          {!! Form::select('exam_year', selective_multiple_exam_year(), $exam_year, ['class'=>'form-control form-control-sm exam_year selectize', 'autocomplete'=> 'off', 'required'=> true]) !!}
					{!!invalid_feedback('exam_year')!!}
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

        <div class="form-group">
            {!! Form::submit('Search', ['class' => 'btn btn-sm btn-primary']) !!}
        </div>
      {!! Form::close() !!}

			<div class="d-flex justify-content-between">
				<div>
						@if($num_rows>0)
						 <h3> Total Number Of Student: {{$num_rows}}</h3> 
						@endif

						@if ($total_amount > 0)
							<strong style="font-size: 16px;">Total Amount : {{$total_amount}}</strong><br/>
						@endif
				</div>

        	{!! Form::open(['route' => 'report.hsc.ff.generate', 'method'=> 'post', 'target' => '_blank']) !!}
						{!! Form::hidden('session', $session) !!}
						{!! Form::hidden('groups', $groups) !!}
						{!! Form::hidden('current_level', $current_level) !!}
						{!! Form::hidden('exam_year', $exam_year) !!}
						{!! Form::hidden('formfillup_type', $formfillup_type) !!}
						{!! Form::hidden('from_date', $from_date) !!}
						{!! Form::hidden('to_date', $to_date) !!}
					<div class="dropdown">
            <button type="button" class="btn btn-primary dropdown-toggle" id="reportDropdown"
              data-toggle="dropdown" aria-expanded="false">
              Download Report
            </button>
            <div class="dropdown-menu" aria-labelledby="reportDropdown" role="menu">
              <button class="dropdown-item" name="type" type="submit" value="pdf"><i class="fas fa-file-pdf"></i> PDF</button>

              <button class="dropdown-item" name="type" type="submit" value="csv"><i class="fas fa-file-csv"></i> Student CSV</button>

              <button class="dropdown-item" name="type" type="submit" value="report_excel"><i class="fas fa-file-spreadsheet"></i> Indivisual/Consulated/Groups</button>
            </div>
          </div>
					{!! Form::close() !!}
        </div>

      	<table class="table table-hover defDTable table-striped w-full cell-border">
            <thead>
              <tr>
								<th>Registration ID</th>
								<th>Student Name</th>
								<th>Session</th>
								<th>Level Of Study</th>				
								<th>Groups</th>
								<th>Reg Type</th>		
								<th>Transaction ID</th>					
								<th>Registration Type</th>					
								<th>Total Amount</th>					
								<th>Exam Year</th>	
								<th>Paid Date</th>	
							</tr>
            </thead>
            
            <tbody>

							@foreach($students as $college)
								<tr class="{{ Study::updatedRow('id', $college->id) }}">
									<td>{{ $college->id }}</td>
									<td>{{ $college->name }}</td>
									<td>{{ $college->session }}</td>
									<td>{{ $college->level_study }}</td>					
									<td>{{ $college->groups }}</td>			
									<td>{{ $college->formfillup_type }}</td>				
									<td>{{ $college->transaction_id }}</td>					
									<td>{{ $college->formfillup_type }}</td>					
									<td>{{ $college->total_amount }}</td>					
									<td>{{ $college->exam_year }}</td>	
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
	<script>

		
	</script>
@endpush