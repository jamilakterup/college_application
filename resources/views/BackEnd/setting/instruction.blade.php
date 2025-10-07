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

    					{{ Form::open(['route' => 'settings.instruction.update', 'method' => 'post', 'files'=> true])}}
                        
                        <div class="form-group">
                            {{ Form::label('hsc_adm_instruction', 'HSC 1st Year Admission Instruction', ['class' => 'form-control-label  text-danger']) }}
                            {!! Form::textarea('hsc_adm_instruction', config('settings.hsc_adm_instruction'), ['class' => 'summernote']) !!}
                        </div>

                        <div class="form-group">
                            {{ Form::label('hons_adm_instruction', 'Honours 1st Year Admission Instruction', ['class' => 'form-control-label text-danger']) }}
                            {!! Form::textarea('hons_adm_instruction', config('settings.hons_adm_instruction'), ['class' => 'summernote']) !!}
                        </div>

                        <div class="form-group">
                            {{ Form::label('masters_adm_instruction', 'Masters 1st Year Admission Instruction', ['class' => 'form-control-label text-danger']) }}
                            {!! Form::textarea('masters_adm_instruction', config('settings.masters_adm_instruction'), ['class' => 'summernote']) !!}
                        </div>

                        <div class="form-group">
                            {{ Form::label('masters1st_adm_instruction', 'Masters Part-1 1st Year Admission Instruction', ['class' => 'form-control-label text-danger']) }}
                            {!! Form::textarea('masters1st_adm_instruction', config('settings.masters1st_adm_instruction'), ['class' => 'summernote']) !!}
                        </div>

                        <div class="form-group">
                            {{ Form::label('deg_adm_instruction', 'Degree 1st Year Admission Instruction', ['class' => 'form-control-label text-danger']) }}
                            {!! Form::textarea('deg_adm_instruction', config('settings.deg_adm_instruction'), ['class' => 'summernote']) !!}
                        </div>

                        <div class="form-group">
                            {{ Form::label('hsc_ff_instruction', 'HSC Formfillup Instruction', ['class' => 'form-control-label text-danger']) }}
                            {!! Form::textarea('hsc_ff_instruction', config('settings.hsc_ff_instruction'), ['class' => 'summernote']) !!}
                        </div>

                        <div class="form-group">
                            {{ Form::label('hons_ff_instruction', 'Honours Formfillup Instruction', ['class' => 'form-control-label text-danger']) }}
                            {!! Form::textarea('hons_ff_instruction', config('settings.hons_ff_instruction'), ['class' => 'summernote']) !!}
                        </div>

                        <div class="form-group">
                            {{ Form::label('deg_ff_instruction', 'Degree Formfillup Instruction', ['class' => 'form-control-label text-danger']) }}
                            {!! Form::textarea('deg_ff_instruction', config('settings.deg_ff_instruction'), ['class' => 'summernote']) !!}
                        </div>

                        <div class="form-group">
                            {{ Form::label('masters_ff_instruction', 'Masters Formfillup Instruction', ['class' => 'form-control-label text-danger']) }}
                            {!! Form::textarea('masters_ff_instruction', config('settings.masters_ff_instruction'), ['class' => 'summernote']) !!}
                        </div>

                        <div class="form-group">
                            {{ Form::label('hons_app_instruction', 'Honours Application Instruction', ['class' => 'form-control-label text-danger']) }}
                            {!! Form::textarea('hons_app_instruction', config('settings.hons_app_instruction'), ['class' => 'summernote']) !!}
                        </div>

                        <div class="form-group">
                            {{ Form::label('deg_app_instruction', 'Degree Application Instruction', ['class' => 'form-control-label text-danger']) }}
                            {!! Form::textarea('deg_app_instruction', config('settings.deg_app_instruction'), ['class' => 'summernote']) !!}
                        </div>

                        <div class="form-group">
                            {{ Form::label('masters_app_instruction', 'Masters Application Instruction', ['class' => 'form-control-label text-danger']) }}
                            {!! Form::textarea('masters_app_instruction', config('settings.masters_app_instruction'), ['class' => 'summernote']) !!}
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
<script src="{{ asset('global/vendor/summernote/summernote-lite.min.js') }}"></script>
	<script>
		$(document).ready(function() {
            $('.summernote').summernote({
                placeholder: 'গুরত্বপূর্ণ নির্দেশাবলীঃ',
                tabsize: 2,
                height: 170,
              });
		});
	</script>
@endpush