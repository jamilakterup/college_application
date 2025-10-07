@php
$level = [];
$faculty = [];
$faculty_js_id = '';

switch ($category) {
	case "hsc":
		$level = selective_multiple_hsc_level();
		$faculty = selective_multiple_study_group();
		break;
	case "degree":
		$level = selective_multiple_degree_level();
		$faculty = selective_degree_subjects();
		break;
	case "honours":
		$level = selective_multiple_honours_level();
		$faculty = selective_faculties();
		$faculty_js_id = 'faculty';
		break;
	case "masters":
		$level = selective_multiple_masters_level();
		$faculty = selective_faculties();
		$faculty_js_id = 'faculty';
		break;
	default:
		
	}
@endphp

@if($category!='')
	{!! Form::select('faculty', $faculty, null, ['class'=> 'small_form_element form-control form-control-sm', 'id'=> $faculty_js_id]) !!}
	<span id="dept"></span>
	{!! Form::select('level', $level, null, ['class'=> 'small_form_element form-control form-control-sm']) !!}
	{!! Form::select('session', selective_multiple_session(), null, ['class'=> 'small_form_element form-control form-control-sm', 'id'=> 'session'])!!}
	{!! Form::select('type', ['1' => 'Front', '2'=> 'Back'], null, ['class'=> 'small_form_element form-control form-control-sm', 'id'=> 'type'])!!}
@endif


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