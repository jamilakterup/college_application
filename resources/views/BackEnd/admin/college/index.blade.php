@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'College Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('admin.college.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New College</a></div>
          <h3 class="panel-title">College Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border text-center">
            <thead>
              <tr>
                <th>Collage Code</th>
				<th>Biller ID</th>
				<th>Logo</th>
				<th>College Name</th>
				<th>Area</th>
				<th>Phone</th>
				<th>Establish Date</th>
				<th>Status</th>
				<th class="text-center">Actions</th>
				<th class="text-center">Option</th>
              </tr>
            </thead>
            
            <tbody>
	              @foreach($colleges as $college)

					<tr class="text-center {{ Study::updatedRow('id', $college->id) }}">
						<td>{{ $college->college_code }}</td>
						<td>{{ $college->biller_id }}</td>					
						<td> <img src="{{ URL::to('/') }}/upload/college/{{$college->logo}}" alt='' class='img-type-a' width="50px" /> </td>					
						<td><a href="{{ $college->website }}" target='_blank'>{{ $college->college_name . ' (' . $college->college_name_bengali . ')' }}</a></td>
						<td>{{ $college->area_name . ' (' . $college->area_name_bengali . ')' }}</td>
						<td>{{ $college->phone }}</td>
						<td>{{ $college->establish_date }}</td>
						<td>
							@if($college->status == 0)
								Inactive
							@endif

							@if($college->status == 1)
								Active
							@endif						
						</td>
						<td>
							@if($college->status == 0)
								{{ Form::open(['route' => ['admin.college.status', $college->id],'method' => 'put','class' => 'inline']) }}
									{{ Form::hidden('status', 1) }}
									{{ Form::hidden('id', $college->id) }}
									{{ Form::submit('Activate', ['class' => 'btn btn-success type-b'])}}
								{{ Form::close() }}
							@endif

							@if($college->status == 1)
								{{ Form::open(['route' => ['admin.college.status', $college->id],'method' => 'put','class' => 'inline']) }}
									{{ Form::hidden('status', 0) }}
									{{ Form::hidden('id', $college->id) }}
									{{ Form::submit('Deactivate', ['class' => 'btn btn-danger type-b'])}}
								{{ Form::close() }}
							@endif
						</td>
						<td>

							<a href="{{ URL::route('admin.college.edit', $college->id) }}" class='btn btn-primary type-b'><i class='fa fa-pencil'></i></a>
							{{ Form::open(['route' => ['admin.college.destroy', $college->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $college->id) }}
								<button type='submit' class='btn btn-danger type-b'><i class='fa fa-trash'></i></button>
							{{ Form::close() }}
						</td>
					</tr>	

				@endforeach
            </tbody>
          </table>
        </div>
      </div>

@endsection

@push('scripts')
	<script>
	</script>
@endpush