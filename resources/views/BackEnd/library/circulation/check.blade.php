@extends('BackEnd.library.layouts.master')
@section('page-title', 'Circulation Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Issue-Return Book</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
				
			  	{{ Form::open(['route' => 'library.circulation.checkpost', 'method' => 'post', 'class'=> 'form-horizontal']) }}

		          	<div class="form-group row">
					    {{ Form::label('libraryuser_id', 'User Type', ['class' => 'col-md-2 form-control-label']) }}
					    <div class="col-md-10">
					        {{ Form::select('libraryuser_id', $libraryuser_lists, NULL, ['class' => 'form-control']) }}
					        {!!invalid_feedback('libraryuser_id')!!}
					    </div>
					</div>

					<div class="form-group row">
					    {{ Form::label('libmember_id', 'Member ID', ['class' => 'col-md-2 form-control-label']) }}
					    <div class="col-md-10">
					        {{ Form::text('libmember_id', NULL, ['class' => 'form-control', 'placeholder' => 'Enter library member id']) }}
					        {!!invalid_feedback('libmember_id')!!}
					    </div>
					</div>

					<div class="form-group row">
					    <div class="col-md-10 offset-md-2">
					      {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
					      <a href="{{ route('library.circulation.index') }}" class="btn btn-warning btn-outline">Back</a>
					    </div>
					</div>
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection