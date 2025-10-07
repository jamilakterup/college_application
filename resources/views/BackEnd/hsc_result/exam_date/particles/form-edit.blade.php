<div class="form-group row">
	{{ Form::hidden('class_id',$current_level ) }}
	{{ Form::hidden('session', $session) }}
	{{ Form::hidden('group_id', $group_id) }}
	{{ Form::hidden('exam_id', $exam_id) }}
	{{ Form::hidden('exam_year', $exam_year) }}

    {{ Form::label('class_id', 'Class', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

      <?php $class_name = App\Models\Classe::whereId($current_level)->pluck('name')->first(); ?>
      {{ Form::text('', $class_name, ['class' => 'form-control', 'readonly' => true]) }}
		  {{ Form::hidden('class_id', $current_level) }}
      {!!invalid_feedback('class_id')!!}
  
    </div>
  </div>

  <div class="form-group row">
    {{ Form::label('group', 'Group', ['class' => 'col-md-2 form-control-label']) }}
    <div class="col-md-10">

      <?php $department_name = App\Models\Group::whereId($group_id)->pluck('name')->first(); ?>
		  {{ Form::text('', $department_name, ['class' => 'form-control', 'readonly' => true]) }}
		  {{ Form::hidden('department_id', $group) }}
  
      {!!invalid_feedback('group')!!}
  
    </div>
  </div>

  @foreach($class_sub as $sub)
    <div class='form-group row'>
    <?php $sub_var=$sub->subject->name.' ('.$sub->subject->code.')';?>
      {{ Form::label('subject', $sub_var, ['class' => 'col-md-2 form-control-label']) }}
      <div class='col-md-10'>
        <?php  
        $check_sub_xm= App\Models\ExamDate::whereClass_id($current_level)->whereGroup_id($group_id)->whereExam_id($exam_id)->whereSession($session)->whereExam_year($exam_year)->whereSubject_id($sub->subject->id)->get();
        ?>
        @if($check_sub_xm->count()==0)
        {{ Form::text($sub->subject_id,  Null, ['class' => 'form-control date']) }}
        @else
        {{ Form::text($sub->subject_id,  $check_sub_xm[0]->date, ['class' => 'form-control date']) }}
        @endif
      </div>
    </div>	  
  @endforeach
  
  <div class="form-group row">
    <div class="col-md-10 offset-md-2">
      {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
      <a href="{{ route('hsc_result.exam_date.index') }}" class="btn btn-warning btn-outline">Back</a>
    </div>
  </div>