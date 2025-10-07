@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Certificate Requirement Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item admission-menu">
	@include('BackEnd.admin.admission.particles.subMenu')
</div>

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('admin.requirement.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add Certificate Requirement</a></div>
          <h3 class="panel-title">Certificate Requirement Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              <tr>
              	<th>Certificate Short Name</th>			
				<th>Certificate Full Name</th>
				<th>Edit</th>
				<th>Delete</th>
              </tr>
            </thead>
            
            <tbody>
            	@foreach($requirements as $requirement)

					<tr class="text-center {{ Study::updatedRow('id', $requirement->id) }}">
						<td>{{ $requirement->certificate_short_name }}</td>						
						<td>{{ $requirement->certificate_full_name }}</td>
						<td class="text-center"><a href="{{ URL::route('admin.requirement.edit', $requirement->id) }}" class='btn btn-info type-b'><i class='fa fa-pencil'></i></a></td>	
						<td class="text-center">
							{{ Form::open(['route' => ['admin.requirement.destroy', $requirement->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $requirement->id) }}
								<button type='submit' class='btn btn-danger type-b'><i class='fa fa-trash'></i></button>
							{{ Form::close() }}
						</td>
					</tr>	

				@endforeach
            	
            </tbody>
          </table>
          {{ $requirements->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush