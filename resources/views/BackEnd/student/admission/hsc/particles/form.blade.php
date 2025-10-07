@php
	$query_present_ps = isset($student) ? ["district=" => $student->present_dist] : null;
	$query_permanent_ps = isset($student) ? ["district=" => $student->permanent_dist] : null;
@endphp

{!! Form::open(['route'=> 'students.hsc.store', 'method' => 'post', 'files'=> true, 'data-form'=> 'postForm']) !!}
<h4>Personal Info</h4>
<div class="row mb-3">
	<div class="col-lg-6">
		<div class="form-group">
			<label for="name" class="col-form-label">Name</label>
			{!! Form::text('student_name', @$student->name?? null, ['class'=> 'form-control', 'placeholder'=> 'Student Name', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
		</div>
		<div class="form-group">
			<label for="ssc_roll" class="col-form-label">SSC Roll</label>
			{!! Form::text('ssc_roll', @$student->ssc_roll ?? null, ['class'=> 'form-control', 'placeholder'=> 'SSC Roll', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
		</div>
			
		<div class="form-group">
			<label for="session" class="col-form-label">Session</label>
			{!! Form::select('session', selective_multiple_session(), $student->session ?? null, ['class' => 'form-control', 'id'=> 'session', 'required' => true, 'data-placeholder' => '--Select Session--']) !!}
			<div class="invalid-feedback"></div>
		</div>						  
		<div class="form-group">
			<label for="photo" class="col-form-label">Your picture</label>
			<input  type="file" name="photo" class="form-control" id="photo">
			<div class="invalid-feedback"></div>
		</div>

        <div class="form-group">
			<label for="father_name" class="col-form-label">Father's Name</label>
			{!! Form::text('father_name', $student->father_name ?? null, ['class'=> 'form-control', 'placeholder'=> 'Father\'s Name', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
		</div>

        <div class="form-group">
			<label for="mother_name" class="col-form-label">Mother's Name</label>
			{!! Form::text('mother_name', $student->mother_name ?? null, ['class'=> 'form-control', 'placeholder'=> 'Mother\'s Name', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
		</div>
	</div>

	<div class="col-lg-6">

		<div class="form-group">
			<label for="birth_date" class="col-form-label">Date Of Birth</label>
			{!! Form::text('birth_date', $student->birth_date ?? null, ['class'=> 'form-control date', 'placeholder'=> 'Date of Birth', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
		</div>
		<div class="form-group">
			<label for="blood_group" class="col-form-label">Blood Group</label>
			{!! Form::select('blood_group', selective_blood_lists(), $student->blood_group ?? null, ['class' => 'form-control selectize', 'id'=> 'blood_group']) !!}
				<div class="invalid-feedback"></div>
		</div>
		<div class="form-group">
			<label for="gender" class="col-form-label">Gender</label>
			{!! Form::select('gender', selective_gender_list(), $student->gender ?? null, ['class' => 'form-control selectize', 'id'=> 'gender']) !!}
			<div class="invalid-feedback"></div>
		</div>
		
		<div class="form-group">
			<label for="religion" class="col-form-label">Religion</label>
			{!! Form::select('religion', selective_religion_list(), $student->religion ?? null, ['class' => 'form-control selectize', 'id'=> 'gender']) !!}
				<div class="invalid-feedback"></div>
		</div>
		<div class="form-group">
			<label for="student_mobile" class="col-form-label">Mobile Number</label>
			{!! Form::text('student_mobile', $student->contact_no ?? null, ['class'=> 'form-control', 'placeholder'=> 'Student Mobile', 'required'=> true, 'maxlength' => '11']) !!}
			<div class="invalid-feedback"></div>
		</div>
	</div>
</div>

<h4>Address Info</h4>
<div class="row">
	<div class="col-lg-6">
		<label></label>
		<div class="form-group">
			<label for="present_village" class="col-form-label">Present Village</label>
			{!! Form::text('present_village', $student->present_village ?? null, ['class'=> 'form-control', 'placeholder'=> 'Present Village', 'required'=> true, 'id'=> 'present_village']) !!}
			<div class="invalid-feedback"></div>
		</div>
		<div class="form-group">
			<label for="present_po" class="col-form-label">Present Post Office</label>
			{!! Form::text('present_po', $student->present_po ?? null, ['class'=> 'form-control', 'placeholder'=> 'Present Post Office', 'required'=> true, 'id' => 'present_po']) !!}
			<div class="invalid-feedback"></div>
		</div>
		
		<div class="form-group">
			<label for="present_dist" class="col-form-label">Present District</label>
			{!! Form::select('present_dist', create_option_array('district_thana', 'district', 'district', 'District'), $student->present_dist ?? null, ['class' => 'form-control', 'onchange'=> 'getThanaOption(this);' , 'data-for'=> 'present_ps', 'id'=> 'present_dist']) !!}
			<div class="invalid-feedback"></div>
		</div>

		<div class="form-group">
			<label for="present_ps" class="col-form-label">Present Thana</label>
			{!! Form::select('present_ps', create_option_array('district_thana', 'thana', 'thana', 'Thana', $query_present_ps), $student->present_ps ?? null, ['class' => 'form-control', 'id'=> 'present_ps', 'data-placeholder'=> '--Select Thana--']) !!}
			<div class="invalid-feedback"></div>
		</div>
	</div>

	<div class="col-lg-6">
		<input type="checkbox" name="same_as_present" id="same_as_present"> <label for="same_as_present">Same as Present Address</label>
		<div class="form-group">
			<label for="permanent_village" class="col-form-label">Permanent Village</label>
			{!! Form::text('permanent_village', $student->permanent_village ?? null, ['class'=> 'form-control', 'placeholder'=> 'Permanent Village', 'required'=> true, 'id'=> 'permanent_village']) !!}
			<div class="invalid-feedback"></div>
		</div>
		<div class="form-group">
			<label for="permanent_po" class="col-form-label">Permanent Post Office</label>
			{!! Form::text('permanent_po', $student->permanent_po ?? null, ['class'=> 'form-control', 'placeholder'=> 'Permanent Post Office', 'required'=> true, 'id' => 'permanent_po']) !!}
			<div class="invalid-feedback"></div>
		</div>
		
		<div class="form-group">
			<label for="permanent_dist" class="col-form-label">Permanent District</label>
			{!! Form::select('permanent_dist', create_option_array('district_thana', 'district', 'district', 'District'), $student->permanent_dist ?? null, ['class' => 'form-control','onchange'=> 'getThanaOption(this);' , 'data-for'=> 'permanent_ps', 'id' => 'permanent_dist']) !!}
			<div class="invalid-feedback"></div>
		</div>

		<div class="form-group">
			<label for="permanent_ps" class="col-form-label">Permanent Thana</label>
			{!! Form::select('permanent_ps', create_option_array('district_thana', 'thana', 'thana', 'Thana', $query_permanent_ps), $student->permanent_ps ?? null, ['class' => 'form-control', 'id'=> 'permanent_ps', 'data-placeholder'=> '--Select Thana--']) !!}
			<div class="invalid-feedback"></div>
		</div>
	</div>
</div>

<h4>SSC Info</h4>
<div class="row">
	<div class="col-lg-6">
        <div class="form-group">
			<label for="ssc_registration" class="col-form-label">SSC Registration</label>
			{!! Form::text('ssc_registration', $student->ssc_registration ?? null, ['class'=> 'form-control', 'placeholder'=> 'SSC Registration', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
		</div>

        <div class="form-group">
			<label for="ssc_roll" class="col-form-label">SSC Roll</label>
			{!! Form::text('ssc_roll', $student->ssc_roll ?? null, ['class'=> 'form-control', 'placeholder'=> 'SSC Roll', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
		</div>
        

        <div class="form-group">
			<label for="ssc_gpa" class="col-form-label">SSC GPA</label>
			{!! Form::text('ssc_gpa', $student->gpa ?? null, ['class'=> 'form-control', 'placeholder'=> 'SSC GPA', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
		</div>

        <div class="form-group">
			<label for="ssc_passing_year" class="col-form-label">SSC Passing Year</label>
			{!! Form::select('ssc_passing_year',selective_multiple_passing_year(), $student->ssc_passing_year ?? null ,['class'=> 'form-control selectize get_options', 'data-placeholder'=>'--Select Passing Year--', 'id'=> 'passing_year']) !!}
			<div class="invalid-feedback"></div>
		</div>
    </div>

    <div class="col-lg-6">
        <div class="form-group">
			<label for="ssc_institute" class="col-form-label">SSC Institute</label>
			{!! Form::text('ssc_institute', $student->ssc_institute ?? null, ['class'=> 'form-control', 'placeholder'=> 'SSC Institute', 'required'=> true]) !!}
			<div class="invalid-feedback"></div>
		</div>

        <div class="form-group">
			<label for="ssc_group" class="col-form-label">SSC Group</label>
			{!! Form::select('ssc_group',selective_multiple_study_group(), $student->ssc_group ?? null ,['class'=> 'form-control selectize get_options', 'data-placeholder'=>'--Select SSC Group--', 'id'=> 'ssc_group']) !!}
			<div class="invalid-feedback"></div>
		</div>

        <div class="form-group">
			<label for="ssc_board" class="col-form-label">SSC Board</label>
			{!! Form::select('ssc_board',selective_boards(), $student->ssc_board ?? null ,['class'=> 'form-control selectize get_options', 'data-placeholder'=>'--Select SSC Board--', 'id'=> 'ssc_board']) !!}
			<div class="invalid-feedback"></div>
		</div>

        <div class="form-group">
			<label for="ssc_session" class="col-form-label">SSC Session</label>
            {!! Form::select('ssc_session', selective_multiple_session(), $student->ssc_session ?? null, ['class' => 'form-control selectize', 'id'=> 'ssc_session', 'required' => true, 'data-placeholder'=> '--Select Session--']) !!}
			<div class="invalid-feedback"></div>
		</div>
    </div>
</div>

<h4 class="mt-4">Admission Info</h4>
<div class="row">
    <div class="col-12">
        <div class="form-group row">
            <label for="hsc_group" required="required" class="col-sm-4 control-label">HSC Group :</label>
            <div class="col-sm-8">
                {!! Form::select('hsc_group', selective_multiple_study_group(), null, ['class' => 'form-control', 'id'=> 'hsc_group', 'required'=> true]) !!}
                {!!invalid_feedback('hsc_group')!!}
            </div>
        </div>
        <div class="form-group row">
            <label for="form_current_level" class="col-sm-4 control-label">Current Level :</label>
            <div class="col-sm-8">
                {!! Form::select('form_current_level', selective_multiple_hsc_level(), null, ['class' => 'form-control', 'id'=> 'form_current_level', 'required'=> true]) !!}
                {!!invalid_feedback('form_current_level')!!}
            </div>
        </div>						  
        <div class="form-group row">
            <label for="compulsory_course_codes" class="col-sm-4 control-label">Compulsory Course Info :</label>
            <div class="col-sm-8">
                <div class="compulsory_course_codes">   
                    <table  class="table table-bordered table-striped">
                        <!-- <caption >Compulsory Courses &amp; Codes</caption> -->
                        <thead >
                            <tr style="background:#fffddd">
                                <th>Course Code</th>
                                <th >Course Name</th>
                            </tr>
                        </thead>
                        <tbody >
                            <tr>
                                <td >ex: 101-102</td>
                                <td >ex: Bangla</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="help-block"></div>
            </div>
        </div>
        
        <div class="form-group row">
            <label for="selective_course_codes" class="col-sm-4 control-label">Selective Course Info :</label>
            <div class="col-sm-8">
                <div class="selective_course_codes"> 
                    <table class="table table-bordered table-striped">
                        <!-- <caption>Selective Courses &amp; Codes</caption> -->
                        <thead >
                            <tr style="background:#fffddd">
                                <th >Course Code</th>
                                <th >Course Name</th>
                            </tr>
                        </thead>
                        <tbody >
                            <tr>
                                <td >ex: 174-175</td>
                                <td >ex: Physics</td>
                            </tr>
                        </tbody>
                    </table>
                </div>  
                <div class="help-block"></div>
            </div>
        </div>
        
        <div class="form-group row">
            <label for="optional_course_codes" class="col-sm-4 control-label">Optional Course Info :</label>
            <div class="col-sm-8">
                <div class="optional_course_codes">
                    <table  class="table table-bordered table-striped">
                        <!-- <caption>Optional Courses &amp; Codes</caption> -->
                        <thead >
                            <tr style="background:#fffddd">
                                <th >Course Code</th>
                                <th >Course Name</th>
                            </tr>
                        </thead>
                        <tbody >
                            <tr>
                                <td >ex: 178-179</td>
                                <td >ex: Biology</td>
                            </tr>
                        </tbody>
                    </table>
                </div> 
                <div class="help-block"></div>
            </div>
        </div>
    </div>
</div>

<div class="float-right clear mb-2">
    {!! Form::submit('Save Data', ['class'=> 'btn btn-primary','data-value'=> 'create', 'data-button'=> 'save']) !!}
</div>
{!! Form::close() !!}

<script type="text/javascript">



	$('form').on('submit', function() {
		var compulsory_course_codes = '';
		var selective_course_codes = '';
		var optional_course_codes = '';

		var val = $("#hsc_group").val();
		var prev = '';

		if ( (val == 'Science') || (val == 'Business Studies') ) 
		{

			var selective_course_code = $(".selective_course_codes select").val();

			if (selective_course_code == '') {
				alert("Selective Courses must be selected properly");
				return false;
			}


			var optional_course_code = $(".optional_course_codes select").val();
			if (optional_course_code == '') {
				alert("optional Course must be selected properly");
				return false;
			}

			$(".selective_course_codes select > option").each(function() {
				if (this.value != '') {
					var s = $('#selective_course_codes').val();
					s = s.replace("," + this.value, "");
					$('#selective_course_codes').val(s);
				}
			});

			if (!($('#selective_course_codes').val().indexOf(selective_course_code) > (-1))) {
				$('#selective_course_codes').val($('#selective_course_codes').val() + "," + selective_course_code);
			}

			compulsory_course_codes = $('#compulsory_course_codes').val();
			selective_course_codes = $('#selective_course_codes').val();
			optional_course_codes = $('#optional_course_codes').val();
        //alert(selective_course_codes);
    } 

    else if (val == 'Humanities')
    {
    	var count = $('.selective_course_codes input[type=checkbox]:checked').length;
    	if (count < 3) {
    		alert("At-least 3 of the selective subjects must be chosen");
    		return false;
    	} 
    	else 
    	{
    		var selective_course_codes = "";
    		var a = [];
    		$('.selective_course_codes input[type=checkbox]:checked').each(function() {
    			var id = $(this).attr('id').replace(/check/, '');
    			var option = $("#text"+id).val();
    			a.push(option);
    		});
    		selective_course_codes = a.join(',');
    		$('#selective_course_codes').val(selective_course_codes);
    	}

    	var optional_course_code = $(".optional_course_codes select").val();
    	if (optional_course_code == '') 
    	{
    		alert("Optional Course must be selected");
    		return;
    	}

    	compulsory_course_codes = $('#compulsory_course_codes').val();
    	selective_course_codes = $('#selective_course_codes').val();
    	optional_course_codes = $('#optional_course_codes').val();
    }
    var mobile = $('#student_mobile').val();
    var mobile_number_confirmation = $('#student_mobile_re').val();
    var password = $('#inputPassword').val();
    var password_confirmation = $('#inputPasswordConfirm').val();

    if(mobile!=mobile_number_confirmation){
    	$('.mobile_number_confirmation_error').html('<span style="color:red;">Mobile Number  Mismatch</span>');
    	return false;
    } else {
    	$('.mobile_number_confirmation_error').html('');
    }

    if(password!=password_confirmation){
    	$('.confirm_password_error').html('<span style="color:red;">Password  Mismatch</span>');
    	return false;
    } else {
    	$('.confirm_password_error').html('');
    }


    $(':disabled').each(function(e) {
    	$(this).removeAttr('disabled');
    })
});

    $(document).on('change', '#hsc_group', function (e) {
		var val = $("#hsc_group").val();
		if ( (val == 'Science') || (val == 'Business Studies') ) {
			$(".selective_course_codes select").off();
			$(document).on('change','.selective_course_codes select',function(){
				var option = $(this).val();
				var id = $(this).attr('id').replace(/select/, '');
          //alert(id);
          $("input[type=text]#text"+id).attr('value', option);
          if (option == '') {
          	option = "Select";
          	$(".optional_course_codes select").val('');
          	$(".optional_course_codes select option").removeAttr('disabled');
          	$(".optional_course_codes select").attr('disabled','disabled');
          } else {
          	$(".optional_course_codes select").val('');
          	$(".optional_course_codes select option").removeAttr('disabled');
          	$(".optional_course_codes select").removeAttr('disabled');
          	$(".optional_course_codes select option[value='" + option + "']").attr('disabled','true');
          }
          $("input[type=text]#text"+id).val(option);
      });
			$(document).on('change','.optional_course_codes #selecting',function(){
        	//alert('arts');
        	var option = $(this).val();
        	$("#optional_course_codes").attr('value', option);
        	if (option == '') option = "Select";
        	$("#texting").val(option);
        });
		} else if (val == 'Humanities') {
        //alert('arts');
        $(".selective_course_codes select").off();
        $(document).on('change','.selective_course_codes select',function(){       	
        	var option = $(this).val();
        	if (option == '') option = "Select";
        	var id = $(this).attr('id').replace(/select/, '');
        	$("input[type=text]#text"+id).val(option).change();
        	$("input[type='checkbox']#text"+id).val(option).change();
        });
        $(document).on('change','.optional_course_codes #selecting',function(){
        	var option = $(this).val();
        	$("#optional_course_codes").attr('value', option);
        	if (option == '') option = "Select";
        	$("#texting").val(option);
        });
        $(document).on('change','.selective_course_codes input[type=checkbox]',function(){ 
        	var count = $('.selective_course_codes input[type=checkbox]:checked').length;
        	if (count == 4) {
        		alert("You can only select 3 of these selective subjects");
        		$(this).prop('checked',false);
        		return;
        	}
        	if (count == 3) $(".optional_course_codes #selecting").removeAttr('disabled');
        	else {
        		$(".optional_course_codes #selecting").prop("selectedIndex", 0);
        		$(".optional_course_codes #selecting").attr('disabled','disabled');

        	}
        	var id = $(this).attr('id').replace(/check/, '');
        	var option = $(this).val();

        	if (option == "Select") {
        		alert("Please select a subject 1st, and then; tick the related checkbox");
        		$(this).prop('checked',false);
        	} else {
        		if ($(this).prop('checked') == true) {

        			var intId = id.match(/\d+/);

        			$("#select"+intId).attr('disabled','true');
        			$("#select"+intId+" > option").each(function() {
        				$(".optional_course_codes select option[value='" + this.value + "']").attr('disabled','true');
        			});

        			$(".optional_course_codes select option[value='" + option + "']").attr('disabled','true');
        		} else {
        			var intId = id.match(/\d+/);
        			$("#select"+intId).removeAttr('disabled');
        			$("#select"+intId+" > option").each(function() {

        				$(".optional_course_codes select option[value='" + this.value + "']").removeAttr('disabled');
        			});

        			$(".optional_course_codes select option[value='" + option + "']").removeAttr('disabled');
        		}
        	}
        });
    }
});

$(document).on('change', '#hsc_group', function (e) {
      var group = $(this).val();
      url = "{{route('student.hsc.hscGroupChange')}}";
      $.ajax({
        type:'POST',
        url:url,
        data:{group:group,course:0},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        async: false,
        success:function(response){

          $('.compulsory_course_codes').html(response);
        }
      });//end of ajax
      //ajax start
   
          //alert('cffhgf');
      $.ajax({
        type:'POST',
        url:url,
        data:{group:group,course:1},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        async: false,
        success:function(response){
          $('.selective_course_codes').html(response);
        }
      });//end of ajax
      //ajax start
      $.ajax({
        type:'POST',
        url:url,
        data:{group:group,course:2},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        async: false,
        success:function(response){
          $('.optional_course_codes').html(response);
        }
      });//end of ajax


      }); 
</script>