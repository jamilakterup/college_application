<form class='form-horizontal' action="{{route('admin.payslip_title.store')}}" data-form='ajaxForm'>
      
    <div class="form-group">
      {{ Form::label('title', 'PaySlip Title', ['class' => 'col-form-label']) }}
        {{ Form::text('title', $title, ['class' => 'form-control', 'placeholder' => 'Enter payslip title']) }}
        <div class='invalid-feedback'></div>
    </div>

    {!! Form::hidden('payslipheader_id', $header->id, []) !!}

    <div class="form-group">
      {!! Form::submit('Save Data', ['class'=> 'btn btn-primary', 'data-value'=> 'create', 'data-button'=>'save']) !!}
    </div>
</form>