@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Add New User')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">

	<div class="panel-body">
		<div class="row">
			<div class="col-md-8 offset-md-2">

				{{ Form::model($user, ['route' => ['admin.user.reset.post', $user->id], 'method' => 'put', 'class'=> 'form-horizontal']) }}

				<div class='form-group row'>
					{{ Form::label('password', 'Password', ['class' => 'col-md-2 form-control-label']) }}

					<div class='col-md-10'>
						{{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Enter user password']) }}
						{!!invalid_feedback('password')!!}
					</div>
				</div> <!-- end form-group -->

				<div class='form-group row'>
					{{ Form::label('password_confirmation', 'Password Confirmation', ['class' => 'col-md-2  form-control-label']) }}

					<div class='col-md-10'>
						{{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirm user password']) }}
						{!!invalid_feedback('password_confirmation')!!}
					</div>
				</div> <!-- end form-group -->

				@if(isset($user->id))
				{{ Form::hidden('id', $user->id) }}
				@endif	

				<div class="form-group row">
					<div class="col-md-10 offset-md-2">
						{!! Form::submit('Change', ['class'=> 'btn btn-primary']) !!}
						<a href="{{ route('admin.user.index') }}" class="btn btn-warning btn-outline">Back</a>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@endsection