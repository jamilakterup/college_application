@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Faculty Management')

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
          <div class="panel-actions"><a href="{{ route('admin.faculty.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Faculty</a></div>
          <h3 class="panel-title">Faculty Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              <tr>
					<th>Faculty Code</th>
					<th>Faculty Name</th>
					<th>Short Name</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
            </thead>
            
            <tbody>
	            @foreach($faculties as $faculty)

					<tr class="text-center {{ Study::updatedRow('id', $faculty->id) }}">
						<td>{{ $faculty->faculty_code }}</td>
						<td>{{ $faculty->faculty_name }}</td>
						<td>{{ $faculty->short_name }}</td>
						<td><a href="{{ URL::route('admin.faculty.edit', $faculty->id) }}" class='edt'><i class='fad fa-pencil'></i></a></td>	
						<td>
							{{ Form::open(['route' => ['admin.faculty.destroy', $faculty->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $faculty->id) }}
								<button type='submit' class='btn btn-sm btn-danger type-b'><i class='fad fa-trash'></i></button>
							{{ Form::close() }}
						</td>
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $faculties->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush