<form class='form-horizontal' action="{{route('admin.permission.store')}}" data-form='postForm'>
	<div class="form-group">
	  {{ Form::label('name', 'Name', ['class' => 'col-form-label']) }}

	    {{ Form::text('name', $name ?? null, ['class' => 'form-control form-control-sm', 'placeholder' => 'Enter Permission Name']) }}

	    <div class='invalid-feedback'></div>
	</div>

	<div class="form-group">
	  {{ Form::label('group_name', 'Group Name', ['class' => 'col-form-label']) }}

	    {{ Form::text('group_name', $group_name ?? null, ['class' => 'form-control form-control-sm', 'placeholder' => 'Enter Group Name']) }}

	    <div class='invalid-feedback'></div>
	</div>

	<div class="form-group">
	  {{ Form::label('parent_group_name', 'Parent Group Name', ['class' => 'col-form-label']) }}

	    {{ Form::text('parent_group_name', $parent_group_name ?? null, ['class' => 'form-control form-control-sm', 'placeholder' => 'Enter Parent Group Name']) }}

	    <div class='invalid-feedback'></div>
	</div>

	<div class="form-group">
	  {{ Form::label('guard_name', 'Guard Name', ['class' => 'col-form-label']) }}

	    {{ Form::select('guard_name',[''=> 'Select', 'web'=> 'Web', 'api'=> 'Api'], $guard_name ?? null, ['class' => 'form-control form-control-sm selectize', 'data-placeholder' => '<--Guard Name-->', 'id'=> 'guard_name']) }}

	    <div class='invalid-feedback'></div>
	</div>

	<div class="form-group">
	    {!! Form::submit('Save Data', ['class'=> 'btn btn-primary','data-value'=> 'create', 'data-button'=>'save']) !!}
	</div>
</form>