@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Exam Setup Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item header-menu">
	@include('BackEnd.hsc_result.exam.particles.subMenu')
</div>

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('hsc_result.examparticle.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Exam Particle</a></div>
          <h3 class="panel-title">Exam Particle Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              	<tr>
					<th>Exam Particle Name</th>			
					<th>Short Name</th>
					<th>Total</th>		
					<th>Pass</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
            </thead>
            
            <tbody>
	        	@foreach($xmparticles as $xmparticle)

					<tr class="text-center {{ Ecm::updatedRow('id', $xmparticle->id) }}">
						<td>{{ $xmparticle->name }}</td>		
						<td>{{ $xmparticle->short_name }}</td>
						<td>{{ $xmparticle->total }}</td>
						<td>{{ $xmparticle->pass }}</td>
						<td><a href="{{ URL::route('hsc_result.examparticle.edit', $xmparticle->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
						<td>
							{{ Form::open(['route' => ['hsc_result.examparticle.destroy', $xmparticle->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $xmparticle->id) }}
								<button type='submit' class='del'><i class='fa fa-trash'></i></button>
							{{ Form::close() }}
						</td>
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $xmparticles->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush