@php

    $id = $teacher->id;
    $career = $teacher->teacherCareer;

    $career_from=$career->career_from;
    $career_to=$career->career_to;
    $career_description=$career->career_description;
    $special_memo=$career->special_memo;
    $activity_memo=$career->activity_memo;
    $language_name1=$career->language_name1;
    $read_skill1=$career->read_skill1;
    $write_skill1=$career->write_skill1;
    $speak_skill1=$career->speak_skill1;
    $language_name2=$career->language_name2;
    $read_skill2=$career->read_skill2;
    $write_skill2=$career->write_skill2;
    $speak_skill2=$career->speak_skill2;
    $language_name3=$career->language_name3;
    $read_skill3=$career->read_skill3;
    $write_skill3=$career->write_skill3;
    $speak_skill3=$career->speak_skill3;
    $emrgnc_name=$career->emrgnc_name;
    $emrgnc_position_held=$career->emrgnc_position_held;
    $emrgnc_address=$career->emrgnc_address;
    $emrgnc_mobile=$career->emrgnc_mobile;
    $emrgnc_email=$career->emrgnc_email;
    $relation =$career->relation;
@endphp

{{ Form::open(['route' => ['teacher.editTeachercareerinput', ['id'=> $id]], 'method' => 'post', 'class'=> 'form-horizontal', 'files'=> true]) }}

<h3> Appointment or Promotion</h3>

<input type="hidden" disabled="yes" name="teacher_id" value="<?php echo $id; ?>" />

<table class="table"> 
  <tr>
    <td>From:</td>
    <td> 
      <input class="form-control datepickr" type="text" name="career_from" value="<?php echo $career_from; ?>"/> 
    </td>
  </tr>
  
  <tr>
    <td>To:</td>
    <td>
      <input class="form-control datepickr" type="text" name="career_to" value="<?php echo $career_to; ?>" /> 
    </td>
  </tr>
  
  <tr>
    <td>Description:</td>
    <td> 
      <input class="form-control" type="text" name="career_description" value="<?php echo $career_description; ?>"/> 
    </td>
  </tr>
  
  <tr>
    <td colspan="2">
      <h3> Specialization </h3>
    </td>
  </tr>
  
  <tr>
    <td>Memo:</td>
    <td>
      <input class="form-control" type="text" name="special_memo"  value="<?php echo $special_memo; ?>"/>
    </td>
  </tr>
  
  <tr>
    <td colspan="2">
      <h3>Extracurricular Activity </h3>
    </td>
  </tr>
  
  <tr>
    <td>
      Memo:
    </td>
    <td>
      <input class="form-control" type="text" name="activity_memo" value="<?php echo $activity_memo; ?>" />
    </td>
  </tr>
  
  <tr>
    <td colspan="2"> <h3> Language Proficiency</h3></td>
  </tr>
  
  <!--language1-->
  <tr>
    <td>
      Language 1:
    </td>
    <td>
      <select class="form-control" name="language_name1">
        <option>--Select--</option>
        <option <?php if($language_name1=='bangla') echo "selected"; ?> value='bangla'>Bangla</option>
        <option <?php if($language_name1=='english') echo "selected"; ?> value='english'>English</option>
        <option <?php if($language_name1=='hindi') echo "selected"; ?> value='hindi'>Hindi</option>
        <option <?php if($language_name1=='japanese') echo "selected"; ?> value='japanese'>japanese</option>
      </select>
    </td>
  </tr>
  
  <tr>
    <td>Reading Skill</td>
    <td>
      <select class="form-control" name="read_skill1">
        <option>--Select--</option>
        <option <?php if($read_skill1=='high') echo "selected"; ?> value='high'>High</option>
        <option <?php if($read_skill1=='medium') echo "selected"; ?> value='medium'>Medium</option>
        <option <?php if($read_skill1=='low') echo "selected"; ?> value='low'>Low</option>
      </select>
    </td>
  </tr>
  
  <tr>
    <td>Writing Skill</td>
    <td>
      <select class="form-control" name="write_skill1">
        <option>--Select--</option>
        <option <?php if($write_skill1=='high') echo "selected"; ?> value='high'>High</option>
        <option <?php if($write_skill1=='medium') echo "selected"; ?> value='medium'>Medium</option>
        <option <?php if($write_skill1=='low') echo "selected"; ?> value='low'>Low</option>
      </select>
    </td>
  </tr>
  
  <tr>
    <td>Speaking Skill</td>
    <td>
      <select class="form-control" name="speak_skill1">
        <option>--Select--</option>
        <option <?php if($speak_skill1=='high') echo "selected"; ?> value='high'>High</option>
        <option <?php if($speak_skill1=='medium') echo "selected"; ?> value='medium'>Medium</option>
        <option <?php if($speak_skill1=='low') echo "selected"; ?> value='low'>Low</option>
      </select>
    </td>
  </tr>
  
  <!--language2-->
  <tr>
    <td>
      Language 2:
    </td>
    <td>
      <select class="form-control" name="language_name2">
        <option>--Select--</option>
        <option <?php if($language_name2=='bangla') echo "selected"; ?> value='bangla'>Bangla</option>
        <option <?php if($language_name2=='english') echo "selected"; ?> value='english'>English</option>
        <option <?php if($language_name2=='hindi') echo "selected"; ?> value='hindi'>Hindi</option>
        <option <?php if($language_name2=='japanese') echo "selected"; ?> value='japanese'>japanese</option>
      </select>
    </td>
  </tr>
  
  <tr>
    <td>Reading Skill</td>
    <td>
      <select class="form-control" name="read_skill2">
        <option>--Select--</option>
        <option <?php if($read_skill2=='high') echo "selected"; ?> value='high'>High</option>
        <option <?php if($read_skill2=='medium') echo "selected"; ?> value='medium'>Medium</option>
        <option <?php if($read_skill2=='low') echo "selected"; ?> value='low'>Low</option>
      </select>
    </td>
  </tr>
  
  <tr>
    <td>Writing Skill</td>
    <td>
      <select class="form-control" name="write_skill2">
        <option>--Select--</option>
        <option <?php if($write_skill2=='high') echo "selected"; ?> value='high'>High</option>
        <option <?php if($write_skill2=='medium') echo "selected"; ?> value='medium'>Medium</option>
        <option <?php if($write_skill2=='low') echo "selected"; ?> value='low'>Low</option>
      </select>
    </td>
  </tr>
  
  <tr>
    <td>Speaking Skill</td>
    <td>
      <select class="form-control" name="speak_skill2">
        <option>--Select--</option>
        <option <?php if($speak_skill2=='high') echo "selected"; ?> value='high'>High</option>
        <option <?php if($speak_skill2=='medium') echo "selected"; ?> value='medium'>Medium</option>
        <option <?php if($speak_skill2=='low') echo "selected"; ?> value='low'>Low</option>
      </select>
    </td>
  </tr>
  
  <!--language3-->
  <tr>
    <td>
      Language 3:
    </td>
    <td>
      <select class="form-control" name="language_name3">
        <option>--Select--</option>
        <option <?php if($language_name3=='bangla') echo "selected"; ?> value='bangla'>Bangla</option>
        <option <?php if($language_name3=='english') echo "selected"; ?> value='english'>English</option>
        <option <?php if($language_name3=='hindi') echo "selected"; ?> value='hindi'>Hindi</option>
        <option <?php if($language_name3=='japanese') echo "selected"; ?> value='japanese'>japanese</option>
      </select>
    </td>
  </tr>
  
  <tr>
    <td>Reading Skill</td>
    <td>
      <select class="form-control" name="read_skill3">
        <option>--Select--</option>
        <option <?php if($read_skill3=='high') echo "selected"; ?> value='high'>High</option>
        <option <?php if($read_skill3=='medium') echo "selected"; ?> value='medium'>Medium</option>
        <option <?php if($read_skill3=='low') echo "selected"; ?> value='low'>Low</option>
      </select>
    </td>
  </tr>
  
  <tr>
    <td>Writing Skill</td>
    <td>
      <select class="form-control" name="write_skill3">
        <option>--Select--</option>
        <option <?php if($write_skill3=='high') echo "selected"; ?> value='high'>High</option>
        <option <?php if($write_skill3=='medium') echo "selected"; ?> value='medium'>Medium</option>
        <option <?php if($write_skill3=='low') echo "selected"; ?> value='low'>Low</option>
      </select>
    </td>
  </tr>
  
  <tr>
    <td>Speaking Skill</td>
    <td>
      <select class="form-control" name="speak_skill3">
        <option>--Select--</option>
        <option <?php if($speak_skill3=='high') echo "selected"; ?> value='high'>High</option>
        <option <?php if($speak_skill3=='medium') echo "selected"; ?> value='medium'>Medium</option>
        <option <?php if($speak_skill3=='low') echo "selected"; ?> value='low'>Low</option>
      </select>
    </td>
  </tr>

  <!--  Emergency Contact -->
  
  <tr>
    <td colspan="2"><h3> Emergency Contact</h3> </td>
  </tr>
  
  <tr>
    <td>
      Name:
    </td>
    <td>
      <input class="form-control" type="text" name="emrgnc_name" value="<?php echo $emrgnc_name; ?>" />
    </td>
  </tr>
  
  <tr>
    <td>
      Position Held:
    </td>
    <td>
      <input class="form-control" type="text" name="emrgnc_position_held" value="<?php echo $emrgnc_position_held; ?>"/>
    </td>
  </tr>
  
  <tr>
    <td>
      Organization/Address:
    </td>
    <td>
      <input class="form-control" type="text" name="emrgnc_address" value="<?php echo $emrgnc_address; ?>" />
    </td>
  </tr>
  
  <tr>
    <td>
      Mobile:
    </td>
    <td>
      <input class="form-control" type="text" name="emrgnc_mobile" value="<?php echo $emrgnc_mobile; ?>" />
    </td>
  </tr>
  
  <tr>
    <td>E-mail:</td>
    <td> <input class="form-control" type="email" name="emrgnc_email" value="<?php echo $emrgnc_email; ?>"/></td>
  </tr>
  
  <tr>
    <td>
      Relation:
    </td>
    <td>
      <input class="form-control" type="text" name="relation" value="<?php echo $relation; ?>"/>
    </td>
  </tr>
  
</table>

<div class="form-group row">
    <div class="col-md-12 d-flex justify-content-center">
      <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
    </div>
</div>

{!! Form::close() !!}