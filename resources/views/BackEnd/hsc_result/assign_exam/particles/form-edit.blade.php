<div class="form-group row">
  {{ Form::label('class_id', 'Class', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    <?php $class_name = App\Models\Classe::whereId($class_id)->pluck('name'); ?>
    {{ Form::text('', $class_name[0], ['class' => 'form-control', 'readonly' => true]) }}
    {{ Form::hidden('class_id', $class_id) }}

    {!!invalid_feedback('name')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('exam', 'Exam', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">
    <div class="border p-2 checkbox-group">
      <label class="checkbox-inline">
         @foreach($exams as $exam)
          <?php

            $checked = '';

            if(isset($exam->id)) :  

              //check having the exam in the class
              $has_exam = App\Models\ClassExam::where('classe_id',$class_id)->where('exam_id',$exam->id)->count();                  
              if($has_exam > 0) :
                $checked = 'checked';
              endif;  

            endif;

          ?>

          @if($checked == '')
            <p>
              {!! Form::checkbox('exam-' . $exam->id, $exam->id) . ' ' . $exam->name !!}
            </p>
          @endif

          @if($checked == 'checked')
            <p>
              {!! Form::checkbox('exam-' . $exam->id, $exam->id, true) . ' ' . $exam->name !!}
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
    <a href="{{ route('hsc_result.assign_exam.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>