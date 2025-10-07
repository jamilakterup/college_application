@extends('BackEnd.hsc_result.layouts.master')
@section('page-title', 'Faculty Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="submenu-item header-menu">
	{{ link_to_route('hsc_result.process.create', 'Result Process', NULL, ['class' => 'btn btn-info']) }}
    <a href="{{ URL::route('hsc_result.process.top-ten.index') }}" class='btn btn-success'><i class="fa fa-trophy"></i> Get Top Records</a>
</div>

<div class="panel">
        <header class="panel-heading">
          <h3 class="panel-title">Result Processing</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
                <tr>
                    <th>Id</th>			
                    <th>Department</th>
                    <th>Session</th>
                    <th>Exam</th>
                    <th>Tabulation</th>
                    <th>Download Merit List</th>
                    <th width="12%">Actions</th>
                </tr>
            </thead>
            
            <tbody>
	        	@foreach($processed_result as $result)
                    <tr class="text-center {{ Ecm::updatedRow('id', $result->id) }}">
                        <td>{{ $result->id }}</td>
                        <td>{{$result->group->name}}</td>					
                        <td>{{$result->session}}</td>
                        <td>{{$result->exam->name}}</td>
                        <td>
                            <a class="mr-2" href="{{ URL::route('hsc_result.process.tabulation-pdf', array($result->id, $result->exam->id )) }}" target="_blank" class='edt'><i class='fa fa-download' style="font-size: 16px;"></i></a>

                            <a href="{{ URL::route('hsc_result.process.tabulation-excel', array($result->id, $result->exam->id )) }}" target="_blank" class='edt'><i class="fa fa-file-excel" style="font-size: 16px;"></i></a>
                        </td>	
                        <td>
                            <a class="mr-2" href="{{ URL::route('hsc_result.process.merit-pdf', $result->id ) }}" target="_blank" class='edt'><i class='fa fa-download' style="font-size: 16px;"></i></a>

                            <a href="{{ URL::route('hsc_result.process.merit-excel', $result->id ) }}" target="_blank" class='edt'><i class='fa fa-file-excel' style="font-size: 16px;"></i></a>
                        </td>

                        <td class="d-flex justify-content-between">
                            
                            <a class="btn btn-success btn-sm" href="{{route('hsc_result.process.indivisual', $result->id)}}">Indivisual</a>

                            {{ Form::open(['route' => ['hsc_result.process.destroy', $result->id], 'method' => 'delete', 'class' => 'delete']) }}
                                {{ Form::hidden('id', $result->id) }}
                                <button type='submit' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></button>
                            {{ Form::close() }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
          </table>
          {{ $processed_result->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush