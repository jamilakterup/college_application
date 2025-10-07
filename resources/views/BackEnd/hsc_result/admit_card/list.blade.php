@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Admit Card Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
		<?php
			$exam_name = App\Models\Exam::whereId($exam_id)->pluck('name')->first();		
		?>
        <header class="panel-heading">
          <h3 class="panel-title">Admit Card Generate Form ({{$session}} -> {{$group}} -> {{$curr_level->name}} -> {{$exam_name}} )</h3>
        </header>
			<div class="panel-body">
				{{ Form::open(['route' => 'hsc_result.admit_card.store', 'method' => 'post']) }}
				
				{{ Form::hidden('session', $session) }}
				{{ Form::hidden('group', $group) }}
				{{ Form::hidden('current_level', $current_level) }}	    	
				{{ Form::hidden('exam_id', $exam_id) }}
				
				
				<div class='form-group'>
					<p class='para-type-b'>
						<span>Instructions:</span><br/>
						i)  You can download maximum <span>50 students</span> Admit Card at a time.
					</p>
				</div>
				
				<div class='form-group'>
					{{ Form::label('students_list', 'Students List', ['class' => '']) }}
					<table class='table table-bordered null-odd-even'>
						
						<tr>
							<th style='width: 13%'>
								<i class='i-type-a'>ALL</i> <br/> <input type='checkbox' onchange='checkAll(this)' name='toggleCheck' id='toggle-check'/>
							</th>
							<th>Student ID</th>
							<th>Name</th>		
							<th>Group</th>							
							<th>Exam Name</th>
						</tr>
						
						@foreach($student_info as $info)
						<tr>
							<td class="text-center">{{ Form::checkbox('studentinfo-' . $info->id, $info->id, false, ['class' => 'action-type-a']) }}</td>						
							<td>{{ $info->id }}</td>
							<td style='text-align: left'>{{ $info->name }}</td>
							<td style='text-align: left'>{{ $info->groups }}</td>			
							<td>{{ App\Models\Exam::whereId($exam_id)->pluck('name')->first() }}</td>
						</tr>
						@endforeach
						
					</table>
					<div>
						{{ $student_info->links() }}
					</div>
			</div>

			<div class='form-group mb-2'>
				{{ Form::submit('Generate', ['class' => 'btn btn-primary']) }}
			</div> <!-- end form-group -->

		</div>

	{{ Form::close() }} 
</div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush