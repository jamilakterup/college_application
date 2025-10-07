<div class="form-group row">
  {{ Form::label('payslipheader_id', 'PaySlip Header', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">
    {{ Form::select('payslipheader_id', create_option_array('payslipheaders', 'id', 'title', 'Payslip Header'),null, ['class' => 'form-control show-tick','data-plugin'=> 'select2', 'placeholder'=> 'Select Payslip Header']) }}

    {!!invalid_feedback('payslipheader_id')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('item', 'PaySlip Item', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    {{ Form::text('item', NULL, ['class' => 'form-control', 'placeholder' => 'Enter payslip item title']) }}

    {!!invalid_feedback('item')!!}

  </div>
</div>

@if(isset($payslip_item->id))
  {{ Form::hidden('id', $payslip_item->id) }}
@endif

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('admin.payslip_item.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>