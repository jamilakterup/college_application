@php
    $held_position_name='';
    $original_position_name='';
    $joining_date='';
    $ending_date='';
    $employer_name=$employment->employer_name;
    $employer_district=$employment->employer_district;
    $employer_thana=$employment->employer_thana;
    $nature_position=$employment->nature_position;
    $held_position=$employment->held_position;
    // $original_position=$employment->original_position;
    // $dept_name=$employment->dept_name;
    $office=$employment->office;
    $responsibility=$employment->responsibility;
    $payment_scale=$employment->payment_scale;
    $present_salary =$employment->present_salary;
    // $joining_date=$result->joining_date;
    // $ending_date=$result->ending_date;
    // $from_date=$result->from_date;
    // $to_date =$result->to_date;
    $to_continue=$employment->to_continue;
    $service_area =$employment->service_area;

    $dept_name=$details->department ;
    $from_date=$details->join_date;
    $to_date=$details->release_date;
    $original_position =$details->position ;


    $results=DB::select('select * from designation WHERE id="'.$held_position.'"');
    foreach($results as $result)
    {
        $held_position_name=$result->name;
        break;
    }


    $results=DB::select('select * from designation WHERE id="'.$original_position.'"');
    foreach($results as $result)
    {
        $original_position_name=$result->name;
        break;
    }
@endphp
<div class="header d-flex justify-content-between align-items-center">
    <h3>Employment History</h3>
    <button class="btn btn-default flex-nowrap" type="button" onclick="PrintElem('#print_details3')"><i class="fa fa-print"></i></button>
</div>

<div id="print_details3">   
    
    <h4> Employment History</h4>

    <table  align="center" width="100%" class="table table-hover">
        <tr>
            <td>Employer:</td>
            <td><?php echo $employer_name; ?></td>
        </tr>
        
        <tr>
            <td>Job Location:</td>
            <td><?php echo $employer_district; ?></td>
        </tr>
        
        <tr>
            <td>Thana:</td>
            <td><?php echo $employer_thana; ?></td>
        </tr>
        
        
        <tr>
            <td>Nature of Position:</td>
            <td><?php echo $nature_position; ?></td>
        </tr>
        
        
        <tr>
            <td>Position Held:</td>
            <td><?php echo $held_position_name; ?></td>
        </tr>
        
        <tr>
            <td>Original Position :</td>
            <td><?php echo $original_position_name; ?></td>
        </tr>
        
        
        <tr>
            <td>Subject/Department:</td>
            <td><?php echo $dept_name; ?></td>
        </tr>
        
        
        <tr>
            <td>Office/Institution:</td>
            <td><?php echo $office; ?></td>
        </tr>
        
        
        <tr>
            <td>Responsibilities:</td>
            <td><?php echo $responsibility; ?></td>
        </tr>
        
        <tr>
            <td>Scale of Payment :</td>
            <td><?php echo $payment_scale; ?></td>
        </tr>
        
        
        <tr>
            <td>Present Salary :</td>
            <td><?php echo $present_salary; ?></td>
        </tr>
        
        <!-- <tr>
            <td>Date of Joining :</td>
            <td><?php echo $joining_date; ?></td>
        </tr>
        
        <tr>
            <td>Date of Ending :</td>
            <td><?php echo $ending_date; ?></td>
        </tr> -->
        
        
        <tr>
            <td>
                From:
            </td>
            <td><?php echo $from_date; ?></td>
        </tr>
        
        <tr>
            <td>
                To:
            </td>
            <td>
                <?php if($to_continue!='') echo $to_continue;  else echo $to_date; ?></td>
            </tr>
            
            
            <tr>
                <td>Area of Service:</td>
                <td><?php echo $service_area; ?></td>
            </tr>
            
        </table>
        
        
    </div>