<div class="form-group row">
  {{ Form::label('title', 'PaySlip Title', ['class' => 'col-sm-2 col-form-label']) }}
  <div class="col-sm-10">
    {{ Form::text('title', NULL, ['class' => 'form-control', 'placeholder' => 'Enter payslip title']) }}
  </div>
</div>
<div class="form-group row">
  {{ Form::label('fees', 'PaySlip Fees', ['class' => 'col-sm-2 col-form-label']) }}
  <div class="col-sm-10">
    <table class='table table-bordered null-odd-even'>

      <tr class="text-white">
        <th style='width: 10%'>ADD</th>
        <th>Descriptions</th>
        <th style='width: 30%'>Fees Amount (Taka)</th>
      </tr>

      @foreach($payslip_headers as $payslip_header)

        <tr>
          <th class='bg-type-a'></td>
          <th class='bg-type-a'>{{ $payslip_header->title }}</th>
          <th class='bg-type-a'></td>         
        </tr> 

        <?php

          $payslip_items = App\Models\PayslipItem::wherePayslipheader_id($payslip_header->id)->get();

        ?>

        @foreach($payslip_items as $payslip_item)

          <tr>
            <td>{{ Form::checkbox($payslip_item->id, $payslip_item->id, NULL, ['class' => 'action-type-a']) }}</td>
            <td class='align-left'>{{ $payslip_item->item }}</td>
            <td>{{ Form::text('fee' . $payslip_item->id, NULL, ['class' => 'form-control', 'readonly' => true]) }}</td>
          </tr>

        @endforeach

      @endforeach

    </table>
  </div>
</div>

 <!-- end form-group -->

<div class='form-group row'>
  <div class='col-sm-10 offset-sm-2'>
    {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
  </div>
</div> <!-- end form-group -->