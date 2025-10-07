<span>
  <strong>Header Title : </strong><span class="text-info">{{$header->title}}</span> ,
  <strong>Programme : </strong><span class="text-info">{{$header->pro_group}}</span> ,
  <strong>Faculty : </strong><span class="text-info">{{$header->group_dept}}</span> ,
  <strong>Level : </strong><span class="text-info">{{$header->level}}</span>
</span>

<form class='form-horizontal' action="{{route('admin.payslip_generator.store')}}" data-form='ajaxForm'>
    <div class="form-group">
      {{ Form::label('title', 'PaySlip Title', ['class' => 'col-form-label']) }}
        {{ Form::select('title', $title_options , $title, ['class' => 'form-control', 'placeholder' => '<--Select payslip title-->']) }}
        <div class='invalid-feedback'></div>
    </div>

    <div class="form-group">
      {{ Form::label('fees', 'PaySlip Fees', ['class' => 'col-form-label']) }}
        <table class='table table-bordered null-odd-even'>

          <tr class="text-white">
            <th style='width: 10%'>ADD</th>
            <th>Descriptions</th>
            <th style='width: 30%'>Fees Amount (Taka)</th>
          </tr>

            <?php

              $payslip_items = App\Models\PayslipItem::wherePayslipheader_id($header->id)->get();

            ?>

            @foreach($payslip_items as $payslip_item)

              <tr>
                <td>{{ Form::checkbox($payslip_item->id, $payslip_item->id, NULL, ['class' => 'action-type-a']) }}</td>
                <td class='align-left'>{{ $payslip_item->item }}</td>
                <td>{{ Form::text('fee' . $payslip_item->id, NULL, ['class' => 'form-control']) }}</td>
              </tr>

            @endforeach
        </table>
    </div>

    {!! Form::hidden('payslipheader_id', $header->id, []) !!}

    <div class="form-group">
      {!! Form::submit('Save Data', ['class'=> 'btn btn-primary', 'data-value'=> 'create', 'data-button'=>'save']) !!}
    </div>
</form>