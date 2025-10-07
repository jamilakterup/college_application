@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'User Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('admin.user.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New User</a></div>
          <h3 class="panel-title">User Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              <tr>
				<th>Full Name</th>			
				<th>Username</th>
				<th>Email</th>				
				<th>Role</th>
				<th>Status</th>	
				<th>Action</th>	
				<th>Edit</th>
				<th>Delete</th>
				<th style='width: 85px;'>Reset Pass.</th>
				</tr>
            </thead>
            
            <tbody>
	            @foreach($users as $user)

					<tr class="text-center {{ Study::updatedRow('id', $user->id) }}">
						<td>{{ $user->full_name }}</td>
						<td>{{ $user->username }}</td>						
						<td>{{ $user->email }}</td>
						<td>
							@foreach ($user->roles as $role)
									{{ link_to_route('admin.role.show', $role->name, $role->id, ['class' => 'btn btn-link']) }}
                            @endforeach
						</td>
						<td>{{ Study::userStatus($user->status) }}</td>
						<td>
							@if($user->status == 0)
								{{ Form::open(['route' => ['admin.user.status', $user->id], 'method' => 'put', 'class' => 'inline']) }}
									{{ Form::hidden('status', 1) }}
									{{ Form::hidden('id', $user->id) }}
									{{ Form::submit('Active', ['class' => 'btn btn-success type-b']) }}
								{{ Form::close() }}
							@endif

							@if($user->status == 1)
								{{ Form::open(['route' => ['admin.user.status', $user->id], 'method' => 'put', 'class' => 'inline']) }}
									{{ Form::hidden('status', 0) }}
									{{ Form::hidden('id', $user->id) }}
									{{ Form::submit('Inactive', ['class' => 'btn btn-danger type-b']) }}
								{{ Form::close() }}
							@endif
						</td>			
						<td><a href="{{ URL::route('admin.user.edit', $user->id) }}" class='edt'><i class='fa fa-pencil'></i></a></td>	
						<td>
							{{ Form::open(['route' => ['admin.user.destroy', $user->id], 'method' => 'delete', 'class' => 'delete']) }}
								{{ Form::hidden('id', $user->id) }}
								<button type='submit' class='del btn btn-danger type-b'><i class='fa fa-trash'></i></button>
							{{ Form::close() }}
						</td>
						<td><a href="{{ URL::route('admin.user.reset', $user->id) }}" class='action-type-a'><i class='fa fa-key'></i></a></td>
					</tr>	

				@endforeach
            </tbody>
          </table>
          {{ $users->links() }}
        </div>
      </div>

@endsection

@push('scripts')
	<script>
		
	</script>
@endpush