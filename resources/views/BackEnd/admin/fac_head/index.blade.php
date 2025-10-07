@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Faculty Head Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item admission-menu">
	@include('BackEnd.admin.faculty.particles.subMenu')
</div>

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('admin.fac_head.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Faculty Head</a></div>
          <h3 class="panel-title">Faculty Head Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              	<tr>
					<th>Name</th>
					<th>Faculty</th>
					<th>Starting Date</th>
					<th>End Date</th>
					<th>Status</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
            </thead>
            <tbody>
	            @foreach($faculty_heads as $faculty_head)
					<tr class="text-center {{ Study::updatedRow('id', $faculty_head->id) }}">
						<td>{{ $faculty_head->name }}</td>
						<td>{{ $faculty_head->faculty->faculty_name }}</td>
						<td>{{ $faculty_head->starting_date }}</td>
						<td>{{ $faculty_head->end_date }}</td>
						<td>
							@if($faculty_head->status === 1)
								<span>active</span>
							@endif

							@if($faculty_head->status === 0)
								<span>inactive</span>
							@endif
						</td>										
						<td><a href="{{ URL::route('admin.fac_head.edit', $faculty_head->id) }}" class='edt'><i class='fad fa-pencil'></i></a></td>	
						<td>
							{{ Form::open(['route' => ['admin.fac_head.destroy', $faculty_head->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $faculty_head->id) }}

								<button type='submit' class='btn btn-sm btn-danger type-b'><i class='fad fa-trash'></i></button>
							{{ Form::close() }}
						</td>
					</tr>	

				@endforeach

            </tbody>
          </table>
          {{ $faculty_heads->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush