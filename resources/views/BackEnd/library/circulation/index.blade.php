@extends('BackEnd.library.layouts.master')
@section('page-title', 'Circulation Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions">
            <a href="{{ route('library.circulation.check') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Circulation</a>
          </div>
          <h3 class="panel-title">Circulation List</h3>
        </header>
        <div class="panel-body">
          <div class="col-md-12 d-flex justify-content-center">

            {!! Form::open(['route'=> 'library.circulation.search', 'method'=> 'post', 'class' => 'form-inline filter-form']) !!}
              {{ Form::select('status', $book_status_lists, NULL, ['class' => 'form-control m-left-0']) }}

              {{ Form::text('libmember_id', NULL, ['class' => 'form-control', 'placeholder' => 'Enter member id no']) }}

              {{ Form::text('accession_no', NULL, ['class' => 'form-control', 'placeholder' => 'Enter accession no']) }}

              {{ Form::text('call_no', NULL, ['class' => 'form-control', 'placeholder'  => 'Enter call no']) }}
      
              {{ Form::submit('Search', ['class' => 'btn btn-default']) }}
            {!! Form::close() !!}
    
          </div>

          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              <tr>
                <th>Member Id</th>
                <th>Member Type</th>
                <th>Accession No</th>
                <th>Call No</th>
                <th>ISBN</th>
                <th>Title</th>
                <th>Issue Date</th>
                <th>Return Date</th>
                <th>Status</th>
                <th>Edit</th>
                <th>Delete</th>
				      </tr>
            </thead>
            
            <tbody>
              @foreach($libcirculations as $libcirculation)

                <tr class="{{ App\Models\Study::updatedRow('id', $libcirculation->id) }}">
                  <td>{{ $libcirculation->libmember_id }}</td>
                  <td>{{ $libcirculation->libmember->libraryuser->user_type }}</td>
                  <td>{{ $libcirculation->maccession->accession_no }}</td>
                  <td>{{ $libcirculation->maccession->material->call_no }}</td>
                  <td>{{ $libcirculation->maccession->material->isbn }}</td>
                  <td>{{ $libcirculation->maccession->material->title }}</td>
                  <td>{{ $libcirculation->issue_date }}</td>
                  <td>{{ $libcirculation->return_date }}</td>
                  <td>
                    @if($libcirculation->status == 1)
                      Issued
                    @endif

                    @if($libcirculation->status == 2)
                      Returned
                    @endif            
                  </td>
                  <td><a href="{{ URL::route('library.circulation.edit', $libcirculation->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>
                  <td>
                    {{ Form::open(['route' => ['library.circulation.destroy', $libcirculation->id], 'method' => 'delete', 'class' => 'delete']) }}
                      {{ Form::hidden('id', $libcirculation->id) }}
                      <button type='submit' class='del'><i class='fa fa-trash'></i></button>
                    {{ Form::close() }}
                  </td>
                </tr>

              @endforeach
            </tbody>
          </table>
          {{ $libcirculations->appends(Request::except('page'))->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush