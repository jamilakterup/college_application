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
          <h3 class="panel-title">Exam Particle Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              	<tr>
					<th>Class</th>
					<th style='width: 50%'>Exam</th>
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
								$exams = App\Models\ClassExam::where('classe_id',$class->id)->orderBy('exam_id')->get();
							?>
							@if($exams->count() > 0)
								@foreach($exams as $exam)
									<span class='btn btn-type-e'>{{ $exam->exam->name }}</span>
								@endforeach
							@endif
						</td>
						<td><a href="{{ URL::route('hsc_result.assign_exam.edit', $class->id) }}" class='edt'><i class='fa fa-plug'></i></a></td>	
						<td>
							{{ Form::open(['route' => ['hsc_result.assign_exam.destroy', $class->id], 'method' => 'delete', 'class' => 'delete']) }}
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