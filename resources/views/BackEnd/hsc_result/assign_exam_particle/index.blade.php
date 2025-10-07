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
          <h3 class="panel-title">Exam Assign Particle Lists</h3>
        </header>
        <div class="panel-body">

	        @foreach($classes as $class)	

				<?php 
					$class_departments = App\Models\ClassGroup::where('classe_id',$class->id)->orderBy('group_id')->get();
					$departments_count = 3;
				?>	
				
				@if($departments_count > 0)	

					@foreach($class_departments as $class_department)

			          <center><caption class="text-center">Class: {{ $class->name }} ({{ $class_department->group->name }})</caption></center>
			          <table class="table table-hover defDTable w-full cell-border">
			            <thead>
			              	<tr>
								<th>Subject</th>
								<th>Exam Particle</th>
								<th>Total</th>
								<th>Pass</th>
								<th>Percentages</th>							
								<th>Assign / Edit</th>
								<th>Unassign All</th>
							</tr>
			            </thead>

			            <?php
							$subjects = App\Models\ClassSubject::where('classe_id',$class->id)->where('group_id',$class_department->group_id)->get();
						?>
			            
			            <tbody>
				        	@foreach($subjects as $subject)
								<?php
									$exam_particles = App\Models\ConfigExamParticle::where('classe_id',$class->id)->where('group_id',$class_department->group_id)->where('subject_id',$subject->subject_id)->get();
									$total_exam_particles = $exam_particles->count();
									$i = 0;							
								?>

								@if(Session::has('department_id') && Session::has('subject_id'))
									@if(Session::get('department_id') == $class_department->department_id && Session::get('subject_id') == $subject->subject_id)
										<?php $flash = true; ?>
									@else
										<?php $flash = false; ?>								
									@endif
								@else
									<?php $flash = false; ?>
								@endif								
								
								@if($total_exam_particles == 0)
									<tr class="text-center @if($flash == true) {{ Ecm::updatedRow('id', $class->id) }} @endif">
										<td>{{ $subject->subject->name }}({{$subject->subject->code}})</td>
										<td></td>
										<td></td>
										<td></td>
										<td><a href="{{ URL::route('hsc_result.assign_exam_particle.edit', [$class->id, $class_department->group_id, $subject->subject_id]) }}" class='edt'><i class='fa fa-plug'></i></a></td>
										<td>
											{{ Form::open(['route' => ['hsc_result.assign_exam_particle.destroy', $class->id, $class_department->group_id, $subject->subject_id], 'method' => 'delete', 'class' => 'delete']) }}
												{{ Form::hidden('class_id', $class->id) }}
												{{ Form::hidden('department_id', $class_department->department_id) }}
												{{ Form::hidden('subject_id', $subject->subject_id) }}
												<button type='submit' class='del'><i class='fa fa-eraser'></i></button>
											{{ Form::close() }}											
										</td>
									</tr>
								@endif

								@if($total_exam_particles > 0)
									@foreach($exam_particles as $exam_particle)

										<tr class="text-center @if($flash == true) {{ Ecm::updatedRow('id', $class->id) }} @endif">
											<?php $i++; ?>
											@if($i == 1)
												<td rowspan="{{ $total_exam_particles }}">{{ $subject->subject->name }} ({{$subject->subject->code}})</td>
											@endif	
											<td>{{ $exam_particle->xmparticle->name }}</td>
											<td>{{ $exam_particle->total }}</td>
											<td>{{ $exam_particle->pass }}</td>
											<td>{{ $exam_particle->per_centage }}</td>
										
											@if($i == 1)
												<td rowspan="{{ $total_exam_particles }}"><a href="{{ URL::route('hsc_result.assign_exam_particle.edit', [$class->id, $class_department->group_id, $subject->subject_id]) }}" class='edt'><i class='fa fa-plug'></i></a></td>
												<!--td rowspan="{{ $total_exam_particles }}">
													{{ Form::open(['route' => ['hsc_result.assign_exam_particle.destroy', $class->id, $class_department->group_id, $subject->subject_id], 'method' => 'delete', 'class' => 'delete']) }}
														{{ Form::hidden('class_id', $class->id) }}
														{{ Form::hidden('department_id', $class_department->group_id) }}
														{{ Form::hidden('subject_id', $subject->subject_id) }}
														<button type='submit' class='del'><i class='fa fa-eraser'></i></button>
													{{ Form::close() }}											
												</td-->
											@endif
										</tr>	

									@endforeach	
								@endif	

							@endforeach
			            </tbody>
			          </table>
	          @endforeach

				@endif

			@endforeach	
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush