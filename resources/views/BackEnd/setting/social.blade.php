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
    						{{ Form::label('site_title', 'Site Title', ['class' => 'form-control-label']) }}
    						{{ Form::text('site_title', config('settings.site_title'), ['class' => 'form-control', 'placeholder' => 'Site Title']) }}
    					</div>


    					<div class="form-group">
    						{{ Form::label('default_email_address', 'Default Email Address', ['class' => 'form-control-label']) }}
    						{{ Form::text('default_email_address', config('settings.default_email_address'), ['class' => 'form-control', 'placeholder' => 'Default Email Address']) }}
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