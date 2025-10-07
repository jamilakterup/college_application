@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'PaySlip Item Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item admission-menu">
	@include('BackEnd.admin.admission.particles.subMenu')
</div>

<div class="panel">
    <header class="panel-heading">
      <div class="panel-actions"><a href="{{ route('admin.payslip_item.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add PaySlip Item</a></div>
      <h3 class="panel-title">PaySlip Item Lists</h3>
    </header>
    <div class="panel-body">

      	{{ Form::open(['route' => 'admin.payslip_item.search', 'method' => 'post']) }}
	      	<div class="row row-lg">
		        <div class="form-group col-md-6">
		          {!! Form::select('payslipheader_id', selective_multiple_payslip_header(), null, ['class'=>'form-control payslipheader_id', 'autocomplete'=> 'off', 'data-plugin' => 'select2']) !!}
		        </div>
		        <div class="form-group col-md-3">
		          {{ Form::submit('Search', ['class' => 'btn btn-default']) }}

		        </div>
		    </div>
	    {!! Form::close() !!}

	    <table class="table table-hover dataTable w-full cell-border">
	        <thead>
	          	<tr>
								<th>PaySlip Header</th>
								<th>PaySlip Item</th>				
								<th>Edit</th>
								<th>Delete</th>
							</tr>
	        </thead>
	        
	        <tbody>
	            @foreach($payslip_items as $payslip_item)

							<tr class="text-center {{ Study::updatedRow('id', $payslip_item->id) }}">
								<td>{{ $payslip_item->payslipheader->title }}</td>
								<td>{{ $payslip_item->item }}</td>
								<td><a href="{{ route('admin.payslip_item.edit', $payslip_item->id) }}" class='edt'><i class='fad fa-pencil'></i></a></td>	
								<td>
									{{ Form::open(['route' => ['admin.payslip_item.destroy', $payslip_item->id], 'method' => 'delete', 'class' => 'delete']) }}
										{{ Form::hidden('id', $payslip_item->id) }}
										<button type='submit' class='btn btn-sm btn-danger type-b'><i class='fad fa-trash'></i></button>
									{{ Form::close() }}
								</td>
							</tr>	

						@endforeach
	        </tbody>
	    </table>
      {{ $payslip_items->links() }}
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