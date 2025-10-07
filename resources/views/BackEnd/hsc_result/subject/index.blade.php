@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Faculty Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item header-menu">
	@include('BackEnd.hsc_result.subject.particles.subMenu')
</div>

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('hsc_result.subject.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Subject</a></div>
          <h3 class="panel-title">Subject Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              	<tr>
					<th>Subject Id</th>			
					<th>Subject</th>
					<th>Subject Code</th>				
					<th>Optional</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
            </thead>
            
            <tbody>
	        	@foreach($subjects as $subject)

					<tr class="text-center {{ Ecm::updatedRow('id', $subject->id) }}">
						<td>{{ $subject->id }}</td>
						<td style='text-align: left'>{{ $subject->name }}</td>		
						
						<td>{{ $subject->code }}</td>	
						<td>
							@if($subject->optional == 1)
								Yes
							@else
								No	
							@endif
						</td>													
						<td><a href="{{ URL::route('hsc_result.subject.edit', $subject->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
						<td>
							{{ Form::open(['route' => ['hsc_result.subject.destroy', $subject->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $subject->id) }}
								<button type='submit' class='btn btn-danger type-b'><i class='fa fa-trash'></i></button>
							{{ Form::close() }}
						</td>
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $subjects->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush