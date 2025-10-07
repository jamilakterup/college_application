@extends('BackEnd.hall.layouts.master')
@section('page-title', 'Hall Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('hall.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add Hall</a></div>
          <h3 class="panel-title">Hall Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
                <tr>
                    <th scope="col">Hostel Name</th>
                    <th scope="col">Total Seat</th>
                    <th scope="col">Available Seat</th>
                    <th scope="col">Total Room</th>
                    <th scope="col">Provost Name</th>
                    <th scope="col">Edit</th>
				</tr>
            </thead>
            
            <tbody>
                @foreach($hallinfo as $hall)
                    <tr class="text-center">
                        <td>{{ $hall->hostel_name }}</td>
                        <td>{{ $hall->total_seat }}</td>
                        <td>{{ $hall->available_seat }}</td>
                        <td>{{ $hall->no_room }}</td>
                        <td>{{ $hall->provost }}</td>
                        <td><a href="{{ route('hall.edit', $hall->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>               
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