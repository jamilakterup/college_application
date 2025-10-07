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
          <div class="panel-actions"><a href="{{ route('hsc_result.marks_input_config.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Config</a></div>
          <h3 class="panel-title">Mark Input Config Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              <tr>
					<th>Id</th>			
					<th>Exam Name</th>
					<th>Session</th>
					<th>Exam Year</th>
					<th>Exp Date</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
            </thead>
            
            <tbody>
	           @foreach($mark_configs as $mark_config)

					<tr class="text-center {{ Ecm::updatedRow('id', $mark_config->id) }}">
						<td>{{ $mark_config->id }}</td>
						<td>
						<?php 
						$exm_id = $mark_config->exam_id;
						$results = DB::table('exams')->where('id',$exm_id)->get() ;
						foreach($results as $result){
							echo $result->name;
						}
						?>
						</td>
						<td>{{$mark_config->session}}</td>	
						<td>{{$mark_config->exam_year}}</td>	
						<td> {{$mark_config->exp_date}}</td>					
						<td><a href="{{ URL::route('hsc_result.marks_input_config.edit', $mark_config->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>
						<td>
							{{ Form::open(['route' => ['hsc_result.marks_input_config.destroy', $mark_config->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $mark_config->id) }}
								<button type='submit' class='del'><i class='fa fa-trash'></i></button>
							{{ Form::close() }}
						</td>	
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $mark_configs->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush