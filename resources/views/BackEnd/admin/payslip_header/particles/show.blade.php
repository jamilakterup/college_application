
@php
	$payslip_items = App\Models\PayslipItem::where('payslipheader_id', $header->id)->get();
	$payslip_generators = App\Models\PayslipGenerator::where('payslipheader_id', $header->id)->get();
@endphp
<span>
	<strong>Title : </strong><span class="text-info">{{$header->title}}</span> ,
	<strong>Programme : </strong><span class="text-info">{{$header->pro_group}}</span> ,
	<strong>Faculty : </strong><span class="text-info">{{$header->group_dept}}</span> ,
	<strong>Level : </strong><span class="text-info">{{$header->level}}</span>
</span>
<h4>Payslip Items</h4>
<table class="table table-hover text-center table-sm">
	<thead>
		<tr>
			<th>ID</th>
			<th>Ttitle</th>
			<th>Type</th>
		</tr>
	</thead>
	<tbody>

		@foreach($payslip_items as $item)
			<tr>
				<td>{{$item->id}}</td>
				<td>{{$item->item}}</td>
				<td>{{$item->itemtype->name}}</td>
			</tr>
		@endforeach
	</tbody>
</table>


<h4>Payslip Generator</h4>
<table class="table table-hover text-center table-sm">
	<thead>
		<tr>
			<th>ID</th>
			<th>Payslip Header</th>
			<th>Payslip Item</th>
			<th>Payslip Title</th>
			<th>Fees</th>
		</tr>
	</thead>
	<tbody>

		@foreach($payslip_generators as $generator)
			<tr>
				<td>{{$generator->id}}</td>
				<td>{{$generator->payslipheader->title}}</td>
				<td>{{$generator->payslipitem->item}}</td>
				<td>{{$generator->paysliptitle->title}}</td>
				<td>{{$generator->fees}}</td>
			</tr>
		@endforeach
	</tbody>
</table>