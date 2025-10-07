@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Exam Setup Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item header-menu">
	@include('BackEnd.hsc_result.exam.particles.subMenu')
</div>

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('hsc_result.class_test.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Class Test</a></div>
          <h3 class="panel-title">Class Test Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              	<tr>
					<th>Class Test Id</th>			
					<th>Class Test</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
            </thead>
            
            <tbody>
	        	@foreach($class_tests as $class_test)

					<tr class="text-center {{ Ecm::updatedRow('id', $class_test->id) }}">
						<td>{{ $class_test->id }}</td>
						<td>{{ $class_test->name }}</td>					
						<td><a href="{{ URL::route('hsc_result.class_test.edit', $class_test->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
						<td>
							{{ Form::open(['route' => ['hsc_result.class_test.destroy', $class_test->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $class_test->id) }}
								<button type='submit' class='del'><i class='fa fa-trash'></i></button>
							{{ Form::close() }}
						</td>
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $class_tests->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush