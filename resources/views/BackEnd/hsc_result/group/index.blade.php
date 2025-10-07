@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Faculty Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item header-menu">
	@include('BackEnd.hsc_result.class.particles.subMenu')
</div>

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('hsc_result.group.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Department</a></div>
          <h3 class="panel-title">Department Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              <tr>
					<th>Department Id</th>			
					<th>Department</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
            </thead>
            
            <tbody>
	        	@foreach($departments as $department)

					<tr class="text-center {{ Ecm::updatedRow('id', $department->id) }}">
						<td>{{ $department->id }}</td>
						<td>{{ $department->name }}</td>					
						<td><a href="{{ URL::route('hsc_result.group.edit', $department->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
						<td>
							{{ Form::open(['route' => ['hsc_result.group.destroy', $department->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $department->id) }}
								<button type='submit' class='btn btn-danger type-b'><i class='fa fa-trash'></i></button>
							{{ Form::close() }}
						</td>
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $departments->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush