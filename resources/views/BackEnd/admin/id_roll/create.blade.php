@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'ID Roll Management')

@push('styles')
<style type="text/css">
	.borderless td, .borderless th {
	    border: none;
	}
	.dig-form {
	  height: 80vh;
	  display: block;
	  overflow-y: auto;
	}
	.cus_label{
		margin-top: .28575rem!important;
	}
</style>
@endpush

@section('content')

<div class="panel">
	<header class="panel-heading d-flex justify-content-between">
		<h3 class="panel-title">ID Roll</h3>
		<div class="panel-actions"><a href="{{ route('admin.id_roll.new') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New</a></div>
	</header>
	<div class="panel-body">
		<div class="col-md-8 offset-md-2">

			{{ Form::open(['route' => 'admin.id_roll.create', 'method' => 'post', 'class' => 'form-horizontal']) }}
				<div class="form-group row">
					<label class="col-md-3 form-control-label" for="inputSizingSmall">Session</label>
					<div class="col-md-7">
						{!! Form::select('session', selective_multiple_session(), $session, ['class'=>'form-control form-control-sm session', 'autocomplete'=> 'off', 'data-plugin' => 'select2']) !!}
						{!!invalid_feedback('session')!!}
					</div>
					<div class="col-md-2">
						{{ Form::submit('Search',['class' => 'btn btn-default', 'name' => 'search']) }}
					</div>
				</div>
			{!! Form::close() !!}

		</div>

		@if (count($id_rolls) < 1 && $session != 0)
			<h4 class="text-center">Configuration for session: <strong>{{$session}}</strong></h4>
			<div class="col-md-8 offset-md-2">
				
				{!! Form::open(['route' => 'admin.id_roll.create', 'method' => 'post']) !!}
				<table class="table borderless text-center overflow-auto">
					<tbody class="dig-form">
						@foreach ($departments as $dep)
							<tr>
								<td>
									<label class=" form-control-label cus_label" for="dept_name"><strong>{{$dep->dept_name}}</strong></label>
								</td>

								{!! Form::hidden('dept_name[]', $dep->dept_name, []) !!}

								<td>
									{!! Form::text('start_digit[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Starting Roll']) !!}
								</td>
								<td>
									{!! Form::text('end_digit[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Ending Roll']) !!}
								</td>
							</tr>
						@endforeach

						{{-- Starting HSC Group --}}
						<tr>
							<td><label class=" form-control-label cus_label" for="dept_name"><strong>HSC Humanities</strong></label>
							</td>
							{!! Form::hidden('hsc_group[]', 'hsc_Humanities', []) !!}
							<td>{!! Form::text('start_digit_hsc[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Starting Roll']) !!}</td>
							<td>{!! Form::text('end_digit_hsc[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Ending Roll']) !!}</td>
						</tr>

						<tr>
							<td><label class=" form-control-label cus_label" for="dept_name"><strong>HSC Business Studies</strong></label>
							</td>
							{!! Form::hidden('hsc_group[]', 'hsc_Business Studies', []) !!}
							<td>{!! Form::text('start_digit_hsc[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Starting Roll']) !!}</td>
							<td>{!! Form::text('end_digit_hsc[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Ending Roll']) !!}</td>
						</tr>

						<tr>
							<td><label class=" form-control-label cus_label" for="dept_name"><strong>HSC Science</strong></label>
							</td>
							{!! Form::hidden('hsc_group[]', 'hsc_Science', []) !!}
							<td>{!! Form::text('start_digit_hsc[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Starting Roll']) !!}</td>
							<td>{!! Form::text('end_digit_hsc[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Ending Roll']) !!}</td>
						</tr>

						{{-- Starting Degree Group --}}
						<tr>
							<td><label class=" form-control-label cus_label" for="dept_name"><strong>Degree B.A</strong></label>
							</td>
							{!! Form::hidden('degree_group[]', 'degree_B.A', []) !!}
							<td>{!! Form::text('start_digit_degree[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Starting Roll']) !!}</td>
							<td>{!! Form::text('end_digit_degree[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Ending Roll']) !!}</td>
						</tr>

						<tr>
							<td><label class=" form-control-label cus_label" for="dept_name"><strong>Degree B.S.S</strong></label>
							</td>
							{!! Form::hidden('degree_group[]', 'degree_B.S.S', []) !!}
							<td>{!! Form::text('start_digit_degree[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Starting Roll']) !!}</td>
							<td>{!! Form::text('end_digit_degree[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Ending Roll']) !!}</td>
						</tr>

						<tr>
							<td><label class=" form-control-label cus_label" for="dept_name"><strong>Degree B.B.S</strong></label>
							</td>
							{!! Form::hidden('degree_group[]', 'degree_B.B.S', []) !!}
							<td>{!! Form::text('start_digit_degree[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Starting Roll']) !!}</td>
							<td>{!! Form::text('end_digit_degree[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Ending Roll']) !!}</td>
						</tr>

						<tr>
							<td><label class=" form-control-label cus_label" for="dept_name"><strong>Degree B.S.C</strong></label>
							</td>
							{!! Form::hidden('degree_group[]', 'degree_B.S.C', []) !!}
							<td>{!! Form::text('start_digit_degree[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Starting Roll']) !!}</td>
							<td>{!! Form::text('end_digit_degree[]', null, ['class'=> 'form-control form-control-sm', 'placeholder'=> 'Ending Roll']) !!}</td>
						</tr>

						{!! Form::hidden('session', $session, []) !!}

					</tbody>
				</table>
				{!! Form::submit('Submit', ['class'=> 'btn btn-primary', 'name'=> 'submit']) !!}
				{!! Form::close() !!}
			</div>
		@else

		@if ($session != 0)
			<h4 class="text-center">Roll no configuration for session: <strong>{{$session}}</strong></h4>
			<table class="table table-hover dataTable table-striped w-full cell-border">
	            <thead>
	              <tr>
	                <th>Session</th>
					<th>Department/Group</th>
					<th>Starting Roll</th>
					<th>Ending Roll</th>
					<th>Last Digit Used</th>
					<th>Action</th>
	              </tr>
	            </thead>
	            
	            <tbody>
		            @foreach($id_rolls as $roll)
						<tr class="text-center {{ Study::updatedRow('id', $roll->id) }}">
							<td>{{$roll->session}}</td>
							<td>{{$roll->dept_name}}</td>
							<td>{{$roll->start_digit}}</td>
							<td>{{$roll->end_digit}}</td>
							<td>{{$roll->last_digit_used}}</td>
							<td>
								<a href="{{ route('admin.id_roll.edit', $roll->id) }}" class='edt'><i class='fa fa-pencil'></i></a>
							</td>
						</tr>
					@endforeach
	            </tbody>
	          </table>
		@endif
		@endif
	</div>
</div>

@endsection

@push('scripts')
	<script>
		var table = $('.table').dataTable({
				"scrollX": true,
				"scrollX": "100%",
	            "autoWidth": false,
	            "scrollY": '60vh',
	            "searching" : true,
	            "lengthChange": false,
	            "bSort": false,
	            "responsive": true,
	            "paging": false
			});
	</script>
@endpush