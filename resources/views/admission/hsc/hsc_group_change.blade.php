<?php 


	if ($course == 2) {

$results = DB::table('course_hsc_new')
            ->where('groups', '=', $group)
            ->Where(function($query)
            {
                $query->where('category', '=', '2')
                      ->orwhere('has_forth', '=', '1');
            })
            ->get();

	}

	 else {

$results = DB::table('course_hsc_new')->where('groups',$group )
             	                        ->where('category',$course)->get()
             	                        ;		
	}
	

	$course_codes = '';
?>



<table class="table table-bordered table-striped">
	<!-- <caption >Compulsory Courses &amp; Codes</caption> -->
	<thead >
		<tr style="background:#fffddd">
			<?php if ($group == 'Humanities'  && $course == 1) { ?>
			<th ><center>Select Any 3</center></th>
			<?php } ?>
			<th >Course Code</th>
			<th >Course Name</th>
		</tr>
	</thead>
	<tbody >
		<?php if ($course == 2) { ?>
		<tr >
		<?php
			echo "<td ><input class='form-control' style=\"width:100px;\" type=\"text\" id=\"texting\" value=\"Select\" readonly ></td>";
			//echo "<td ><select style=\"width:250px;\" id=\"selecting\" disabled=\"disabled\"><option value=\"\"><--Select--></option>";
			echo "<td ><select class='form-control' style=\"width:250px;\" name =\"selecting\" id=\"selecting\"><option value=\"\"><--Select--></option>";
			
	    	foreach($results as $result){
	    		if (strpos($result->subjects, ',') !== FALSE) {
	    			$parts = explode(",", $result->forths);
					$subjects = explode(",", $result->subjects);
					$codes = explode(",", $result->codes);
					foreach ($parts as $key => $part) {
						echo "<option value=\"$codes[$key]\">$subjects[$key]</option>";
					}
				} else {
					echo "<option value=\"".$result->codes."\">".$result->subjects."</option>";
				}
	    	}
	    	echo "</select></td>";
		?>
		</tr>
		<?php } else { ?>
		<?php foreach($results as $result){ ?>
		<?php 
			if ($group == 'Humanities' && $course == 1) {}
			elseif (strpos($result->subjects, ',') === FALSE) {
				$course_codes .= ($result->codes.',');
			}
		?>
		<tr >
			<?php 
			if(Session::has('selection_id')){
			
				$selection_id = Session::get('selection_id');
				$selection_id += 1;
				Session::put('selection_id',$selection_id);
			} else {
				Session::put('selection_id',1);
				$selection_id = 1;
			}
			//if ($group == 'arts' && $course == 1) {
				//echo "<td ><center><input type=\"checkbox\" name=\"selectivecourse[]\" id=\"check$selection_id\"></center></td>";
			//}
			if (strpos($result->subjects, ',') !== FALSE) { 
				$parts = explode(",", $result->forths);
				$subjects = explode(",", $result->subjects);
				$codes = explode(",", $result->codes);
				if ($group == 'Humanities' && $course == 1) {
    			echo "<td ><input style=\"width:100px;\" type=\"checkbox\" id=\"text$selection_id\" value=\"Select\" name=\"selectivecourse[]\"></td><td>
					<input class='form-control' style=\"width:100px;\" type=\"text\" id=\"text$selection_id\" value=\"Select\" readonly >
    			</td>

    			";
    		}

    		else {
                       echo "<td><input class='form-control' name=\"selectivecourse[]\" style=\"width:100px;\" type=\"text\" id=\"text$selection_id\" value=\"Select\" readonly >
    			</td>";
    		}
    			echo "<td ><select class='form-control' style=\"width:250px;\" id=\"select$selection_id\"><option value=\"\"><--Select--></option>";
    			foreach ($parts as $key => $part) {
    				if ($part == 1) {
    					echo "<option value=\"$codes[$key]\">$subjects[$key]</option>";
    				}
    			}
    			echo "</select></td>";
			} else { ?>
			<?php if ($group == 'Humanities' && $course == 1) { echo "<td > <input style=\"width:100px;\" type=\"checkbox\" id=\"text$selection_id\" name=\"selectivecourse[]\" value=\"".$result->codes."\" ></td>"; } ?>

				<?php if ($group == 'Humanities' && $course == 1) { echo "<td> <input class='form-control' style=\"width:100px;\" type=\"text\" id=\"text$selection_id\" value=\"".$result->codes."\" readonly ></td>"; } 

            elseif($group != 'Humanities' && $course == 1){echo "<td> <input class='form-control' style=\"width:100px;\" type=\"text\" name=\"selectivecourse[]\" id=\"text$selection_id\" value=\"".$result->codes."\" readonly ></td>"; }
		
				else{echo "<td> <input class='form-control' style=\"width:100px;\" type=\"text\" name=\"compulsorycourse[]\" id=\"text$selection_id\" value=\"".$result->codes."\" readonly ></td>"; }

				?>

				
			
			<td ><?php echo "<input class='form-control' style=\"width:235px;\" type=\"text\" id=\"textsubject$selection_id\"  value=\"".$result->subjects."\" readonly >"; ?></td>
			<?php } ?>
		</tr>
		<?php } ?>
		<?php } ?>
	</tbody>
</table>



<?php $course_codes=rtrim($course_codes,","); ?>
<?php if ($course == 0) { ?>
<input type="hidden" name="compulsory_course_codes" id="compulsory_course_codes" value="<?php echo $course_codes; ?>" />
<?php } elseif ($course == 1) { ?>
<input type="hidden" name="selective_course_codes" id="selective_course_codes" value="<?php echo $course_codes; ?>" />
<? } elseif ($course == 2) { ?>
<input type="hidden" name="optional_course_codes" id="optional_course_codes" value="<?php echo $course_codes; ?>" />
<?php } ?>





