@foreach($generators as $generator)
	<tr class="text-center update_row" data-row-id="{{$generator->id}}">
		<td>{{ $generator->id }}</td>
		<td>{{ $generator->payslipitem->item }}</td>
		<td>{{ $generator->paysliptitle->title }}</td>
		<td>
			<span>Tk.</span> {{ $generator->fees }}
		</td>								

		<td>
			<a href="{{route('admin.payslip_generator.edit', $generator->id)}}" class="btn btn-primary type-c edit_item" data-id="{{$generator->id}}"><i class='fas fa-pencil'></i></a>
			<a href="{{route('admin.payslip_generator.destroy', $generator->id)}}" class="btn btn-danger type-c delete_item" data-id="{{$generator->id}}"><i class='fas fa-trash'></i></a>
		</td>
	</tr>
@endforeach