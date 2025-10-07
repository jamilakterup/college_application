@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'PaySlip Generator Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item admission-menu">
	@include('BackEnd.admin.admission.particles.subMenu')
</div>

<div class="panel">
    <div class="panel-body">

    	<a href="{{ route('admin.payslip_generator.create') }}" class="btn btn-primary"> <i class="fa fa-plus"></i> Add New</a>

      	<div class="col-md-12 d-flex justify-content-center">

			{!! Form::open(['route'=> 'admin.payslip_generator.search', 'method'=> 'post', 'class' => 'form-inline']) !!}
			  <div class="form-group">
			    {!! Form::text('title', session('payslip_title'), ['class'=> 'form-control', 'placeholder' => 'Enter payslip title']) !!}
			  </div>

			  <div class="form-group">
			    {!! Form::select('status', $status_lists, session('status'), ['class'=>'form-control']) !!}
						{!!invalid_feedback('status')!!}
			  </div>


			  <button type="submit" class="btn btn-info">Search</button>
			{!! Form::close() !!}

		</div>

	    <table class="table table-hover dataTable w-full cell-border">
	        <thead>
	          	<tr>
					<th>Serial No.</th>
					<th>PaySlip Title</th>
					<th>Total Fees (Taka)</th>
					<th>View</th>				
					<th>Status</th>				
					<th>Action</th>	
					<th>Edit</th>
					<th>Delete</th>
				</tr>
	        </thead>
	        
	        <tbody>
	        	<?php

					//$i = ($payslip_titles->getCurrentPage() - 1) * $payslip_titles->getPerPage();
				
				?>
	            @foreach($payslip_titles as $key=> $payslip_title)
	            	<?php //$i++; ?>

					<tr class="text-center {{ Study::updatedRow('id', $payslip_title->id) }}">
						<td>{{ $key+1 }}</td>
						<td>{{ $payslip_title->title }}</td>
						<td class='l-space'>
							<?php
								$total_fees = App\Models\PayslipGenerator::wherePaysliptitle_id($payslip_title->id)->sum('fees');
							?>

							<span>Tk.</span> {{ $total_fees }}
						</td>
						<td>{{ link_to_route('admin.payslip_generator.show', 'Details', $payslip_title->id, ['class' => 'btn btn-success type-b']) }}</td>					
						<td>
							@if($payslip_title->status == 1)
								Listed
							@endif
							
							@if($payslip_title->status == 0)
								Not Listed
							@endif	
						</td>					
						<td>
							@if($payslip_title->status == 1)
								{{ Form::open(['route' => ['admin.payslip_generator.status', $payslip_title->id], 'method' => 'put', 'class' => 'inline']) }}
									{{ Form::hidden('id', $payslip_title->id) }}
									{{ Form::hidden('status', 0) }}
									{{ Form::submit('Unlist', ['class' => 'btn btn-danger type-b'])}}
								{{ Form::close() }}
							@endif
							
							@if($payslip_title->status == 0)
								{{ Form::open(['route' => ['admin.payslip_generator.status', $payslip_title->id], 'method' => 'put', 'class' => 'inline']) }}
									{{ Form::hidden('id', $payslip_title->id) }}
									{{ Form::hidden('status', 1) }}
									{{ Form::submit('Add on List', ['class' => 'btn btn-success type-b'])}}
								{{ Form::close() }}						
							@endif	
						</td>
						<td><a href="{{ URL::route('admin.payslip_generator.edit', $payslip_title->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
						<td>
							{{ Form::open(['route' => ['admin.payslip_generator.destroy', $payslip_title->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $payslip_title->id) }}
								<button type='submit' class='del type-b btn btn-danger'><i class='fad fa-trash'></i></button>
							{{ Form::close() }}
						</td>
					</tr>

				@endforeach
	        </tbody>
	    </table>
      {{ $payslip_titles->links() }}
    </div>
  </div>

@endsection

@push('scripts')
	<script>
		$(document).ready(function() {
			var dataTable = $('.dataTable').dataTable({
	            "searching" : false,
	            "lengthChange": false,
	            "bSort": false,
	            "responsive": true,
	            "scrollY": '60vh',
	            "paging" : false
			});
		});
	</script>
@endpush