@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'ID Card Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	<div class="panel-body">

		<div class="d-flex justify-content-center">
			{{ Form::open(['route' => 'students.idcard.id_card_generate', 'method' => 'post', 'class' => 'form-inline form-type-a filter-form', 'target'=> '__blank']) }}
				{!! Form::text('student_id', null, ['class'=> 'small_form_element form-control form-control-sm', 'placeholder'=> 'Student ID' , 'autofocus'=>'YES']) !!}

				{!! Form::select('category', [''=>'Category *', 'hsc'=> 'HSC', 'degree'=>'Degree', 'honours'=> 'Honours','masters'=> 'Masters'], null, ['class'=> 'form-control form-control-sm small_form_element', 'id'=> 'category']) !!}
						
				<span id="category_details"></span>				
				
			{{ Form::submit('Generate', ['class' => 'btn btn-default btn-sm']) }}
			{{ Form::close() }}
		</div>
		<?php $downlink = Session::get('downlink'); if($downlink){ ?><div>{!!$downlink!!} </div> <?php }?>
	</div>
</div>

@endsection

@push('scripts')
	<script type="text/javascript">
		$("doucment").ready(function(){			
					
		$("#category").change(function(){
		var category=$("#category").val();
		var token = '{{csrf_token()}}';

		$.ajax({
		    type:'POST',
		    url:'idcard/category_details',
		    data:{category: category,  _token:token},
		    success:function(response){          
		        $("#category_details").html(response);
				}
		        
		      });
			});

				  
		});

</script>
@endpush