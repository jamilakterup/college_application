@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Course Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item admission-menu">
	@include('BackEnd.admin.course.particles.subMenu')
</div>

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('admin.course.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Course</a></div>
          <h3 class="panel-title">Course Lists</h3>
        </header>
        <div class="panel-body">

        	<div class="col-md-12 d-flex justify-content-center">

				{!! Form::open(['route'=> 'student.hsc.promotion.invoice', 'method'=> 'post', 'class' => 'form-inline']) !!}
				  <div class="form-group">
				    {!! Form::select('code', create_option_array('courses', 'id', 'name', 'Course'), session('code'), ['class'=>'form-control group', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('code')!!}
				  </div>

				  <div class="form-group">
				    {!! Form::select('department_id', create_option_array('departments', 'id', 'dept_name', 'Department'), session('department_id'), ['class'=>'form-control group', 'autocomplete'=> 'off']) !!}
							{!!invalid_feedback('department_id')!!}
				  </div>

				  <div class="form-group">
					  <select name='level' class='form-control'>
							<option value=''>Study Level</option> 

							@if($levels->count() > 0)
								@foreach($levels as $level)
									<option value="{{ $level->level }}">{{ Study::level($level->level) . ' year' }}</option>
								@endforeach
							@endif
						</select>
					</div>
				  <div class="form-group">
						<select name='session' class='form-control'>
							<option value=''>Session</option> 

							@if($sessions->count() > 0)
								@foreach($sessions as $session)
									<option value="{{ $session->session }}">{{ $session->session }}</option>
								@endforeach
							@endif
						</select>
					</div>

				  <button type="submit" class="btn btn-info">Search</button>
				{!! Form::close() !!}

			</div>
			<br>
          <table class="table table-hover defDTable w-full cell-border text-center">
            <thead>
              <tr>
					<th>Course Code</th>
					<th>Course Name</th>					
					<th>Session</th>
					<th>Mark</th>				
					<th>Department</th>		
					<th>Program</th>	
					<th>Course Type</th>				
					<th>Study Level</th>											
					<th>Edit</th>
					<th>Delete</th>
				</tr>
            </thead>
            
            <tbody>
	            @foreach($courses as $course)

					<tr class="text-center {{ Study::updatedRow('id', $course->id) }}">
						<td>{{ $course->code }}</td>
						<td>{{ $course->name }}</td>	
						<td>{{ $course->session }}</td>	
						<td>{{ $course->mark }}</td>
						<td>{{ $course->department->dept_name }}</td>		
						<td>{{ $course->program->name }}</td>
						<td>
							@if($course->type == 0)
								Optional
							@endif

							@if($course->type == 1)
								Major
							@endif
						</td>	
						<td>{{ Study::level($course->level) . ' year' }}</td>																					
						<td><a href="{{ URL::route('admin.course.edit', $course->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
						<td>
							{{ Form::open(['route' => ['admin.course.destroy', $course->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $course->id) }}
								<button type='submit' class='btn btn-danger type-b'><i class='fa fa-trash'></i></button>
							{{ Form::close() }}
						</td>
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $courses->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush