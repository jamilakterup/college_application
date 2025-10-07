@php
    $career_from=$career->career_from;
	$career_to=$career->career_to;
    $career_description =$career->career_description;
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
    $emrgnc_mobile =$career->emrgnc_mobile ;
    $emrgnc_email =$career->emrgnc_email ;
    $relation=$career->relation;
@endphp

<div class="header d-flex justify-content-between align-items-center">
    <h3>Career Summary</h3>
    <button class="btn btn-default flex-nowrap" type="button" onclick="PrintElem('#print_details5')"><i class="fa fa-print"></i></button>
</div>

<div id="print_details5">     
    
    <h4>Career Summary</h4>
    
    <table  align="center" width="100%" class="table table-hover">
        <tr>
            <td>From:</td>
            <td><?php echo $career_from; ?></td>
        </tr>
        
        <tr>
            <td>To:</td>
            <td><?php echo $career_to; ?></td>
        </tr>
        
        <tr>
            <td>Description:</td>
            <td><?php echo $career_description; ?></td>
        </tr>
        
        <tr>
            <td>
                <b> Specialization </b>
            </td>
            <td> </td>
        </tr>

        <tr>
            <td>Memo:</td>
            <td><?php echo $special_memo; ?></td>
            
        </tr>
        
        <tr>
            <td>
                <b>Extracurricular Activity </b>
            </td>
            <td> </td>
        </tr>
        
        <tr>
            <td>
                Memo:
            </td>
            <td><?php echo $activity_memo; ?></td>
        </tr>
        
        <tr>
            <td> <b> Language Proficiency</b></td>
            <td>&nbsp;</td>
            
        </tr>
        
        <!--language1-->
        <tr>
            <td>
                Language 1:
            </td>
            <td><?php echo $language_name1; ?></td>
        </tr>
        
        <tr>
            <td>Reading Skill</td>
            <td><?php echo $read_skill1; ?></td>
        </tr>
        
        <tr>
            <td>Writing Skill</td>
            <td><?php echo $write_skill1; ?></td>
        </tr>
        
        <tr>
            <td>Speaking Skill</td>
            <td><?php echo $speak_skill1; ?></td>
        </tr>

        <!--language2-->
        <tr>
            <td>
                Language 2:
            </td>
            <td><?php echo $language_name2; ?></td>
        </tr>
        
        <tr>
            <td>Reading Skill</td>
            <td><?php echo $read_skill2; ?></td>
        </tr>
        
        <tr>
            <td>Writing Skill</td>
            <td><?php echo $write_skill2; ?></td>
        </tr>
        
        <tr>
            <td>Speaking Skill</td>
            <td><?php echo $speak_skill2; ?></td>
        </tr>
        
        <!--language3-->
        <tr>
            <td>
                Language 3:
            </td>
            <td><?php echo $language_name3; ?></td>
        </tr>
        
        <tr>
            <td>Reading Skill</td>
            <td><?php echo $read_skill3; ?></td>
        </tr>
        
        <tr>
            <td>Writing Skill</td>
            <td><?php echo $write_skill3; ?></td>
        </tr>
        
        <tr>
            <td>Speaking Skill</td>
            <td><?php echo $speak_skill3; ?></td>
        </tr>
        
        
        <!--  Emergency Contact -->
        
        <tr>
            <td><b> Emergency Contact</b> </td>
            <td>&nbsp;</td>
        </tr>
        
        <tr>
            <td>
                Name:
            </td>
            <td><?php echo $emrgnc_name; ?></td>
        </tr>
        
        <tr>
            <td>
                Position Held:
            </td>
            <td><?php echo $emrgnc_position_held; ?></td>
        </tr>

        <tr>
            <td>
                Organization/Address:
            </td>
            <td><?php echo $emrgnc_address; ?></td>
        </tr>

        <tr>
            <td>
                Mobile:
            </td>
            <td><?php echo $emrgnc_mobile; ?></td>
        </tr>
        
        <tr>
            <td>E-mail:</td>
            <td><?php echo $emrgnc_email; ?></td>
        </tr>
        
        <tr>
            <td>
                Relation:
            </td>
            <td><?php echo $relation; ?></td>
        </tr>
    </table>

</div>