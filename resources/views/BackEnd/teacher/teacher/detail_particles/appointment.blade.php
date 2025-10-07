@php
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

<div class="header d-flex justify-content-between align-items-center">
    <h3>Appointment History</h3>
    <button class="btn btn-default flex-nowrap" type="button" onclick="PrintElem('#print_details4')"><i class="fa fa-print"></i></button>
</div>

<div id="print_details4">     
    
    <h4>Appointment History</h4>

    <table  align="center" width="100%" class="table table-hover">
        
        <tr>
            <td colspan="2"><h3>Lecturer</h3></td>
        </tr>
        
        <tr>
            <td colspan="2"><b> B.C.S </b></td>
            <tr>
                <td>Appointment Type :</td>
                <td><?php echo $appointment_type; ?></td>
            </tr>
            
            <tr>
                <td>BCS No:</td>
                <td><?php echo $bcs_no; ?></td>
            </tr>
            
            <tr>
                <td>Merit/Position :</td>
                <td><?php echo $bcs_position; ?></td>
            </tr>
            
            <tr>
                <td>G.O. No :</td>
                <td><?php echo $bcs_go_no; ?></td>
            </tr>
            
            <tr>
                <td>Date of Appointment :</td>
                <td><?php echo $bcs_appointment_date; ?></td>
            </tr>
            
            <tr>
                <td>Name of Institution:</td>
                <td><?php echo $institute_name; ?></td>
            </tr>
            
            <tr>
                <td>Date of Joining:</td>
                <td><?php echo $bcs_joining_date; ?></td>
            </tr>
            
            <tr>
                <td>Date of Ending:</td>
                <td><?php echo $bcs_ending_date; ?></td>
            </tr>
            
            <tr>
                <td>Nature of Job/Field:</td>
                <td><?php echo $bcs_job_field; ?></td>
            </tr>
            
            <!--/table-->
            
            
            <!--table border="0"-->
            
            <tr>
                <td colspan="2"><h3> Additional to above</h3>
                </td>
            </tr>
            
            <tr>
                <td colspan="2"><b> PSC & 10%</b></td>
            </tr>
            
            <tr>
                <td>PSC No:</td>
                <td><?php echo $psc_no; ?></td>
            </tr>
            
            <tr>
                <td>Merit/Position :</td>
                <td><?php echo $psc_position; ?></td>
            </tr>
            
            <tr>
                <td>G.O. No :</td>
                <td><?php echo $psc_go_no; ?></td>
            </tr>
            
            <tr>
                <td>Date of Appointment :</td>
                <td><?php echo $psc_appointment_date; ?></td>
            </tr>
            
            
            <tr>
                <td>Date of Joining:</td>
                <td><?php echo $psc_joining_date; ?></td>
            </tr>
            
            
            <!--/table-->
            
            
            <!--table border="0"-->
            
            <tr>
                <td colspan="2">
                    <h3> Nationalized Teacher</h3>
                </td>
            </tr>
            <tr>
                <td>DoJ as Private Service:</td>
                <td><?php echo $private_service; ?></td>
            </tr>
            
            <tr>
                <td>G.O. No :</td>
                <td><?php echo $additional_go_no; ?></td>
            </tr>
            
            
            <tr>
                <td>Date Of Absorption :</td>
                <td><?php echo $absorption_date; ?></td>
            </tr>
            
            <tr>
                <td>Effective Service:</td>
                <td><?php echo $effective_service; ?></td>
            </tr>
            
            
            <!--table-->
            
            <tr>
                <td colspan="2">
                    <h3>Assistant Professor (Pormotee/10%Quota)</h3>
                </td>
            </tr>
            
            <tr>
                <td>G.O. no</td>
                <td><?php echo $assistant_prof_go_no; ?></td>
            </tr>
            
            <tr>
                <td>Date :</td>
                <td><?php echo $assistant_prof_go_date; ?></td>
            </tr>
            
            <tr>
                <td>Date of Joining:</td>
                <td><?php echo $assistant_prof_joining_date; ?></td>
            </tr>
            
            
            <tr>
                <td colspan="2">  
                    <h3>Associate Professor (Pormotee/10%Quota)</h3>
                </td>
            </tr>
            
            <tr>
                <td>G.O. no</td>
                <td><?php echo $associate_prof_go_no; ?></td>
            </tr>
            
            <tr>
                <td>Date :</td>
                <td><?php echo $associate_prof_go_date; ?></td>
            </tr>
            
            <tr>
                <td>Date of Joining:</td>
                <td><?php echo $associate_prof_joining_date; ?></td>
            </tr>
            
            
            <tr>
                <td colspan="2"> <h3>Professor (Pormotee/10%Quota)</h3>
                </td>
            </tr>
            
            <tr>
                <td>G.O. no</td>
                <td><?php echo $prof_go_no; ?></td>
            </tr>
            
            <tr>
                <td>Date :</td>
                <td><?php echo $prof_go_date; ?></td>
            </tr>
            
            <tr>
                <td>Date of Joining:</td>
                <td><?php echo $prof_joining_date; ?></td>
            </tr>
            
            
        </table>
        
    </div>