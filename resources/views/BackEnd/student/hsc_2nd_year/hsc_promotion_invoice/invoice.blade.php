@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Hsc 2nd Year Admission Invoice Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <header class="panel-heading">
          <h3 class="panel-title">Hsc 2nd Year Admission Invoice List</h3>
        </header>
        <div class="panel-body">

        	<div class="col-md-12 d-flex justify-content-center">

				{!! Form::open(['route'=> 'student.hsc.promotion.invoice', 'method'=> 'post', 'class' => 'form-inline']) !!}
				  <div class="form-group">
				    {!! Form::text('student_id', session('student_id'), ['class'=> 'form-control', 'placeholder' => 'Student ID']) !!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('dept_name', selective_multiple_study_group(), session('dept_name'), ['class'=>'form-control group', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('dept_name')!!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('exam_year', selective_multiple_exam_year(), session('exam_year'), ['class'=>'form-control exam_year', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('exam_year')!!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('status',['Pending'=> 'Pending', 'Paid'=> 'Paid'] , session('status'), ['class'=>'form-control', 'placeholder'=> '--Select Payment Status--']) !!}
							{!!invalid_feedback('status')!!}
				  </div>


				  <button type="submit" class="btn btn-info">Search</button>
				{!! Form::close() !!}

			</div>
			<br>

			@if (count($num_rows))
				{!!'<h3> Total Number Of Student: '.count($num_rows).'</h3>'!!}
			@endif
		{!! Form::open(['route'=> 'student.hsc.promotion.invoice.action', 'id'=> 'invoice-form']) !!}
          <table class="table table-hover defDTable table-striped w-full cell-border">
            <thead>
              <tr>
              		<th><input type="checkbox" id="check-all" /> All</th>
					<th>Student ID</th>
					<th>Student Name</th>
					<th>Session</th>				
					<th>Exam Year</th>
					<th>Department</th>
					<th>Total Amount</th>			
					<th>Status</th>
					<th>Paid Date</th>	
				</tr>
            </thead>
            
            <tbody>

            	@foreach ($invoices as $invoice)
            		<tr>
            			<td><input type="checkbox" name="student_ids[]" value="{{$invoice->roll}}" /></td>
            			<td>{{$invoice->roll}}</td>
            			<td>{{$invoice->student_info_hsc->name}}</td>
            			<td>{{$invoice->admission_session}}</td>
            			<td>{{$invoice->passing_year}}</td>
            			<td>{{$invoice->pro_group}}</td>
            			<td>{{$invoice->total_amount}}</td>
            			<td>{!! $invoice->status == 'Pending'? "<span class='badge badge-danger'>{$invoice->status}</span>": "<span class='badge badge-success'>{$invoice->status}</span>" !!}</td>
            			<td>{{ $invoice->status == 'Pending' ? '': date('d-F-Y', strtotime($invoice->update_date))}}</td>
            		</tr>
            	@endforeach
            </tbody>
          </table>
          <button type="submit" class="btn btn-danger float-right invoice-delete" name="action_type" value="delete">Delete</button>
          {{-- <input type="submit" class="btn btn-danger float-right invoice-delete" name="action_type" value="delete"> --}}

          {!! Form::close() !!}

          {{ $invoices->appends(Request::except('page'))->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>

		$(document).ready(function() {
			$('#check-all').checkAll();
		});

		$(document).on('click', '.invoice-delete',function(event) {
			elem = $("input[name*='student_ids']:checked");

			var student_ids = elem.map(function() {
			    return $(this).val();
			}).get().join(', ');

			event.preventDefault();
			var form = $(this).parents('form');

			if(elem.length > 0){
				form.append(jQuery('<input>', {
			        'name': 'action_type',
			        'value': 'delete',
			        'type': 'hidden'
			      }));
			}else{
				$("input[name='action_type']").remove();
			}

			Swal.fire({
			  title: 'Are you sure?',
			  text: `You won't be able to revert this student id- ${student_ids}!`,
			  icon: 'warning',
			  showCancelButton: true,
			  confirmButtonColor: '#3085d6',
			  cancelButtonColor: '#d33',
			  confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
			  if (result.isConfirmed) {
			    form.submit();
			  }
			})
		});
		
	</script>
@endpush