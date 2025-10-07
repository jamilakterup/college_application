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
		    <p>
				{!! Form::checkbox('department-' . $department->id, $department->id) . ' ' . $department->name !!}
		    </p>
			@endforeach
		</label>
	</div>
  </div>
</div>

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('hsc_result.class.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>