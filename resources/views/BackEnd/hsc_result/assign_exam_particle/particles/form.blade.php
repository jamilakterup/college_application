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
  {{ Form::label('department_id', 'Department', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    <?php $department_name = App\Models\Group::whereId($department_id)->pluck('name'); ?>
    {{ Form::text('', $department_name[0], ['class' => 'form-control', 'readonly' => true]) }}
    {{ Form::hidden('department_id', $department_id) }}

    {!!invalid_feedback('department_id')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('subject_id', 'Subject', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">

    <?php $subject_name = App\Models\Subject::whereId($subject_id)->pluck('name'); ?>
    {{ Form::text('', $subject_name[0], ['class' => 'form-control', 'readonly' => true]) }}
    {{ Form::hidden('subject_id', $subject_id) }}

    {!!invalid_feedback('subject_id')!!}

  </div>
</div>

<div class="form-group row">
  {{ Form::label('xmparticle_id', 'Exam Particle', ['class' => 'col-md-2 form-control-label']) }}
  <div class="col-md-10">
    <table class='table table-bordered null-odd-even'>

      <tr>
        <th style='width: 10%'>ADD</th>
        <th>Name</th>
        <th>Total</th>
        <th>Pass</th>
        <th>Percentage</th>
      </tr>

      @foreach($xmparticles as $xmparticle)

        <?php

          $checked = '';

          if(isset($xmparticle->id)) :  

            //check having the exam particle in the class subject
            $has_exam_particle = App\Models\ConfigExamParticle::whereClasse_id($class_id)->whereGroup_id($department_id)->whereSubject_id($subject_id)->whereXmparticle_id($xmparticle->id)->count();                  
            if($has_exam_particle > 0) :
              $checked = 'checked';
              $the_exam_particle = App\Models\ConfigExamParticle::whereClasse_id($class_id)->whereGroup_id($department_id)->whereSubject_id($subject_id)->whereXmparticle_id($xmparticle->id)->first();
            endif;  

          endif;

        ?>      

        @if($checked == '')
          <tr>
            <td class="text-center">{{ Form::checkbox('examparticle-' . $xmparticle->id, $xmparticle->id, NULL, ['class' => 'action-type-a']) }}</td>
            <td class='align-left'>{{ $xmparticle->name }}</td>
            <td>{{ Form::text('total-' . $xmparticle->id, $xmparticle->total, ['class' => 'form-control', 'readonly' => true]) }}</td>
            <td>{{ Form::text('pass-' . $xmparticle->id, $xmparticle->pass, ['class' => 'form-control', 'readonly' => true]) }}</td>
            <td>{{ Form::text('per-' . $xmparticle->id, $xmparticle->per_centage, ['class' => 'form-control', 'readonly' => true]) }}</td>
          
          </tr>
        @endif  

        @if($checked == 'checked')  
          <tr>
            <td class="text-center">{{ Form::checkbox('examparticle-' . $xmparticle->id, $xmparticle->id, true, ['class' => 'action-type-a']) }}</td>
            <td class='align-left'>{{ $xmparticle->name }}</td>
            <td>{{ Form::text('total-' . $xmparticle->id, $the_exam_particle->total, ['class' => 'form-control']) }}</td>
            <td>{{ Form::text('pass-' . $xmparticle->id, $the_exam_particle->pass, ['class' => 'form-control']) }}</td>
            <td>{{ Form::text('per-' . $xmparticle->id, $the_exam_particle->per_centage, ['class' => 'form-control']) }}</td>
            
          </tr>
        @endif      

      @endforeach

    </table>
    
    <table class='table table-bordered null-odd-even'>

      <tr>
        
        <th>Total Subject Marks</th>
        <th>Total Pass Marks</th>
        <th>Conversion Marks</th>
      </tr>
      <?php 
      $exm_sub_count = count($exam_subjects);
      if($exm_sub_count <1 ) {?>
          <td><input type="text"   class="form-control"  name="total_number" autocomplete="off" value="" ></td>
          <td><input type="text"   class="form-control"  name="total_pass" autocomplete="off" value="" ></td>
           <td><input type="text"   class="form-control"  name="total_converted" autocomplete="off" value="" ></td>
      <?php } else {
        
        foreach($exam_subjects as $exam_subject) {?>
            <td><input type="text"   class="form-control"  name="total_number" autocomplete="off" value="<?php echo $exam_subject->total; ?>" ></td>
           <td><input type="text"   class="form-control"  name="total_pass" autocomplete="off" value="<?php echo $exam_subject->total_pass; ?>" ></td>
            <td><input type="text"   class="form-control"  name="total_converted" autocomplete="off" value="<?php echo $exam_subject->total_converted; ?>" ></td>
      <?php }}?>
    </table>
  </div>
</div>

<div class="form-group row">
  <div class="col-md-10 offset-md-2">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('hsc_result.assign_exam.index') }}" class="btn btn-warning btn-outline">Back</a>
  </div>
</div>