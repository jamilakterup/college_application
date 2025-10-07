<div class="form-group row">
  {{ Form::label('class_id', 'Class', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    <?php $class_name = App\Models\Classe::whereId($class_id)->pluck('name'); ?>
    {{ Form::text('', $class_name[0], ['class' => 'form-control', 'readonly' => true]) }}
    {{ Form::hidden('class_id', $class_id) }}

    {!!invalid_feedback('class_id')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('department_id', 'Department', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    <?php $department_name = App\Models\Group::whereId($department_id)->pluck('name'); ?>
    {{ Form::text('', $department_name[0], ['class' => 'form-control', 'readonly' => true]) }}
    {{ Form::hidden('department_id', $department_id) }}

    {!!invalid_feedback('code')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('subject', 'Subject', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">
    <div class="border p-2 checkbox-group">
      <label class="checkbox-inline">
         @foreach($subjects as $subject)
          <?php

            $checked = '';

            if(isset($subject->id)) : 

              //check having the subject in the class, department
              $has_subject = App\Models\ClassSubject::where('classe_id',$class_id)->where('group_id',$department_id)->where('subject_id',$subject->id)->count();                 
              if($has_subject > 0) :
                $checked = 'checked';
              endif;
            endif;
          ?>
          @if($checked == '')
            <p>
              {!! Form::checkbox('subject-' . $subject->id, $subject->id) . ' ' . $subject->name.' ('.$subject->code.')' !!}
            </p>
          @endif

          @if($checked == 'checked')
            <p>
              {!! Form::checkbox('subject-' . $subject->id, $subject->id, true) . ' ' . $subject->name.' ('.$subject->code.')' !!}
            </p>        
          @endif
        @endforeach
      </label>
    </div>

  </div>
</div>

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('hsc_result.assign_subject.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>