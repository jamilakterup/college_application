@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Sticker Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Create Sticker</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::open(['route' => 'hsc_result.sticker.store', 'method' => 'post', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.hsc_result.sticker.particles.form')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">			
	$(document).ready(function() {
		$(document.body).on('change','.year', function() {	
									 
			$('.loaderImage').show();            
			 var group = $(this).val();
			 var exam = $('#exam').empty();
			 var subject = $('#subject').empty();
			 
			if(group == '') 
			{
				$('.loaderImage').hide();
				$('<option/>', {
					value:'',
					text:'Select Exam'
				}).appendTo(exam);

				 $('<option/>', {
									value:'',
									text:'Select Subject'
							}).appendTo(subject);
			}
			   
			$.get('{{URL::to("hsc_result/mark_input/load-exam")}}/'+$(this).val(), function(response){
				
					if(response.success)
					{  

						$('.loaderImage').hide();
							$('<option/>', {
									value:'',
									text:'Select Exam'
							}).appendTo(exam);

							$('<option/>', {
									value:'',
									text:'Select Subject'
							}).appendTo(subject);

							$.each(response.exam_arr, function(id, name){ 
								$('<option/>', {
									value:id,
									text:name
								}).appendTo(exam);
							});
					}
				}, 'json'); 
		});

		$(document.body).on('change','.group', function() {	
									 
			$('.loaderImage').show();            
			 var group = $(this).val();
			 var subject = $('#subject').empty();
			 var year = $('.year').val();

			
			if(group == '') 
			{
				$('.loaderImage').hide();
				$('<option/>', {
					value:'',
					text:'Select Subject'
				}).appendTo(subject);
			}
			   
			$.get('{{URL::to("hsc_result/mark_input/load-subject")}}/'+year+'/'+$(this).val(), function(response){
				
					if(response.success)
					{  

						$('.loaderImage').hide();
							$('<option/>', {
									value:'',
									text:'Select Subject'
							}).appendTo(subject);

							$.each(response.sub_arr, function(id, name){ 
								$('<option/>', {
									value:id,
									text:name
								}).appendTo(subject);
							});
					}
				}, 'json'); 
		});
		
	});
</script>
@endpush