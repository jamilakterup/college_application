<div class="form-group row">
    {{ Form::label('libraryuser_id', 'User Type', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::select('libraryuser_id', $libraryuser_lists, NULL, ['class' => 'form-control']) }}
        {!!invalid_feedback('libraryuser_id')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('full_name', 'Full Name', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('full_name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter full name']) }}
        {!!invalid_feedback('full_name')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('date_of_birth', 'Date Of Birth', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('date_of_birth', NULL, ['class' => 'form-control date', 'placeholder' => 'Enter date of birth']) }}
        {!!invalid_feedback('date_of_birth')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('contact_no', 'Contact No', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('contact_no', NULL, ['class' => 'form-control', 'placeholder' => 'Enter contact no']) }}
        {!!invalid_feedback('contact_no')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('gender', 'Gender', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        <div class="border p-2 checkbox-group">
            <label class="checkbox-inline">
                <p>
                    {{ Form::radio('gender', 1, true, []) }} Male
                </p>
                
                <p>
                    {{ Form::radio('gender', 2, false, []) }} Female
                </p>
            </label>
        </div>
        {!!invalid_feedback('gender')!!}
    </div>
</div>

@if(isset($libmember->id))
	{{ Form::hidden('id', $libmember->id) }}
@endif

<div class="form-group row">
    <div class="col-md-10 offset-md-2">
      {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
      <a href="{{ route('library.member.index') }}" class="btn btn-warning btn-outline">Back</a>
    </div>
</div>