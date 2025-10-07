@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Site Settings')

@push('styles')
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
    				<div class="col-md-8 offset-md-2">

    					{{ Form::open(['route' => 'settings.general.update', 'method' => 'post', 'files'=> true])}}
    					<div class="form-group">
    						{{ Form::label('college_name', 'College Name', ['class' => 'form-control-label']) }}
    						{{ Form::text('college_name', config('settings.college_name'), ['class' => 'form-control', 'placeholder' => 'College Name']) }}
    					</div>

    					<div class="form-group">
    						{{ Form::label('college_name_bn', 'College Name in (Bengali)', ['class' => 'form-control-label']) }}
    						{{ Form::text('college_name_bn', config('settings.college_name_bn'), ['class' => 'form-control', 'placeholder' => 'College Name in (Bengali)']) }}
    					</div>

    					<div class="form-group">
    						{{ Form::label('college_district', 'College District', ['class' => 'form-control-label']) }}
    						{{ Form::text('college_district', config('settings.college_district'), ['class' => 'form-control', 'placeholder' => 'College District']) }}
    					</div>

    					<div class="form-group">
    						{{ Form::label('college_district_bn', 'College District in (Bengali)', ['class' => 'form-control-label']) }}
    						{{ Form::text('college_district_bn', config('settings.college_district_bn'), ['class' => 'form-control', 'placeholder' => 'College District']) }}
    					</div>

    					<div class="form-group">
    						{{ Form::label('college_email_address', 'College Email Address', ['class' => 'form-control-label']) }}
    						{{ Form::text('college_email_address', config('settings.college_email_address'), ['class' => 'form-control', 'placeholder' => 'College Email Address']) }}
    					</div>

    					<div class="form-group">
    						{{ Form::label('college_web_address', 'College Web Address', ['class' => 'form-control-label']) }}
    						{{ Form::text('college_web_address', config('settings.college_web_address'), ['class' => 'form-control', 'placeholder' => 'College Web Address']) }}
    					</div>

    					<div class="form-group">
    						{{ Form::label('college_eiin', 'College EIIN', ['class' => 'form-control-label']) }}
    						{{ Form::text('college_eiin', config('settings.college_eiin'), ['class' => 'form-control', 'placeholder' => 'College EIIN']) }}
    					</div>

    					<div class="form-group">
    						{{ Form::label('college_biller_id', 'College Biller ID', ['class' => 'form-control-label']) }}
    						{{ Form::text('college_biller_id', config('settings.college_biller_id'), ['class' => 'form-control', 'placeholder' => 'College Biller ID']) }}
    					</div>

    					<div class="form-group">
    						{{ Form::label('site_title', 'Site Title', ['class' => 'form-control-label']) }}
    						{{ Form::text('site_title', config('settings.site_title'), ['class' => 'form-control', 'placeholder' => 'Site Title']) }}
    					</div>


    					<div class="form-group">
    						{{ Form::label('default_email_address', 'Default Email Address', ['class' => 'form-control-label']) }}
    						{{ Form::text('default_email_address', config('settings.default_email_address'), ['class' => 'form-control', 'placeholder' => 'Default Email Address']) }}
    					</div>

    					<div class="form-group">
    						{{ Form::label('site_logo', 'Site Logo', ['class' => 'form-control-label']) }}
    						<div class="img-thumbnail mb-1" id="site_logo_image_pre_area" style="{{config('settings.site_logo') ? '':'display: none;'}} width: 80px;">
	                          <img style="height: 100%; width: 100%;" src="{{asset('upload/sites/'.config('settings.site_logo'))}}" id="site_logo_image_pre" alt="Not Set Yet">
	                        </div>
    						{!! Form::file('site_logo', ['class'=> 'form-control image_data', 'data-type' =>'site_logo','placeholder'=> 'Site Logo']) !!}
    					</div>

    					<div class="form-group">
    						{{ Form::label('site_favicon', 'Site Favicon', ['class' => 'form-control-label']) }}
    						<div class="img-thumbnail mb-1" id="site_favicon_image_pre_area" style="{{config('settings.site_favicon') ? '':'display: none;'}} width: 80px;">
	                          <img style="height: 100%; width: 100%;" src="{{asset('upload/sites/'.config('settings.site_favicon'))}}" id="site_favicon_image_pre" alt="Not Set Yet">
	                        </div>
    						{!! Form::file('site_favicon', ['class'=> 'form-control image_data', 'data-type' =>'site_favicon','placeholder'=> 'Site Logo']) !!}
    					</div>

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
	<script>
		$(document).ready(function() {
		});
	</script>
@endpush