@extends('BackEnd.library.layouts.master')
@section('page-title', 'Class Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Upload Material From CSV</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
				
			  	{{ Form::open(['route' => 'library.material.postupload', 'method' => 'post', 'class'=> 'form-horizontal','files' => true]) }}

				  	<div class="form-group row">
						<div class="col-md-2">
							{{ Form::label('isbn', 'ISBN/ISSN', ['class' => 'form-control-label']) }}
							<p class='margin-0'><a href="{{ URL::route('library.material.csv') }}">Download CSV Format</a></p>
						</div>
						<div class="col-md-10">
							{{ Form::file('material_csv', NULL, ['class' => 'form-control']) }}
							{!!invalid_feedback('material_csv')!!}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-10 offset-md-2">
						  {!! Form::submit('Upload', ['class'=> 'btn btn-primary']) !!}
						  <a href="{{ route('library.material.index') }}" class="btn btn-warning btn-outline">Back</a>
						</div>
					</div>
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection