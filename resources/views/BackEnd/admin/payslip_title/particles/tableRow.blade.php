<tr class="text-center update_row" data-row-id="{{$payslip_title->id}}">
	<td>{{$payslip_title->id}}</td>
	<td>{{$payslip_title->title}}</td>
	<td>
		<a href="{{route('admin.payslip_title.edit', $payslip_title->id)}}" class="btn btn-primary type-c edit_item" data-id="{{$payslip_title->id}}"><i class='fas fa-pencil'></i></a>
		<a href="{{route('admin.payslip_title.destroy', $payslip_title->id)}}" class="btn btn-danger type-c delete_item" data-id="{{$payslip_title->id}}"><i class='fas fa-trash'></i></a>
	</td>
</tr>