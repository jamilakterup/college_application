@php
    $id=$teacher->id;
    $name=$teacher->name;
	$father_name=$teacher->father_name;
    $mother_name=$teacher->mother_Name;
	$birth_date=$teacher->birth_date;
    $gender=$teacher->gender;
    $marital_status=$teacher->marital_status;
    $nationality=$teacher->nationality;
    $religion=$teacher->religion;
    $present_address=$teacher->present_address;
    $permanent_address=$teacher->permanent_address;
    $home_district=$teacher->home_district;
    $phone_office=$teacher->phone_office;
    $phone_home=$teacher->phone_home;
    $personal_mobile=$teacher->personal_mobile;
    $email=$teacher->email;
    $alternate_email=$teacher->alternate_email;
    $ximage=$teacher->image;
    $spouse_name=$teacher->spouse_name;
    $relation=$teacher->relation;
    $spouse_mobile=$teacher->spouse_mobile;
    $spouse_phone=$teacher->spouse_phone;
@endphp

{{ Form::open(['route' => ['teacher.editTeacherPersonal', ['id'=> $id]], 'method' => 'post', 'class'=> 'form-horizontal', 'files'=> true]) }}

<input type="hidden" id="personal_edit" name="personal_edit" value="true">
<table class="table">
    <h3>Personal Details</h3>

    <tr>
        <td>ID</td>    
        <td><input class="form-control" type="text" disabled="yes" id="teacher_id2" value="<?php echo $id; ?>" /></td>
    </tr>
    
    <tr>  
        <td>Name</td>	
        <td><input class="form-control" name="name" type="text" id="name2" value="<?php echo $name; ?>" /></td>
    </tr>

    <tr>
        <td>Photo</td>
        <td>
            <div class="img-thumbnail mb-1" id="photo_image_pre_area" style="{{!isset($teacher) ? 'display: none;' : ($teacher->image != '' ? '' : 'display: none;')}} width: 50px;">
                <img style="height: 100%; width: 100%;" src="{{@ asset($teacher->image ?? null)}}" id="photo_image_pre" alt="Not Set Yet">
            </div>
            {!! Form::file('image', ['class'=> 'form-control','placeholder'=> 'Teacher Photo', 'onchange'=> 'viewImage(this, "photo_image_pre")']) !!}
        </td>
    </tr>
    
    
    <tr>
        <td>Father Name</td>	
        <td><input class="form-control" name="father_name" type="text" id="father_name2" value="<?php echo $father_name; ?>" /></td>
    </tr>
    
    
    <tr>
        <td>Mother Name</td>	
        <td><input class="form-control" name="mother_name" type="text" id="mother_name2" value="<?php echo $mother_name; ?>" /></td>
    </tr>
    
    <tr>    
        <td>Birth Date</td>	
        <td><input class="form-control" name="birth_date" type="text" id="birth_date2" value="<?php echo $birth_date; ?>" /></td>
    </tr>
    
    
    <tr>
        <td>Gender</td>
        <td>
            <select class="form-control" id="gender2">
                <option <?php if($gender=='male') echo "selected"; ?> value='male'>Male</option>
                <option <?php if($gender=='female') echo "selected"; ?> value='female'>Female</option>
            </select>
        </td>
    </tr>
    
    
    <tr>
        <td>Marital Status</td>
        <td>
            <select class="form-control"  id="marital_status2">
                <option <?php if($marital_status=='married') echo "selected"; ?> value='married'>Married</option>
                <option <?php if($marital_status=='unmarried') echo "selected"; ?> value='unmarried'>Unmarried</option>
                <option <?php if($marital_status=='divorced') echo "selected"; ?> value='divorced'>divorced</option>
            </select>
        </td>
    </tr>

    <tr>
        <td>Spouse Name</td>	
        <td><input class="form-control" name="spouse_name" type="text" id="spouse_name2" value="<?php echo $spouse_name; ?>"/></td>
    </tr>

    <tr>
        <td>Relation</td>	
        <td><input class="form-control" name="relation" type="text" id="relation2" value="<?php echo $relation; ?>"/></td>
    </tr>

    <tr>
        <td>Spouse Mobile</td>	
        <td><input class="form-control" name="spouse_mobile" type="text" id="spouse_mobile2" value="<?php echo $spouse_mobile; ?>"/></td>
    </tr>

    <tr>
        <td>Spouse Phone</td>	
        <td><input class="form-control" name="spouse_phone" type="text" id="spouse_phone2" value="<?php echo $spouse_phone; ?>"/></td>
    </tr>
    
    <tr>
        <td>Nationality</td>	
        <td><input class="form-control" name="nationality" type="text" id="nationality2" value="<?php echo $nationality; ?>"/></td>
    </tr>


    <tr>
        <td>Religion</td>
        <td>
            <select class="form-control" id="religion2">
                <option <?php if($religion=='Islam') echo "selected"; ?> value='Islam'>Islam</option>
                <option <?php if($religion=='Hinduism') echo "selected"; ?> value='Hinduism'>Hinduism</option>
                <option <?php if($religion=='Christianity') echo "selected"; ?> value='Christianity'>Christianity</option>
                <option <?php if($religion=='Buddhism') echo "selected"; ?> value='Buddhism'>Buddhism</option>
                <option <?php if($religion=='Others') echo "selected"; ?> value='Others'>Others</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>Present Address</td>	
        <td><input class="form-control" name="present_address" type="text" id="present_address2" value="<?php echo $present_address; ?>"/></td>
    </tr>

    <tr>
        <td>Permanent Address</td>	
        <td><input class="form-control" name="permanent_address" type="text" id="permanent_address2" value="<?php echo $permanent_address; ?>"/></td>
    </tr>



    <tr>
        <td>Home District</td>
        <td>
            <select class="form-control" name="home_district" id="home_district">
                <?php 
                $results=DB::select('select distinct district from district_thana');
                echo "<option value=''>--Home District--</option>";
                foreach($results as $result)
                {
                    if($result->district==$home_district)
                    echo "<option value='{$result->district}' selected='yes'>$result->district</option>";
                    else
                    echo "<option value='{$result->district}'>$result->district</option>";
                }
                ?>
            </select>
        </td>
    </tr>

    <tr>
        <td>Office Phone</td>	
        <td><input class="form-control" name="phone_office" type="text" id="phone_office2" value="<?php echo $phone_office; ?>"/></td>
    </tr>


    <tr>
        <td>Home Phone</td>	
        <td><input class="form-control" name="phone_home" type="text" id="phone_home2" value="<?php echo $phone_home; ?>" /></td>
    </tr>


    <tr>
        <td>Personal Mobile</td>	
        <td><input class="form-control" name="personal_mobile" type="text" id="personal_mobile2" value="<?php echo $personal_mobile; ?>"/></td>
    </tr>


    <tr>
        <td>E-mail</td>	
        <td><input class="form-control" name="email" type="text" id="email2" value="<?php echo $email; ?>"/></td>
    </tr>


    <tr>
        <td>Alternative E-mail</td>	
        <td><input class="form-control" name="alternate_email" type="text" id="alternate_email2" value="<?php echo $alternate_email; ?>"/></td>
    </tr>

</table>

<div class="form-group row">
    <div class="col-md-12 d-flex justify-content-center">
      <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
    </div>
</div>

{!! Form::close() !!}