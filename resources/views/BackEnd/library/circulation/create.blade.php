@extends('BackEnd.library.layouts.master')
@section('page-title', 'Circulation Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')
<div class="panel">
	<div class="panel-body">
		<div class="row">
			<div class="col-md-5">
				<div class="panel-heading">
		            <h4 class="panel-title text-center">Issue Book</h4>
		        </div>

			    {{ Form::open(['route' => 'library.circulation.store', 'method' => 'post']) }}

			    	@include('BackEnd.library.circulation.particles.form-issue-book')

			    {{ Form::close() }} 
			</div>

			<div class="col-md-7 offset-md-0">
				
				<div class="panel-heading">
		            <h4 class="panel-title text-center">Book Issue Information of ID No: {{ $libmember_id }}</h4>
		        </div>

				<table class='table table-bordered' id='return-book'>

					<tr>
						<th>Select</th>
						<th>Call No</th>
						<th>Accession No</th>
						<th>Issue Date</th>
						<th>Return Date</th>
						<th>Fine (tk)</th>
					</tr>	

					@if($libcirculations->count() > 0)

						{{ Form::open(['route' => 'library.circulation.returnbook', 'method' => 'post']) }}

							{{ Form::hidden('libmember_id', $libmember_id) }}

							<?php $total_fine = 0; ?>

							@foreach($libcirculations as $libcirculation)
								<tr>
									<td>{{ Form::checkbox($libcirculation->id, $libcirculation->id) }}</td>
									<td>{{ $libcirculation->maccession->material->call_no }}</td>
									<td>{{ $libcirculation->maccession->accession_no }}</td>
									<td>{{ $libcirculation->issue_date }}</td>
									<td>{{ $libcirculation->return_date }}</td>
									<td>
										<?php
											$today =  time();
											$return_date = new DateTime($libcirculation->return_date);
											$return_date = $return_date->getTimestamp();

											$days = ceil(($today - $return_date)/(24 * 3600));

											if($days > 0) :
												$libraryuser_id = $libcirculation->libmember->libraryuser->id;
												$overdue_amount = Circulation::whereLibraryuser_id($libraryuser_id)->pluck('overdue_amount');
												$fine = $days * $overdue_amount;
												$total_fine += $fine; 											
											else :
												$fine = '';
											endif;	
										?>

										@if($fine != '')
											<span class='c-red'>{{ $fine }}</span>
										@else
											-	
										@endif
									</td>
								</tr>
							@endforeach

							@if($total_fine > 0)
								<tr>
									<th colspan='5'></th>
									<th>{{ $total_fine }}</th>
								</tr>
							@endif

							<tr style='background: none; border: none'>
								<td colspan='6' style='border: 0px solid #fff'>
									{{ Form::submit('Return Book', ['class' => 'btn btn-primary']) }}															
								</td>
							</tr>

						{{ Form::close() }}

					@else 

						<tr>
							<td colspan='6'>No book issues with this Id</td>
						</tr>	

					@endif	
					
				</table>	        

		    
			</div>
			
		</div>
	</div>

	
</div>

@endsection