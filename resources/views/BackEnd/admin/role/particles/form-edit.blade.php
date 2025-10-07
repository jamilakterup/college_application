<div class="form-group">
	{{ Form::label('name', 'Role Name') }}
	{{ Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Enter a Role Name']) }}
	{!!invalid_feedback('name')!!}
</div>

<div class="form-group">
	<div class="checkbox-custom checkbox-primary">
      <input type="checkbox" id="checkPermissionAll" value="1" {{ App\User::roleHasPermissions($role, $all_permissions) ? 'checked' : '' }}>
      <label for="checkPermissionAll">Select All</label>
    </div>

    <table class="table table-hover table-striped w-full cell-border" style="min-width: 900px;">
    	<thead>
    		<tr>
    			<th width="15%">Modules</th>
    			<th width="15%">Group Name</th>
    			<th>Permissions</th>
    		</tr>
    	</thead>
    	<tbody>
    		@php $i = 1; @endphp
    		@foreach ($permission_parent_groups as $parent_group)
    			@php
	                $permission_groups = App\User::getpermissionGroups($parent_group->parent_group_name);
	                $j = 1;
	            @endphp

	    		@foreach ($permission_groups as $group)
	    			@php
	                	$permissions = App\User::getpermissionsByGroupName($group->name);
	    			@endphp
	    			<tr>
	    				@if ($j == 1)
	    					<td rowspan ="{{count($permission_groups)}}" class="text-center">
			    				<p class="text-info">{{$parent_group->parent_group_name}}</p>
			    			</td>
	    				@endif
		    			<td>
		    				<input type="checkbox" class="" id="{{ $i }}Management" value="{{ $group->name }}" onclick="checkPermissionByGroup('role-{{ $i }}-management-checkbox', this)" {{ App\User::roleHasPermissions($role, $permissions) ? 'checked' : '' }}>
		                    <label class="form-check-label" for="checkPermission">{{ $group->name }}</label>
		    			</td>

		    			<td class="role-{{ $i }}-management-checkbox" style="padding: 0px 5px">

							@php
								$groupModule = [];
							@endphp
							@foreach ($permissions as $permission)
								@php
									$array = explode('.', $permission->name);
									$first = $array[0];
									array_shift($array);
									$last = implode('.',$array);
									$groupModule[$first][] = $last;
								@endphp
							@endforeach

							@foreach ($groupModule as $group => $modules)
								@php
									$countPermissions = 0;
								@endphp
								<table style="border: none !important;width:100%" class="child-table">
									<tbody>
										<tr>
												@foreach ($modules as $item)
													@php
														$permission_name = $group.'.'.$item;
													@endphp
													@foreach ($permissions as $permission)
														@if($permission->name == $permission_name)

															@php
																if($role->hasPermissionTo($permission->name)){
																	$countPermissions = $countPermissions+1;
																}
															@endphp
														@endif
													@endforeach
												@endforeach
											<td style="border: none; border-right:1px solid #ddd; border-left:none !important; width:190px;">
												<input type="checkbox" class="module" {{ $countPermissions == count($modules) ? 'checked' : '' }} value="{{ $group }}" onclick="checkPermissionByModule('role-{{ $i }}-management-checkbox', this)">
												{{$group}}
											</td>

											<td style="border: none;">

												@foreach ($modules as $item)
													@php
														$permission_name = $group.'.'.$item;
													@endphp
													@foreach ($permissions as $permission)
														@if($permission->name == $permission_name)

															<input type="checkbox" onclick="checkSinglePermission('role-{{ $i }}-management-checkbox', '{{ $i }}Management', {{ count($permissions) }})" name="permissions[]" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }} id="checkPermission{{ $permission->id }}" value="{{ $permission->name }}">
		                        							<label class="permission-label" for="checkPermission{{ $permission->id }}">{{$item }}</label>	
														@endif
													@endforeach
															

												@endforeach
											</td>

										</tr>
									</tbody>
								</table>										
							@endforeach

		                </td>
	    			</tr>
	    			@php  $j++; @endphp
    				@php  $i++; @endphp
				@endforeach
    		@endforeach
    	</tbody>
    </table>
</div>

<div class="form-group">
    {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
    <a href="{{ route('admin.role.index') }}" class="btn btn-warning btn-outline">Back</a>
</div>