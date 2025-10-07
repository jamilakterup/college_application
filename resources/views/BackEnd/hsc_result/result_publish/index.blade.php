@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Result Publish Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('hsc_result.result_publish.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New</a></div>
          <h3 class="panel-title">Result Publish Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              	<tr>
					          <th>Id</th>	
                    <th>Session</th>		
                    <th>Level</th>
                    <th>Exam</th>
                    <th>Exam Year</th>
                    <th>Open</th>						
                    <th>Edit</th>
				        </tr>
            </thead>
            
            <tbody>
	        	    @foreach($all_rslt as $rslt)

                    <tr class="text-center {{ Ecm::updatedRow('id', $rslt->id) }}">
                        <td>{{ $rslt->id }}</td>
                        <td>{{ $rslt->session }}</td>					
                        <td>{{ $rslt->level }}</td>
                        <td>{{ $rslt->exam->name }}</td>
                        <td>{{ $rslt->exam_year }}</td>
                        @if($rslt->open==1)
                        <td>Open</td>
                        @else
                        <td>Close</td>	
                        @endif					
                        <td><a href="{{ URL::route('hsc_result.result_publish.edit', $rslt->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
                        
                    </tr>	

                @endforeach
            </tbody>
          </table>
          {{ $all_rslt->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush