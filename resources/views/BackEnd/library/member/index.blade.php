@extends('BackEnd.library.layouts.master')
@section('page-title', 'Member Management')

@push('styles')
<style type="text/css">
  .filter-form input,select {
    margin-bottom: 3px;
    margin-right: 3px;
  }
</style>
@endpush

@section('content')

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions">
            <a href="{{ route('library.member.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Material</a>
          </div>
          <h3 class="panel-title">Material List</h3>
        </header>
        <div class="panel-body">
          <div class="col-md-12 d-flex justify-content-center">

            {!! Form::open(['route'=> 'library.member.search', 'method'=> 'post', 'class' => 'form-inline filter-form']) !!}
              {{ Form::text('member_id', $member_id, ['class' => 'form-control', 'placeholder' => 'Enter member id']) }}

              {{ Form::text('full_name', $full_name, ['class' => 'form-control', 'placeholder' => 'Enter member name']) }}
      
              {{ Form::select('libraryuser_id', $libraryuser_lists, $libraryuser_id, ['class' => 'form-control']) }}
      
              {{ Form::submit('Search', ['class' => 'btn btn-default']) }}
            {!! Form::close() !!}
    
          </div>

          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              <tr>
                <th style='width: 7%'>ID</th>
                <th>Name</th>
                <th>User Type</th>
                <th>Contact No</th>
                <th>Edit</th>
                <th>Delete</th>
				      </tr>
            </thead>
            
            <tbody>
              @foreach($libmembers as $libmember)

                <tr class="text-center {{ App\Libs\Study::updatedRow('id', $libmember->id) }}">
                  <td>{{ $libmember->id }}</td>
                  <td>{{ $libmember->full_name }}</td>
                  <td>{{ $libmember->libraryuser->user_type }}</td>
                  <td>{{ $libmember->contact_no }}</td>
                  <td><a href="{{ URL::route('library.member.edit', $libmember->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>
                  <td>
                    {{ Form::open(['route' => ['library.member.destroy', $libmember->id], 'method' => 'delete', 'class' => 'delete']) }}
                      {{ Form::hidden('id', $libmember->id) }}
                      <button type='submit' class='del'><i class='fa fa-trash'></i></button>
                    {{ Form::close() }}
                  </td>
                </tr>

              @endforeach
            </tbody>
          </table>
          {{ $libmembers->appends(Request::except('page'))->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush