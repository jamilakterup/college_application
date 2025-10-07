<p>Payslip Header Title : </strong><span class="text-info">{{$generator->payslipheader->title}}</p>
<p>Payslip Item : </strong><span class="text-info">{{$generator->payslipitem->item}}</span></p>
<p>Payslip Title : </strong><span class="text-info">{{$generator->paysliptitle->title}}</span></p>

<form class='form-horizontal' action="{{route('admin.payslip_generator.store')}}" data-form='ajaxForm'>
      
    <div class="form-group">
      {{ Form::label('fees', 'PaySlip Fees', ['class' => 'col-form-label']) }}
        {{ Form::text('fees', (int) $generator->fees, ['class' => 'form-control']) }}
        <div class='invalid-feedback'></div>
    </div>

    {!! Form::hidden('payslipheader_id', $header->id, []) !!}

    <div class="form-group">
      {!! Form::submit('Save Data', ['class'=> 'btn btn-primary', 'data-value'=> 'create', 'data-button'=>'save']) !!}
    </div>
</form>