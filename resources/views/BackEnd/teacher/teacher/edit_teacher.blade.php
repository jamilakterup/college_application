@extends('BackEnd.teacher.layouts.master')
@section('page-title', 'Teacher Edit')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="panel">
	<header class="panel-heading">
	  <h3 class="panel-title">Edit Teacher</h3>
	</header>

	<div class="panel-body">
		<div class="row">
		  	<div class="col-md-12">

                <ul class="nav nav-tabs" id="teacherDetailTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{session('tab') == ''? 'active' : (session('tab') == 1 ? 'active': '')}}" id="personal-tab" data-toggle="tab" data-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">Personal</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{session('tab') == 2 ? 'active':''}}" id="education-tab" data-toggle="tab" data-target="#education" type="button" role="tab" aria-controls="education" aria-selected="false">Education</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{session('tab') == 3 ? 'active':''}}" id="employment-tab" data-toggle="tab" data-target="#employment" type="button" role="tab" aria-controls="employment" aria-selected="false">Employment</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{session('tab') == 4 ? 'active':''}}" id="appointment-tab" data-toggle="tab" data-target="#appointment" type="button" role="tab" aria-controls="appointment" aria-selected="false">Appointment</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{session('tab') == 5 ? 'active':''}}" id="career-tab" data-toggle="tab" data-target="#career" type="button" role="tab" aria-controls="career" aria-selected="false">Career</button>
                </li>
                </ul>
            
                <div class="tab-content" id="teacherDetailTabContent" style="margin-top: 1.5rem;">
                    <div class="tab-pane fade {{session('tab') == ''? 'show active' : (session('tab') == 1 ? 'show active': '')}}" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                        @includeIf('BackEnd.teacher.teacher.edit_particles.personal')
                    </div>
                
                    <div class="tab-pane fade {{session('tab') == 2 ? 'show active':''}}" id="education" role="tabpanel" aria-labelledby="education-tab">
                        @includeIf('BackEnd.teacher.teacher.edit_particles.education')
                    </div>
                
                    <div class="tab-pane fade {{session('tab') == 3 ? 'show active':''}}" id="employment" role="tabpanel" aria-labelledby="employment-tab">
                        @includeIf('BackEnd.teacher.teacher.edit_particles.employment')
                    </div>
                
                    <div class="tab-pane fade {{session('tab') == 4 ? 'show active':''}}" id="appointment" role="tabpanel" aria-labelledby="appointment-tab">
                        @includeIf('BackEnd.teacher.teacher.edit_particles.appointment')
                    </div>
                
                    <div class="tab-pane fade {{session('tab') == 5 ? 'show active':''}}" id="career" role="tabpanel" aria-labelledby="career-tab">
                        @includeIf('BackEnd.teacher.teacher.edit_particles.career')
                    </div>
            
                </div>
	        </div>
		</div>
	</div>
</div>

@endsection