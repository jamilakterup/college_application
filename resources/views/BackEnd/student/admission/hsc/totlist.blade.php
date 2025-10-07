@php
use App\Libs\Study;
@endphp

@extends('BackEnd.student.layouts.master')
@section('page-title', 'Hsc Tot List Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <div class="panel-body">


        	<div class="col-md-12 d-flex justify-content-center search-filter">

			{!! Form::open(['method'=> 'post', 'class' => 'form-inline']) !!}

			  <div class="form-group">
			    {!! Form::select('groups', selective_multiple_study_group(), null, ['class'=>'form-control group', 'autocomplete'=> 'off', 'id' => 'groups']) !!}
						{!!invalid_feedback('groups')!!}
			  </div>

			  <div class="form-group">
			    {!! Form::select('session', selective_multiple_session(), null, ['class'=>'form-control session', 'autocomplete'=> 'off' , 'session' => 'session','id'=> 'session']) !!}
						{!!invalid_feedback('session')!!}
			  </div>

			  {!! Form::button('Generate Tot List', ['class' => 'btn btn-info', 'id'=> 'submit_tot_list']) !!}
			{!! Form::close() !!}

		</div>

        </div>

        <div id="tot_list_info">
	
	    </div>

	    <div id="tot_li">
	
	    </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush