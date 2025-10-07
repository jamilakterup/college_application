<div class="form-group row">
    {{ Form::label('libraryuser_id', 'User Type', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        <?php $libraryuser_id = isset($libraryuser_id) ? $libraryuser_id : NULL; ?>
		{{ Form::select('libraryuser_id', $libraryuser_lists, $libraryuser_id, ['class' => 'form-control','disabled' => true]) }}
        {!!invalid_feedback('libraryuser_id')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('libmember_id', 'Member Id', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        <?php $libmember_id = isset($libmember_id) ? $libmember_id : NULL; ?>			    		
		{{ Form::text('libmember_id', $libmember_id, ['class' => 'form-control', 'placeholder' => 'Enter library member id', 'readonly' => true]) }}
        {!!invalid_feedback('libmember_id')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('accession_no', 'Accession No', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        {{ Form::text('accession_no', NULL, ['class' => 'form-control', 'placeholder' => 'Enter accession no']) }}
        {!!invalid_feedback('accession_no')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('issued_days', 'Issued For (days)', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
    	<?php $issued_days = isset($circulation->issued_days) ? $circulation->issued_days : NULL; ?>
        {{ Form::text('issued_days', $issued_days, ['class' => 'form-control', 'readonly' => true]) }}
        {!!invalid_feedback('accession_no')!!}
    </div>
</div>

<div class="form-group row">
    {{ Form::label('overdue_amount', 'Fine/day(tk)', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">
        <?php $overdue_amount = isset($circulation->overdue_amount) ? $circulation->overdue_amount : NULL; ?>
		{{ Form::text('overdue_amount', $overdue_amount, ['class' => 'form-control', 'readonly' => true]) }}
        {!!invalid_feedback('overdue_amount')!!}
    </div>
</div>

<div class="form-group row">
    <div class="col-md-10 offset-md-2">
      {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
      <a href="{{ route('library.circulation.index') }}" class="btn btn-warning btn-outline">Back</a>
    </div>
</div>