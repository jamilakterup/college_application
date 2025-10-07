@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Exam Setup Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item header-menu">
	@include('BackEnd.hsc_result.subject_info.particles.subMenu')
</div>

<div class="panel">
        <header class="panel-heading">

          <div class="panel-actions">
{{--           	<a href="{{ route('hsc_result.exam.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Exam</a> --}}
          </div>
{{--           <h3 class="panel-title">Exam Lists</h3> --}}
        </header>


        <div class="panel-body">

        	<form action="{{ url('hsc_result/subject_info') }}" class="form-inline d-flex justify-content-center">

            <div class="form-group">
              {!! Form::text('student_id', request()->get('student_id'), ['class'=> 'form-control', 'id' => 'student_id']) !!}
            </div>

            <div class="form-group">
              {!! Form::select('session', selective_multiple_session() ,request()->get('session') , ['class'=> 'form-control', 'id' => 'session']) !!}
            </div>

            <div class="form-group">
              {!! Form::submit('Search', ['class' => 'form-control']) !!}
            </div>
          </form>
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              	<tr>
					<th>Student Id</th>	
					<th>Name</th>				
					<th>Current Level</th>
					<th>Session</th>
					<th>Compolsay Subjects</th>
					<th>Selective Subject</th>
					<th>Fourth Subject</th>
					<th>Edit</th>
				</tr>
            </thead>
            
            <tbody>
	        	@foreach($student_sub_infos as $info)
					<?php 
						$sutdent_name = DB::table("student_info_hsc")->where("id",$info->student_id)->get();
							?>
					<tr class=" text-center {{ Ecm::updatedRow('id', $info->id) }}">
						<td>{{ $info->student_id }}</td>
						<td>{{$sutdent_name[0]->name}}</td>
						<td>{{ $info->current_level }}</td>	
						<td>{{ $info->session }}</td>	
						@if($info->current_level=='HSC 2nd Year')
						<td>102-108-275</td>
						@else
						<td>101-107-275</td>
						@endif
						<?php $selective=$info->sub4->name.'-'.$info->sub5->name.'-'.$info->sub6->name;?>
						<td>{{ $selective }}</td>
						<td>{{ $info->fourth->name }}</td>
						@if($info->current_level=='HSC 2nd Year')
						<td></td>
						@else					
						<td><a href="{{ URL::route('hsc_result.subject_info.edit', $info->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
						@endif
						
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $student_sub_infos->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush