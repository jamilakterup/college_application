<div class="form-group row">
    {{ Form::label('hostel_name', 'Hostel Name', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

        {{ Form::text('hostel_name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Hostel Name']) }}

        {!!invalid_feedback('hostel_name')!!}

    </div>
</div>

<div class="form-group row">
    {{ Form::label('total_seat', 'Total Sheet', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

        {{ Form::text('total_seat', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Total Sheet']) }}

        {!!invalid_feedback('total_seat')!!}

    </div>
</div>

<div class="form-group row">
    {{ Form::label('available_seat', 'Available Sheet', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

        {{ Form::text('available_seat', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Available Sheet']) }}

        {!!invalid_feedback('available_seat')!!}

    </div>
</div>

<div class="form-group row">
    {{ Form::label('no_room', 'Total Room', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

        {{ Form::text('no_room', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Total Room']) }}

        {!!invalid_feedback('no_room')!!}

    </div>
</div>

<div class="form-group row">
    {{ Form::label('provost', 'Provost', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

        {{ Form::text('provost', NULL, ['class' => 'form-control', 'placeholder' => 'Enter Provost']) }}

        {!!invalid_feedback('provost')!!}

    </div>
</div>

<div class="form-group row">
    <div class="col-md-10 offset-md-2">
      {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
      <a href="{{ route('hall.index') }}" class="btn btn-warning btn-outline">Back</a>
    </div>
</div>