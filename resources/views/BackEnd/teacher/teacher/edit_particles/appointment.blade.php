@php
    $appointment = $teacher->teacherAppointment;
    $id = $teacher->id;

    $appointment_type=$appointment->appointment_type;
	$bcs_no=$appointment->bcs_no;
    $bcs_position=$appointment->bcs_position;
	$bcs_go_no=$appointment->bcs_go_no;
    $bcs_appointment_date=$appointment->bcs_appointment_date;
    $institute_name=$appointment->institute_name;
    $bcs_joining_date=$appointment->bcs_joining_date;
    $bcs_ending_date=$appointment->bcs_ending_date;
    $bcs_job_field=$appointment->bcs_job_field;
    $psc_no=$appointment->psc_no ;
    $psc_position=$appointment->psc_position;
    $psc_go_no=$appointment->psc_go_no;
    $psc_appointment_date=$appointment->psc_appointment_date;
    $psc_joining_date=$appointment->psc_joining_date;
    $private_service=$appointment->private_service;
    $additional_go_no =$appointment->additional_go_no ;
    $absorption_date =$appointment->absorption_date;
    $effective_service=$appointment->effective_service;
    $assistant_prof_go_no=$appointment->assistant_prof_go_no;
    $assistant_prof_go_date =$appointment->assistant_prof_go_date;
    $assistant_prof_joining_date =$appointment->assistant_prof_joining_date;
    $associate_prof_go_no  =$appointment->associate_prof_go_no ;
    $associate_prof_go_date  =$appointment->associate_prof_go_date;
    $associate_prof_joining_date  =$appointment->associate_prof_joining_date;
    $prof_go_no =$appointment->prof_go_no;
    $prof_go_date =$appointment->prof_go_date;
    $prof_joining_date =$appointment->prof_joining_date;
@endphp
{{ Form::open(['route' => ['teacher.editTeacherappointmentinput', ['id'=> $id]], 'method' => 'post', 'class'=> 'form-horizontal', 'files'=> true]) }}

<h3> Appointment or Promotion</h3>

<table class="table">
    <input type="hidden" disabled="yes" name="teacher_id" value="<?php echo $id; ?>" />
    <tr>
        <td colspan="2"><h3> Lecturer</h3></td>
    </tr>
    
    <tr>
        <td colspan="2"><strong>B.C.S</strong></td>
    </tr>
    <tr>
        <td>Appointment Type :</td>
        <td>
            <select class="form-control" name="appointment_type">
                <option <?php if($appointment_type=='BCS') echo "selected"; ?> value='BCS'>BCS</option>
                <option <?php if($appointment_type=='PSC') echo "selected"; ?> value='PSC'>PSC</option>
                <option <?php if($appointment_type=='10%') echo "selected"; ?> value='10%'>10%</option>
                <option <?php if($appointment_type=='Nationalized') echo "selected"; ?> value='Nationalized'>Nationalized</option>
                <option <?php if($appointment_type=='Others') echo "selected"; ?> value='Others'>Others</option>
            </select>
        </td> 
    </tr>
    <tr>
        <td>BCS No:</td>
        <td> <input class="form-control" type="text" name="bcs_no" value="<?php echo $bcs_no; ?>"/></td>
    </tr>
    
    <tr>
        <td>Merit/Position :</td>
        <td> <input class="form-control" type="text" name="bcs_position" value="<?php echo $bcs_position; ?>"/></td>
    </tr>
    
    <tr>
        <td>G.O. No :</td>
        <td> <input class="form-control" type="text" name="bcs_go_no" value="<?php echo $bcs_go_no; ?>"/>
        </td>
    </tr>
    
    <tr>
        <td>Date of Appointment :</td>
        <td> <input class="form-control datepickr" type="text" name="bcs_appointment_date" value="<?php echo $bcs_appointment_date; ?>"/>
        </td>
    </tr>
    
    <tr>
        <td>Name of Institution:</td>
        <td> <input class="form-control" type="text" name="institute_name" value="<?php echo $institute_name; ?>"/>
        </td>
    </tr>
    
    <tr>
        <td>Date of Joining:</td>
        <td>
            <input class="form-control datepickr" type="text" name="bcs_joining_date" value="<?php echo $bcs_joining_date; ?>"/></td>
        </tr>
        
        <tr>
            <td>Date of Ending:</td>
            <td>
                <input class="form-control datepickr" type="text" name="bcs_ending_date" value="<?php echo $bcs_ending_date; ?>"/>
            </td>
        </tr>
        
        <tr>
            <td>Nature of Job/Field:</td>
            <td> 
                <input class="form-control" type="text" name="bcs_job_field" value="<?php echo $bcs_job_field; ?>"/>
            </td>
        </tr>
        
        
        <tr>
            <td colspan="2">
                <h3> Additional to above</h3>
            </td>
        </tr>
        
        <tr>
            <td colspan="2"> <strong>PSC & 10%</strong> </td>
        </tr>
        
        <tr>
            <td>PSC No:</td>
            <td><input class="form-control" type="text" name="psc_no" value="<?php echo $psc_no; ?>" />
            </td>
        </tr>
        
        <tr>
            <td>Merit/Position :</td>
            <td>
                <input class="form-control" type="text" name="psc_position" value="<?php echo $psc_position; ?>" />
            </td>
        </tr>
        
        <tr>
            <td>G.O. No :</td>
            <td> <input class="form-control" type="text" name="psc_go_no" value="<?php echo $psc_go_no; ?>"/>
            </td>
        </tr>
        
        <tr>
            <td>Date of Appointment :</td>
            <td> <input class="form-control datepickr" type="text" name="psc_appointment_date" value="<?php echo $psc_appointment_date; ?>" />
            </td>
        </tr>
        
        
        <tr>
            <td>Date of Joining:</td>
            <td>
                <input class="form-control datepickr" type="text" name="psc_joining_date" value="<?php echo $psc_joining_date; ?>"/>
            </td>
        </tr>
        
        <tr>
            <td colspan="2">
                <h3> Nationalized Teacher</h3>
            </td>
        </tr>
        <tr>
            <td>DoJ as Private Service:</td>
            <td>
                <input class="form-control" type="text" name="private_service" value="<?php echo $private_service; ?>"/>
            </td>
        </tr>
        
        <tr>
            <td>G.O. No :</td>
            <td> <input class="form-control" type="text" name="additional_go_no" value="<?php echo $additional_go_no; ?>"/>
            </td>
        </tr>
        
        
        <tr>
            <td>Date Of Absorption :</td>
            <td>
                <input class="form-control datepickr" type="text" name="absorption_date" value="<?php echo $absorption_date; ?>"/>
            </td>
        </tr>
        
        <tr>
            <td>Effective Service:</td>
            <td><input class="form-control" type="text" name="effective_service" value="<?php echo $effective_service; ?>"/></td>
        </tr>
        
        
        <!--table-->
        
        <tr>
            <td colspan="2">
                <h3>Assistant Professor (Pormotee/10%Quota)</h3>
            </td>
        </tr>
        
        <tr>
            <td>G.O. no</td>
            <td>
                <input class="form-control" type="text" name="assistant_prof_go_no" value="<?php echo $assistant_prof_go_no; ?>" /> 
            </td>
        </tr>
        
        <tr>
            <td>Date :</td>
            <td>
                <input class="form-control datepickr" type="text" name="assistant_prof_go_date" value="<?php echo $assistant_prof_go_date; ?>"/>
            </td>
        </tr>
        
        <tr>
            <td>Date of Joining:</td>
            <td>
                <input class="form-control datepickr" type="text" name="assistant_prof_joining_date" value="<?php echo $assistant_prof_joining_date; ?>"/>
            </td>
        </tr>
        
        
        <tr>
            <td colspan="2">  
                <h3>Associate Professor (Pormotee/10%Quota)</h3>
            </td>
        </tr>
        
        <tr>
            <td>G.O. no</td>
            <td>
                <input class="form-control" type="text" name="associate_prof_go_no" value="<?php echo $associate_prof_go_no; ?>" /> 
            </td>
        </tr>
        
        <tr>
            <td>Date :</td>
            <td>
                <input class="form-control datepickr" type="text" name="associate_prof_go_date" value="<?php echo $associate_prof_go_date; ?>" />
            </td>
        </tr>
        
        <tr>
            <td>Date of Joining:</td>
            <td>
                <input class="form-control datepickr" type="text" name="associate_prof_joining_date" value="<?php echo $associate_prof_joining_date; ?>"/>
            </td>
        </tr>
        
        
        <tr>
            <td colspan="2"> 
                <h3>Professor (Pormotee/10%Quota)</h3>
            </td>
        </tr>
        
        <tr>
            <td>G.O. no</td>
            <td>
                <input class="form-control" type="text" name="prof_go_no" value="<?php echo $prof_go_no; ?>" /> 
            </td>
        </tr>
        
        <tr>
            <td>Date :</td>
            <td>
                <input class="form-control datepickr" type="text" name="prof_go_date" value="<?php echo $prof_go_date; ?>"/>
            </td>
        </tr>
        
        <tr>
            <td>Date of Joining:</td>
            <td>
                <input class="form-control datepickr" type="text" name="prof_joining_date" value="<?php echo $prof_joining_date; ?>"/>
            </td>
        </tr>
        
        
    </table>

<div class="form-group row">
    <div class="col-md-12 d-flex justify-content-center">
      <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
    </div>
</div>

{!! Form::close() !!}