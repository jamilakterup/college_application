@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Department Head Management')

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
          <div class="panel-actions"><a href="{{ route('admin.dept_head.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Department Head</a></div>
          <h3 class="panel-title">Department Head Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border text-center">
            <thead>
              	<tr>
					<th>Name</th>
					<th>Department</th>
					<th>Starting Date</th>
					<th>End Date</th>
					<th>Status</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
            </thead>
            <tbody>
	           @foreach($dept_heads as $dept_head)

				<tr class="text-center {{ Study::updatedRow('id', $dept_head->id) }}">
					<td>{{ $dept_head->name }}</td>
					<td>{{ $dept_head->department->dept_name }}</td>
					<td>{{ $dept_head->starting_date }}</td>
					<td>{{ $dept_head->end_date }}</td>
					<td>
						@if($dept_head->status === 1)
							<span>active</span>
						@endif

						@if($dept_head->status === 0)
							<span>inactive</span>
						@endif
					</td>										
					<td><a href="{{ URL::route('admin.dept_head.edit', $dept_head->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
					<td>
						{{ Form::open(['route' => ['admin.dept_head.destroy', $dept_head->id], 'method' => 'delete', 'class' => 'delete']) }}
							{{ Form::hidden('id', $dept_head->id) }}
							<button type='submit' class='btn btn-danger type-b'><i class='fa fa-trash'></i></button>
						{{ Form::close() }}
					</td>
				</tr>	

			@endforeach

            </tbody>
          </table>
          {{ $dept_heads->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush