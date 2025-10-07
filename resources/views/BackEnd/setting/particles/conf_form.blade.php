<div class="form-group">
    {{ Form::label('value', 'Value', ['class' => 'form-control-label  text-danger']) }}

    @if($conf->input_type == 'text')
        {{ Form::text('value', $conf->value, ['class' => 'form-control', 'placeholder' => 'Value']) }}
    @endif


    @if($conf->input_type == 'textarea')
        {!! Form::textarea('value', $conf->value, ['class' => 'summernote']) !!}
    @endif
</div>