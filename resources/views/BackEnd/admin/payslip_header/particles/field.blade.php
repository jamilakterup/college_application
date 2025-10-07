
@php
	$payslip_items = App\Models\PayslipItem::where('payslipheader_id', $header->id)->orderBy('id', 'desc')->get();
	$payslip_titles = App\Models\PayslipTitle::where('payslipheader_id', $header->id)->orderBy('id', 'desc')->get();
	$payslip_generators = App\Models\PayslipGenerator::where('payslipheader_id', $header->id)->orderBy('id', 'desc')->get();
@endphp
<span>
	<strong>Title : </strong><span class="text-info">{{$header->title}}</span> ,
	<strong>Program : </strong><span class="text-info">{{$header->pro_group}}</span> ,
	<strong>Faculty : </strong><span class="text-info">{{$header->group_dept}}</span> ,
	<strong>Level : </strong><span class="text-info">{{$header->level}}</span>
</span>
<div class="fields-errors text-center"></div>

	
<h4 class="float-left">Payslip Items</h4>

<a href="{{route('admin.payslip_item.create',['header_id'=> $header->id])}}" class="btn btn-primary float-right add_item btn-sm">Add New Item</a>

<table class="table table-hover text-center table-sm mt-1 mb-4" id="table_item">
	<thead>
		<tr>
			<th>ID</th>
			<th>Title</th>
			<th>Type</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>

		@foreach($payslip_items as $item)
			<tr class="text-center" data-row-id="{{$item->id}}">
				<td>{{$item->id}}</td>
				<td>{{$item->item}}</td>
				<td>{{$item->itemtype->name}}</td>
				<td>
					<a href="{{route('admin.payslip_item.edit', $item->id)}}" class="btn btn-primary type-c edit_item" data-id="{{$item->id}}"><i class='fas fa-pencil'></i></a>
					<a href="{{route('admin.payslip_item.destroy', $item->id)}}" class="btn btn-danger type-c delete_item" data-id="{{$item->id}}"><i class='fas fa-trash'></i></a>
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
<h4 class="float-left">Payslip Titles</h4>

<a href="{{route('admin.payslip_title.create',['header_id'=> $header->id])}}" class="btn btn-primary float-right add_item btn-sm">Add New Title</a>

<table class="table table-hover text-center table-sm mt-1 mb-4" id="table_title">
	<thead>
		<tr>
			<th>ID</th>
			<th>Title</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>

		@foreach($payslip_titles as $title)
			<tr class="text-center" data-row-id="{{$title->id}}">
				<td>{{$title->id}}</td>
				<td>{{$title->title}}</td>
				<td>
					<a href="{{route('admin.payslip_title.edit', $title->id)}}" class="btn btn-primary type-c edit_item" data-id="{{$title->id}}"><i class='fas fa-pencil'></i></a>
					<a href="{{route('admin.payslip_title.destroy', $title->id)}}" class="btn btn-danger type-c delete_item" data-id="{{$title->id}}"><i class='fas fa-trash'></i></a>
				</td>
			</tr>
		@endforeach
	</tbody>
</table>

<h4 class="float-left">Payslip Generator</h4>
<a href="{{route('admin.payslip_generator.create',['header_id'=> $header->id])}}" class="btn btn-primary float-right add_item btn-sm">Add New Generator</a>

<table class="table table-hover text-center table-sm" id="table_generator">
	<thead>
		<tr>
			<th>ID</th>
			<th>Payslip Item</th>
			<th>Payslip Title</th>
			<th>Fees</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>

		@foreach($payslip_generators as $generator)
			<tr class="text-center" data-row-id="{{$generator->id}}">
				<td>{{$generator->id}}</td>
				<td>{{$generator->payslipitem->item}}</td>
				<td>{{$generator->paysliptitle->title}}</td>
				<td>
					<span>Tk.</span> {{ $generator->fees }}
				</td>
				<td>
					<a href="{{route('admin.payslip_generator.edit', $generator->id)}}" class="btn btn-primary type-c edit_item" data-id="{{$generator->id}}"><i class='fas fa-pencil'></i></a>
					<a href="{{route('admin.payslip_generator.destroy', $generator->id)}}" class="btn btn-danger type-c delete_item" data-id="{{$generator->id}}"><i class='fas fa-trash'></i></a>
				</td>
			</tr>
		@endforeach
	</tbody>
</table>