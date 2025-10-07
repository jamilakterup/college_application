@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Class Management')

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
          <div class="panel-actions"><a href="{{ route('hsc_result.class.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Class</a></div>
          <h3 class="panel-title">Class Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              <tr>
					<th>Class Id</th>			
					<th>Class</th>
					<th>Group</th>						
					<th>Edit</th>
					<th>Delete</th>
				</tr>
            </thead>
            
            <tbody>
	           @foreach($classes as $class)

					<tr class="text-center {{ Ecm::updatedRow('id', $class->id) }}">
						<td>{{ $class->id }}</td>
						<td>{{ ucfirst($class->name) }}</td>
						<td><?php

								$departments_count = $class->classedepartments->count();
								$classedepartments = App\Models\ClassGroup::where('classe_id',$class->id)->orderBy('group_id')->get();
								$i = 0;
							?>
							@if($departments_count > 0)						
								@foreach($classedepartments as $class_department)
									<?php $i++; ?>
									@if($i == $departments_count)
										{{ $class_department->group->name }}
									@else
										{{ $class_department->group->name . ' , ' }}
									@endif
								@endforeach		
							@endif			</td>				
						
						<td><a href="{{ URL::route('hsc_result.class.edit', $class->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
						<td>
							{{ Form::open(['route' => ['hsc_result.class.destroy', $class->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $class->id) }}
								<button type='submit' class='btn btn-danger type-b'><i class='fa fa-trash'></i></button>
							{{ Form::close() }}
						</td>
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $classes->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush