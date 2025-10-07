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
          <h3 class="panel-title">Assign Class Test</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              	<tr>
					<th>Exam Name</th>
					<th style='width: 50%'>Class Test</th>
					<th>Assign / Edit</th>
					<th>Unassign All</th>
				</tr>
            </thead>
            
            <tbody>
	        	@foreach($classes as $class)

					<tr class="text-center {{ Ecm::updatedRow('id', $class->id) }}">
						<td>{{ ucfirst($class->name) }}</td>					
						<td>
							<?php
								$exams = App\Models\ClassTestExam::where('exam_id',$class->id)->orderBy('class_test_id')->get();
								
							?>
							@if($exams->count() > 0)
								@foreach($exams as $exam)
									<span class='btn btn-type-e'>{{ $exam->exam->name }}</span>
								@endforeach
							@endif
						</td>
						<td><a href="{{ URL::route('hsc_result.assign_class_test.edit', $class->id) }}" class='edt'><i class='fa fa-plug'></i></a></td>	
						<td>
							{{ Form::open(['route' => ['hsc_result.assign_class_test.destroy', $class->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $class->id) }}
								<button type='submit' class='del'><i class='fa fa-eraser'></i></button>
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