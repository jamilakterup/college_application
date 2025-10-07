@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Site Settings')

@push('styles')
<link href="{{ asset('global/vendor/summernote/summernote-lite.min.css') }}" rel="stylesheet">
<style type="text/css">

</style>
@endpush

@section('content')
<div class="nav-tabs-horizontal nav-tabs-inverse" data-plugin="tabs">

    @include('BackEnd.setting.particles.subMenu')

    <div class="tab-content pt-15">
    	<div class="tab-pane active" role="tabpanel">
    		<div class="panel-body">
    			<div class="row">
    				<div class="col-md-12">

						@php
							$options = create_option_array('configurations', 'id', 'key_title', 'Key');
						@endphp

    					{{ Form::open(['route' => 'settings.config.update', 'method' => 'post', 'files'=> true, 'id'=> 'conf-form'])}}
                        
							<div class="form-group">
								{{ Form::label('key', 'Configuration Key', ['class' => 'form-control-label  text-danger']) }}
								{!! Form::select('key',$options, null, ['class' => 'form-control', 'id'=> 'key_title', 'required'=> true]) !!}
							</div>

							<div id="key_value"></div>

							<div class="form-group">
								{!! Form::submit('Change', ['class'=> 'btn btn-info']) !!}
							</div>

    					{!! Form::close() !!}
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
  </div>

@endsection

@push('scripts')
<script src="{{ asset('global/vendor/summernote/summernote-lite.min.js') }}"></script>
	<script>
		$(document).on('change', '#key_title', function(){
			$.ajax({
				type: "post",
				url: '{{url("settings/configuration/edit?key_id=")}}'+$(this).val(),
				dataType: "html",
				success: function (response , textStatus, xhr) {
					if(xhr.status === 200){
						$('#key_value').html(response);
						$(document).ready(function() {
							$('.summernote').summernote({
								placeholder: 'গুরত্বপূর্ণ নির্দেশাবলীঃ',
								tabsize: 2,
								height: 170,
							});
						});
					}
				},error: function (xhr, status, error) {
					trigger_ajax_swal_msg(xhr);
				}
			});
		});

		$(document).on('submit',$('#conf-form'), function(e){
			e.preventDefault();
			key_id = $('#key_title').val();
			if(!key_id){
				Swal.fire({
					icon: 'error',
					title: 'Oppps...',
					timer: 3000,
					html: `<b>Please Select Key first.</b>`,
					width: '25em'
				});
				return;
			}
			if($('#conf-form').length > 0){
				postForm = $('#conf-form');
				var url = postForm.attr('action');
				if(url != undefined){
					var formData = new FormData(postForm.get(0));
					$.ajax({
						data: formData,
						url: url,
						type: "POST",
						processData: false,
						contentType: false,
						beforeSend: function() {
							$.LoadingOverlay("show");
						},
						dataType: 'json',
						success: function (response , textStatus, xhr) {
							if(xhr.status === 200){
								$.LoadingOverlay("hide");
								trigger_ajax_swal_msg(xhr);
							}
						},
						error: function (xhr, status, error) {
							$.LoadingOverlay("hide");
							trigger_ajax_swal_msg(xhr);
						}
					});
				}
			}
		});

		
	</script>
@endpush