@php
use App\Libs\Study;
@endphp

@extends('BackEnd.admin.layouts.master')
@section('page-title', 'Role Management')

@push('styles')
<style type="text/css">

</style>
@endpush

@section('content')

<div class="panel">
        <header class="panel-heading">
          <div class="panel-actions"><a href="{{ route('admin.role.create') }}" class="btn btn-sm btn-primary"><i class="fal fa-plus"></i> Add New Role</a></div>
          <h3 class="panel-title">Role Lists</h3>
        </header>
        <div class="panel-body">
          <table class="table table-hover defDTable w-full cell-border">
            <thead>
              <tr>
	                <th width="5%">Sl</th>
	                <th width="15%">Role Name</th>
	                <th width="55%">Permissions</th>
	                <th>Edit</th>
	                <th>Delete</th>
	            </tr>
            </thead>
            
            <tbody>
              @foreach ($roles as $role)
               <tr class="text-center {{ Study::updatedRow('id', $role->id) }}">
                    <td class="text-center">{{ $loop->index+1 }}</td>
                    <td class="text-center">{{ $role->name }}</td>
                    <td style="text-align: left;">
                    	@php
                    		$permission_parent_groups = [];
                    		foreach($role->permissions as $perm){
                    			$permission_parent_groups[] = $perm->parent_group_name;
                    		}
                    		$permission_parent_groups = array_unique($permission_parent_groups);
                    		$i = 1;
                    	@endphp

									   	<ul class="list-type-a">
					                @foreach ($permission_parent_groups as $parent_group)
								    			@php
							                $permission_groups = App\User::getpermissionGroups($parent_group);
							                $j = 1;
							            @endphp

									    		<li>
										    		@foreach ($permission_groups as $group)
										    			@php
										                	$permissions = App\User::getpermissionsByGroupName($group->name);
										    			@endphp
										    				
										    				@if ($j == 1)
												    				<span class="badge badge-success">{{$parent_group}}</span>
										    				@endif
										    				<span class="badge badge-info">{{ $group->name }}</span>
										    			@php  $j++; @endphp
									    				@php  $i++; @endphp
													@endforeach
									    		</li>
							    			@endforeach
									    </ul>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.role.edit', $role->id) }}" class='edt'><i class='fad fa-pencil'></i></a>
                    </td>
                    <td class="text-center">
                        {{ Form::open(['route' => ['admin.role.destroy', $role->id], 'method' => 'delete', 'class' => 'delete']) }}
							{{ Form::hidden('id', $role->id) }}
							<button type='submit' class='btn btn-sm btn-danger type-b'><i class='fad fa-trash'></i></button>
						{{ Form::close() }}
                    </td>
                </tr>
               @endforeach
            </tbody>
          </table>
        </div>
      </div>

@endsection

@push('scripts')
	<script>
	</script>
@endpush