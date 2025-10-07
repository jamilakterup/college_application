<div class="form-group row">
  {{ Form::label('name', 'Class Name', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter class name']) }}

    {!!invalid_feedback('name')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('group', 'Group', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

  	<div class="border p-2 checkbox-group">
	   	<label class="checkbox-inline">
		    @foreach($departments as $department)
					<?php

						$checked = '';

						if(isset($department->id)) :	

							//check having the department in the class
							$has_department = App\Models\ClassGroup::where('classe_id',$class->id)->where('group_id',$department->id)->count();									
							if($has_department > 0) :
								$checked = 'checked';
							endif;	

						endif;

					?>

					@if($checked == '')
						{!! Form::checkbox('department-' . $department->id, $department->id) . ' ' . $department->name !!}
					@endif

					@if($checked == 'checked')
						{!! Form::checkbox('department-' . $department->id, $department->id, true) . ' ' . $department->name !!}			
					@endif
			@endforeach
		</label>
	</div>
  </div>
</div>

@if(isset($class->id))
	{{ Form::hidden('id', $class->id) }}
@endif

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('hsc_result.class.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>