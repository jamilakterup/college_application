@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Attendence Sheet Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	
	<div class="panel-body">
		<?php 
		
		$group_name = App\Models\Group::whereId($group)->pluck('name')->first();
		$exam_name = App\Models\Exam::whereId($exam_id)->pluck('name')->first();
		$subject_name = App\Models\Subject::whereId($subject_id)->pluck('name')->first();
		$subject_code = App\Models\Subject::whereId($subject_id)->pluck('code')->first();		
		?>	
		
		{{ Form::open(['route' => 'hsc_result.attendance_sheet.store', 'method' => 'post']) }}
		
		{{ Form::hidden('current_level', $curr_level->id) }}		
		{{ Form::hidden('session', $session) }}
		{{ Form::hidden('group_id', $group) }}
		{{ Form::hidden('exam_id', $exam_id) }}
		{{ Form::hidden('subject_id', $subject_id) }}
		{{ Form::hidden('subject_name', $subject_name) }}
			<input type="hidden" class="form-control" name="str" placeholder="room no." required="required" value="<?php echo $str; ?>">
			<h5 style="text-align: right;color: green;">Showing {{$student_info->firstItem()}} to  {{$student_info->lastItem()}} of {{$student_info->total()}}</h5>

			<div class="form-row">
				<div class="form-group col-md-6">
				  <label for="exam_date" style="font-weight: bold;">Exam Date</label>
				  <input class="form-control" name="exam_date" placeholder="dd-mm-yyyy" required="required">
				</div>
				<div class="form-group col-md-6">
				  <label for="room_no" style="font-weight: bold;">Room No.</label>
				  <input class="form-control" name="room_no" placeholder="room no." required="required">
				</div>

				<div class="form-group col-md-6">
					<label for="from_roll" style="font-weight: bold;">From Roll No.</label>
					<input class="form-control" name="from_roll" placeholder="from roll." required="required">
				</div>

				<div class="form-group col-md-6">
					<label for="to_roll" style="font-weight: bold;">To Roll No.</label>
					<input class="form-control" name="to_roll" placeholder="to roll." required="required">
				</div>
				  
			</div>
			
			<table class="table input-mark mb-0">
				<caption>
					Session <span>{{ $session }}</span> 
					Group <span>{{ $group_name }}</span> 
					Current Level <span>{{ $curr_level->name }}</span> 					
					Exam <span>{{ $exam_name }}</span> 
					Subject <span>{{ $subject_name }} ({{$subject_code}})</span>
				</caption>
			</table>
			
			<table class='table table-bordered null-odd-even input-mark'>
				
				<tr> 
					<th style='width: 10%'>								
						<i class='i-type-a'>ALL</i> <br/> <input type='checkbox' onchange='checkAllAtt(this)' name='toggleCheck' id='toggle-check' checked/>
					</th>
					<th style='width: 10%'>Roll</th>
					<th style='width: 30%'>Name </th>
					<!--th style='width: 30%'>Photo</th-->
					
				</tr>
				
				@foreach($student_info as $info)
				<?php 
				$students  = DB::table('student_info_hsc')->where('id',$info->student_id)->get();
				if(count($students)>0){
					
					
					?>
					<tr class="text-center">
						<td>{{ Form::checkbox('info-' . $info->student_id, $info->student_id, true, ['class' => 'action-type-a']) }}</td>
						<td>{{ $info->student_id }}</td>
						<td>
							<?php
							$student_name = DB::table('student_info_hsc')->where('id',$info->student_id)->first(); 
							echo $student_name->name;
							?>
						</td>
						<!--td class="text-center">
							
							<img width="40" height="40" src="{{ URL::to('/') }}/upload/college/hsc/{{$student_name->image}}" alt="..." >
						</td-->
						
					</tr>		
					<?php } ?>
					@endforeach						
					
				</table>
				
				<nav>
					{{ $student_info->links() }}
				</nav>
		
		
			<div class='col-sm-12' style='text-align: center'>
				{{ Form::submit('Download', ['class' => 'btn custom btn-primary']) }}
			</div>							 			
		
		{{ Form::close() }}        
		
	</div>	
</div>

@endsection

@push('scripts')
	<script>
		
		function checkAllAtt(ele) {

		var checkboxes = document.getElementsByTagName('input');
		var selectags = document.getElementsByTagName('select');  

		if (ele.checked) {

			for (var i = 0; i < checkboxes.length; i++) {
				if (checkboxes[i].type == 'checkbox') {
					checkboxes[i].checked = true;
				}


			}

			for (var j = 0; j < selectags.length; j++) { 
				selectags[j].disabled = false;
			}  

		} else {

			for (var i = 0; i < checkboxes.length; i++) {
				if (checkboxes[i].type == 'checkbox') {
					checkboxes[i].checked = false;
				}


			}

			for (var j = 0; j < selectags.length; j++) { 
				selectags[j].disabled = true;
			}  

		}

		}


	</script>
@endpush