<tr class="text-center update_row" data-row-id="{{$payslip_item->id}}">
	<td>{{$payslip_item->id}}</td>
	<td>{{$payslip_item->item}}</td>
	<td>{{$payslip_item->itemtype->name}}</td>
	<td>
		<a href="{{route('admin.payslip_item.edit', $payslip_item->id)}}" class="btn btn-primary type-c edit_item" data-id="{{$payslip_item->id}}"><i class='fas fa-pencil'></i></a>
		<a href="{{route('admin.payslip_item.destroy', $payslip_item->id)}}" class="btn btn-danger type-c delete_item" data-id="{{$payslip_item->id}}"><i class='fas fa-trash'></i></a>
	</td>
</tr>	