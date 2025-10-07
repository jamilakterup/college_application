<div class="header d-flex justify-content-between align-items-center">
    <h4>Personal Details</h4>
    <button class="btn btn-default flex-nowrap" type="button" onclick="PrintElem('#print_details')"><i class="fa fa-print"></i></button>
</div>
<div id='print_details'>
    <table class="table table-hover" style="width: 100%;">
        <tr>
            <td >Photo</td>
            <td ><center><img class="img-fluid" src="{{ URL::to('/') }}/{{$details->image}}" alt="Photo" height="120px" width="100px"></center><br/></td>
        </tr>
        <tr>
            <td>ID</td> 
            <td>{{$details->id}}</td>
        </tr>
        
        <tr>
            <td>Name</td>   
            <td>{{$details->name}}</td>
        </tr>

        <tr>
            <td>Father Name</td>    
            <td>{{$details->father_name}}</td>
        </tr>
        <tr>
            <td>Mother Name</td>    
            <td>{{$details->mother_Name}}</td>
        </tr>
        
        <tr>    
            <td>Birth Date</td> 
            <td>{{$details->birth_date}}</td>
        </tr>

        <tr>
            <td>Gender</td>
            <td>{{$details->gender}}</td>
        </tr>

        <tr>
            <td>Marital Status</td>
            <td><?php echo $details->marital_status; ?></td>
        </tr>


        <tr>
            <td>Spouse Name</td>
            <td><?php echo $details->spouse_name; ?></td>
        </tr>
        <tr>
            <td>Relation</td>
            <td><?php echo $details->relation; ?></td>
        </tr>
        <tr>
            <td>Spouse Mobile</td>
            <td><?php echo $details->spouse_mobile; ?></td>
        </tr>
        <tr>
            <td>Spouse Phone Status</td>
            <td><?php echo $details->spouse_phone; ?></td>
        </tr>

        <tr>
            <td>Nationality</td>    
            <td><?php echo $details->nationality; ?></td>
        </tr>
        
        
        <tr>
            <td>Religion</td>
            <td><?php echo $details->religion; ?></td>
        </tr>

        <tr>
            <td>Present Address</td>    
            <td><?php echo $details->present_address; ?></td>
        </tr>
        
        <tr>
            <td>Permanent Address</td>  
            <td><?php echo $details->permanent_address; ?></td>
        </tr>
        
        <tr>
            <td>Home District</td>
            <td><?php echo $details->home_district; ?></td>
        </tr>
        
        <tr>
            <td>Office Phone</td>   
            <td><?php echo $details->phone_office; ?></td>
        </tr>

        <tr>
            <td>Home Phone</td> 
            <td><?php echo $details->phone_home; ?></td>
        </tr>
        
        <tr>
            <td>Personal Mobile</td>    
            <td><?php echo $details->personal_mobile; ?></td>
        </tr>
    
        <tr>
            <td>E-mail</td> 
            <td><?php echo $details->email; ?></td>
        </tr>
    
        <tr>
            <td>Alternative E-mail</td> 
            <td><?php echo $details->alternate_email; ?></td>
        </tr>
    </table>
</div>