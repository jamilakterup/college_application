@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Program Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('admin.program.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Program</a></div>
          <h3 class="panel-title">Program Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover dataTable w-full cell-border">
            <thead>
              	<tr>
					<th>Program Code</th>
					<th>Name</th>
					<th>Short Name</th>
					<th>Timeline</th>				
					<th>Edit</th>
					<th>Delete</th>
				</tr>
            </thead>
            
            <tbody>

				@foreach($programs as $program)

				<tr class="text-center {{ Study::updatedRow('id', $program->id) }}">
					<td>{{ $program->code }}</td>
					<td>{{ $program->name }}</td>
					<td>{{ $program->short_name }}</td>
					<td>
						@if($program->timeline <= 1)
							{{ $program->timeline . ' year' }}
						@endif

						@if($program->timeline > 1)
							{{ $program->timeline . ' years' }}
						@endif
					</td>					
					<td><a href="{{ URL::route('admin.program.edit', $program->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
					<td>
						{{ Form::open(['route' => ['admin.program.destroy', $program->id], 'method' => 'delete', 'class' => 'delete']) }}
							{{ Form::hidden('id', $program->id) }}
							<button type='submit' class='btn btn-danger type-b'><i class='fa fa-trash'></i></button>
						{{ Form::close() }}
					</td>
				</tr>	

			@endforeach
            </tbody>
          </table>
          {{ $programs->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		$(document).ready(function() {
			var table = $('.dataTable').dataTable({
				"scrollX": true,
				"scrollX": "100%",
	            "autoWidth": false,
	            "scrollY": '60vh',
	            "searching" : true,
	            "lengthChange": false,
	            "bSort": false,
	            "responsive": true,
	            "paging": false
			});
		});
	</script>
@endpush