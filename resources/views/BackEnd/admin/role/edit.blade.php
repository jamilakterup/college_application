@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Role Management')

@push('styles')
<style type="text/css">
/*.form-check-label,.permission-label {
    text-transform: capitalize;
}*/

.child-table tr{
    background-color: transparent !important;
    border-bottom: 1px solid #E4EAEC;
    width: 100%;
}
</style>
@endpush

@section('content')

<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit Role</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">
			  	{{ Form::model($role, ['route' => ['admin.role.update', $role->id], 'method' => 'put', 'class'=> 'form-horizontal']) }}

		          	@include('BackEnd.admin.role.particles.form-edit')
		          	
		        {!! Form::close() !!}
	        </div>
		</div>
	</div>
</div>

@endsection

@push('scripts')
	@include('BackEnd.admin.role.particles.script')
@endpush