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
          <div class="panel-actions"><a href="{{ route('hsc_result.exam.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Exam</a></div>
          <h3 class="panel-title">Exam Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              	<tr>
					<th>Exam Id</th>			
					<th>Exam</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
            </thead>
            
            <tbody>
	        	@foreach($exams as $exam)

					<tr class="text-center {{ Ecm::updatedRow('id', $exam->id) }}">
						<td>{{ $exam->id }}</td>
						<td>{{ $exam->name }}</td>					
						<td><a href="{{ URL::route('hsc_result.exam.edit', $exam->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
						<td>
							{{ Form::open(['route' => ['hsc_result.exam.destroy', $exam->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $exam->id) }}
								<button type='submit' class='del'><i class='fa fa-trash'></i></button>
							{{ Form::close() }}
						</td>
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $exams->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush