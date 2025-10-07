@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Faculty Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item header-menu">
	@include('BackEnd.hsc_result.subject.particles.subMenu')
</div>

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('hsc_result.subject.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Subject</a></div>
          <h3 class="panel-title">Subject Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              	<tr>
					<th>Class</th>
					<th>Department</th>
					<th style='width: 50%'>Subject</th>
					<th>Assign / Edit</th>
					<th>Unassign All</th>
				</tr>
            </thead>
            
            <tbody>
	        	@foreach($classes as $class)

					<?php 
						$class_departments = App\Models\ClassGroup::where('classe_id',$class->id)->orderBy('group_id')->get();				
						$departments_count = $class->classedepartments->count();
					?>

					@if($departments_count > 0)			
						@foreach($class_departments as $class_department)

							@if(Session::has('department_id'))
								@if(Session::get('department_id') == $class_department->group_id)
									<?php $flash = true;?>
								@else
									<?php $flash = false; ?>								
								@endif
							@else
								<?php $flash = false; ?>
							@endif						

							<tr class="text-center @if($flash == true) {{ Ecm::updatedRow('id', $class->id) }}@endif" >
								<td>{{ ucfirst($class->name) }}</td>					
								<td>{{ $class_department->group->name }}</td>
								<td style='text-align: left'>
									<?php
										$subjects = App\Models\ClassSubject::where('classe_id',$class->id)->where('group_id',$class_department->group_id)->orderBy('id')->get();
										$i = 0;
									?>
									@if($subjects->count() > 0)
										@foreach($subjects as $subject)
											<?php $i++; ?>
											<span class='subject'>{!! '<i>' . $i . '.</i> ' . $subject->subject->name.' ('.$subject->subject->code.')'!!}</span>
										@endforeach
									@endif
								</td>
								<td><a href="{{ URL::route('hsc_result.assign_subject.edit', [$class->id,$class_department->group_id]) }}" class='edt'><i class='fa fa-plug'></i></a></td>
								<td>
									{{ Form::open(['route' => ['hsc_result.assign_subject.destroy', $class->id, $class_department->group_id], 'method' => 'delete', 'class' => 'delete']) }}
										{{ Form::hidden('class_id', $class->id) }}
										{{ Form::hidden('department_id', $class_department->group_id) }}
										<button type='submit' class='del'><i class='fa fa-eraser'></i></button>
									{{ Form::close() }}
								</td>
							</tr>	
								
						@endforeach		
					@endif			

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