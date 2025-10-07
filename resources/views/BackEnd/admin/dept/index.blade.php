@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Department Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item admission-menu">
	@include('BackEnd.admin.dept.particles.subMenu')
</div>

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('admin.dept.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Department</a></div>
          <h3 class="panel-title">Department Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover dataTable w-full cell-border">
            <thead>
              <tr>
					<th>Department Code</th>
					<th>Faculty</th>					
					<th>Department Name</th>
					<th>Programs Offered</th>				
					<th>Total Seat</th>				
					<th>Edit</th>
					<th>Delete</th>
				</tr>
            </thead>
            
            <tbody>

				@foreach($depts as $dept)

					<tr class="text-center {{ Study::updatedRow('id', $dept->id) }}">
						<td>{{ $dept->dept_code }}</td>
						<td>{{ link_to_route('admin.faculty.show', $dept->faculty->short_name, $dept->faculty->id, ['title' => $dept->faculty->faculty_name]) }}</td>						
						<td>{{ $dept->dept_name }}</td>
						<td>

							@foreach($dept->deptprograms as $deptprogram)

								@if($deptprogram->status == 1)
									{{ link_to_route('admin.program.show', $deptprogram->program->short_name, $deptprogram->program->id, ['class' => 'btn type-a', 'title' => $deptprogram->program->name]) }}
								@endif
									
							@endforeach

						</td>						
						<td>{{ $dept->seat }}</td>				
						<td><a href="{{ URL::route('admin.dept.edit', $dept->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
						<td>
							{{ Form::open(['route' => ['admin.dept.destroy', $dept->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $dept->id) }}
								<button type='submit' class='btn btn-danger type-b'><i class='fa fa-trash'></i></button>
							{{ Form::close() }}
						</td>
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $depts->links() }}
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