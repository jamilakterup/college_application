@php
	switch ($category) {
		case "hsc":
			$level = selective_multiple_hsc_level();
			$faculty = selective_multiple_study_group();
			break;
		case "degree":
			$level = selective_multiple_degree_level();
			$faculty = selective_faculties();
			break;
		case "honours":
			$level = selective_multiple_honours_level();
			$faculty = selective_faculties();
			break;
			case "masters":
			$level = selective_multiple_masters_level();
			$faculty = selective_faculties();
			break;
		default:
			$level = [];
			$faculty = [];
		}
@endphp

@if($category!='')

	{!! Form::select('faculty', $faculty, null, ['class'=> 'small_form_element form-control form-control-sm', 'id'=> 'faculty']) !!}
	{!! Form::select('level', $level, null, ['class'=> 'small_form_element form-control form-control-sm']) !!}
	{!! Form::select('session', selective_multiple_session(), null, ['class'=> 'small_form_element form-control form-control-sm', 'id'=> 'session'])!!}
	{!! Form::select('type', ['1' => 'Single', '2'=> 'A4 with 9'], null, ['class'=> 'small_form_element form-control form-control-sm', 'id'=> 'type'])!!}
@endif

<?php 
$name_array_collegemate=array(
							
					"Masters 1st Year"=>"Master Part-1",
					"Masters 2nd Year"=>"Masters Final",
					"Degree 1st Year"=>"Degree 1st Year",
					"Degree 2nd Year"=>"Degree 2nd Year",
					"Degree 3rd Year"=>"Degree 3rd Year",
					"Honours 1st Year"=>"Honours 1st Year",
					"Honours 2nd Year"=>"Honours 2nd Year",
					"Honours 3rd Year"=>"Honours 3rd Year",
					"Honours 4th Year"=>"Honours 4th Year",
					"HSC 1st Year"=>"HSC 1st Year",
					"HSC 2nd Year"=>"HSC 2nd Year",
					"Humanities"		   =>"Humanities",
					"Business Studies"	   =>"Business Studies",
					"Science"	   =>"Science"

);


if($category=='hsc'){ ?>

		{!! Form::select('group_hsc', selective_multiple_study_group(), null, ['class'=> 'small_form_element form-control form-control-sm']) !!}
		<select name="level_hsc" class="small_form_element form-control form-control-sm">
		    <option value="">Level</option>
			<option value="HSC 1st Year"><?php echo $name_array_collegemate['HSC 1st Year']; ?></option>
			<option value="HSC 2nd Year"><?php echo $name_array_collegemate['HSC 2nd Year']; ?></option>
		</select>

<?php } else if($category=='degree') {?>

		{!! Form::select('group_degree', selective_faculties(), null, ['class'=> 'small_form_element form-control form-control-sm']) !!}
		
		<select name="level_degree" class="small_form_element form-control form-control-sm">
		    <option value="">Level</option>
			<option value="Degree 1st Year"><?php echo  $name_array_collegemate['Degree 1st Year']; ?></option>
			<option value="Degree 2nd Year"><?php echo  $name_array_collegemate['Degree 2nd Year']; ?></option>
	        <option value="Degree 3rd Year"><?php echo  $name_array_collegemate['Degree 3rd Year']; ?></option>
		</select> <?php
	}

		else if($category=='honours' || $category=='masters')
	{ ?>

		{!! Form::select('faculty', selective_faculties(), null, ['class'=> 'small_form_element form-control form-control-sm', 'id'=> 'faculty']) !!}
		
		{{-- {!! Form::select('dept', selective_faculties(), null, ['class'=> 'small_form_element form-control form-control-sm', 'id'=> 'dept']) !!} --}}
			

		<select name="dept" class="small_form_element form-control form-control-sm" id="dept" placeholder="Select Deptartment" style="display: none;">
			{{-- <span id="subject_show_as_faculty"></span> --}}
		</select>


		<?php if($category=='honours')
		{ ?>
			<select name="level_honours" class="small_form_element form-control form-control-sm">
			    <option value="">Level</option>
				<option value="Honours 1st Year"><?php echo $name_array_collegemate['Honours 1st Year']; ?></option>
				<option value="Honours 2nd Year"><?php echo $name_array_collegemate['Honours 2nd Year']; ?></option>
				<option value="Honours 3rd Year"><?php echo $name_array_collegemate['Honours 3rd Year']; ?></option>
				<option value="Honours 4th Year"><?php echo $name_array_collegemate['Honours 4th Year']; ?></option>
			</select> <?php
		}

		else if($category=='masters')
		{ ?>
			<select name="level_masters" class="small_form_element form-control form-control-sm">
			    <option value="">Level</option>
				<option value="Masters 1st Year"><?php echo $name_array_collegemate['Masters 1st Year']; ?></option>
				<option value="Masters 2nd Year"><?php echo $name_array_collegemate['Masters 2nd Year']; ?></option>    
			</select> <?php
		}
	}

	else
	{

	}


	if($category!='') 
	{
	    

	    echo \Form::select('session', selective_multiple_session(), null, ['class'=> 'small_form_element form-control form-control-sm', 'id'=> 'session']);

	    
	   echo "<select name='type' class='mid_form_element form-control form-control-sm'>";
			
			
		  		echo "<option value='1'>Single</option>";	
		  		echo "<option value='2'>A4 with 9</option>";				

	    echo "</select>";
	}
		
?>	
@if($category == 'honours' || $category == 'masters')
<script type="text/javascript">

$(document).on("change", '#faculty', function(e) {
    var faculty = $(this).val();

	var token = '{{csrf_token()}}';	
    $.ajax({
	    type: "GET",
	    url: "{{route('student.fact_dept.dropdown')}}",
	    data:{faculty: faculty, _token:token},
	    success: function(data){
	        $("#dept").html(data);
	        if(data !=''){
	        	$("#dept").show();
	        }else{
	        	$("#dept").hide();
	        }
	    }
	});

});
	
</script>