<form class='form-horizontal' action="{{route('admin.payslip_item.store')}}" data-form='ajaxForm'>
      
    <div class="form-group">
      {{ Form::label('item', 'PaySlip Item', ['class' => 'col-form-label']) }}
        {{ Form::text('item', $title, ['class' => 'form-control', 'placeholder' => 'Enter payslip item title']) }}
        <div class='invalid-feedback'></div>
    </div>

    <div class="form-group">
      {{ Form::label('item_type', 'PaySlip Item Type', ['class' => 'col-form-label']) }}
        {{ Form::select('item_type', create_option_array('payslipitem_types', 'id', 'name'), $type, ['class' => 'form-control selectize', 'data-placeholder' => 'Select Payslip Item Type']) }}
        <div class='invalid-feedback'></div>
    </div>

    {!! Form::hidden('payslipheader_id', $header->id, []) !!}

    <div class="form-group">
      {!! Form::submit('Save Data', ['class'=> 'btn btn-primary', 'data-value'=> 'create', 'data-button'=>'save']) !!}
    </div>
</form>