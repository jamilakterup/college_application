@php
    // employment
    $id = $teacher->id;
    $employment = $teacher->teacherEmployment;
	$employer_name=$employment->employer_name ;
	$employer_district=$employment->employer_district;
    $employer_thana=$employment->employer_thana;
	$nature_position=$employment->nature_position;
    $held_position =$employment->held_position ;
    $office=$employment->office;
    $responsibility=$employment->responsibility;
    $payment_scale=$employment->payment_scale;
    $present_salary=$employment->present_salary;
    $to_continue =$employment->to_continue;
    $service_area=$employment->service_area;

    // teacher personal
	$dept_name=$teacher->department ;
	$from_date=$teacher->join_date;
	$to_date=$teacher->release_date;
	$original_position =$teacher->position ;
@endphp

{{ Form::open(['route' => ['teacher.editTeacheremploymentinput', ['id'=> $id]], 'method' => 'post', 'class'=> 'form-horizontal', 'files'=> true]) }}

<h3> Current Employment</h3>
<table class="table">
    
    <tr>
        <td>Employer:</td>
        <td>
            <input class="form-control" name="employer_name" type="text" id="employer_name" value="<?php echo $employer_name; ?>"/>
        </td>
    </tr>
    
    <tr>
        <td>Job Location:</td>
        <td>
            <select id="employer_district" class="form-control" name="employer_district">
                <option value="">District</option>
                <?php 
                
                $results=DB::select('select distinct(district) from district_thana  order by district asc');
                foreach($results as $result)
                {
                    
                    if($result->district==$employer_district)
                    echo "<option value='{$result->district}' selected='yes'>$result->district</option>";
                    else
                    echo "<option value='{$result->district}'>$result->district</option>";
                }
                ?>	
            </select>
            
            <select id="employer_thana" class="form-control" name="employer_thana">
                <option value="">Thana</option>
                <?php 
                
                $results=DB::select('select distinct(thana) from district_thana  order by thana asc');
                foreach($results as $result)
                {
                    if($result->thana==$employer_thana)
                    echo "<option value='{$result->thana}' selected='yes'>$result->thana</option>";
                    else 
                    echo "<option value='{$result->thana}'>$result->thana</option>";
                }
                ?>	
            </select>
        </td>
    </tr>
    
    
    <tr>
        <td>Nature of Position:</td>
        
        <td>
            
            <select class="form-control" id="nature_position" name="nature_position">
                <option <?php if($nature_position=='Regular') echo "selected"; ?> value='Regular'>Regular</option>
                <option <?php if($nature_position=='Deputation') echo "selected"; ?> value='Deputation'>Deputation</option>
                <option <?php if($nature_position=='Lien') echo "selected"; ?> value='Lien'>Lien</option>
                <option <?php if($nature_position=='Special Officer') echo "selected"; ?> value='Special Officer'>Special Officer</option>
                <option <?php if($nature_position=='Study Leave') echo "selected"; ?> value='Study Leave'>Study Leave</option>
            </select>
        </td>
    </tr>
    
    
    <tr>
        <td>Position Held:</td>
        <td>
            <select class="form-control" id="held_position" name="held_position">
                <option value="">Select</option>
                <?php 
                
                $results=DB::select("select id,name from designation where type='teacher' or type='administrator' order by name asc");
                foreach($results as $result)
                {
                    if($result->id==$held_position)
                    echo "<option value='{$result->id}' selected='yes'>$result->name</option>";
                    else
                    echo "<option value='{$result->id}'>$result->name</option>";
                }
                ?>	
            </select>
        </td>
    </tr>
    
    
    <tr>
        <td>Original Position :</td>
        <td>
            <select class="form-control" id="original_position" name="original_position">
                <option value="">Select</option>
                <?php 
                $results=DB::select("select id,name from designation where type='teacher' or type='administrator' order by name asc");
                foreach($results as $result)
                {
                    if($result->id==$original_position)
                    echo "<option value='{$result->id}' selected='yes'>$result->name</option>";
                    else
                    echo "<option value='{$result->id}'>$result->name</option>";
                }
                ?>	
            </select>
        </td>
    </tr>
    
    <tr>
        <td>Subject/Department:</td>
        <td>
            <select class="form-control" id="dept_name" name="dept_name">
                <option value="">Select</option>
                <?php 
                
                $results=DB::select("select dept_name from departments order by dept_name asc");
                foreach($results as $result)
                {
                    if($result->dept_name==$dept_name)
                    echo "<option value='{$result->dept_name}' selected='yes'>$result->dept_name</option>";
                    else
                    echo "<option value='{$result->dept_name}'>$result->dept_name</option>";
                }
                ?>	
            </select>
        </td>
    </tr>
    
    <tr>
        <td>Office/Institution:</td>
        <td> 
            <input class="form-control" name="office" type="text" id="office" value="<?php echo $office; ?>"/>
        </td>
    </tr>
    
    
    <tr>
        <td>Responsibilities:</td>
        <td> <input class="form-control" name="responsibility" type="text" id="responsibility" value="<?php echo $responsibility; ?>"/>
        </td>
    </tr>
    
    <tr>
        <td>Scale of Payment :</td>
        <td> 
            <input class="form-control" name="payment_scale" type="text" id="payment_scale" value="<?php echo $payment_scale; ?>"/>
        </td>
    </tr>
    
    
    <tr>
        <td>Present Salary :</td>
        <td>
            <input class="form-control" name="present_salary" type="text" id="present_salary" value="<?php echo $present_salary; ?>"/>
        </td>
    </tr>
    
    <tr>
        <td>
            From:
        </td>
        <td>
            <input class="form-control datepickr" name="from_date" type="text" id="from_date" value="<?php echo $from_date; ?>" />
        </td>
    </tr>
    
    <tr>
        <td>
            To:
        </td>
        <td>
            <?php if($to_date=='0000-00-00') $to_date='';?>
            <input class="form-control datepickr" name="to_date" type="text" id="to_date" value="<?php echo $to_date; ?>" />
        </td>
    </tr>
    
    <tr>

        <td></td>
        
        <td>
            <input class="form-control" name="to_continue" type="radio" <?php if($to_date=='') echo "checked='checked'";?> id="to_continue" value="continuing" value="<?php echo $to_continue; ?>"/> Continuing </input> 
        </td>
    </tr>
    
    
    
    <tr>
        <td>Area of Service:</td>
        <td>
            <select class="form-control" id="service_area" name="service_area">
                <option <?php if($service_area=='Teaching') echo "selected"; ?> value='Teaching'>Teaching</option>
                <option <?php if($service_area=='Administrative') echo "selected"; ?> value='Administrative'>Administrative</option>
            </select>
        </td>
    </tr>
    
</table>

<div class="form-group row">
    <div class="col-md-12 d-flex justify-content-center">
      <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
    </div>
</div>

{!! Form::close() !!}