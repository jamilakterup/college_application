<div class="dropdown">
  <button type="button" class="btn btn-sm btn-outline btn-primary dropdown-toggle" id="hscStdAction"
    data-toggle="dropdown" aria-expanded="false">Actions</button>
  <div class="dropdown-menu" aria-labelledby="hscStdAction" role="menu">
    @can('student.hsc.edit')
      <a class="dropdown-item edit_data" href="{{route('students.hsc.edit', $id)}}" role="menuitem" data-label="Student" data-id="{{$id}}"><i class="icon wb-edit" aria-hidden="true"></i> Edit</a>
    @endcan
      <a class="dropdown-item" href="{{route('students.hsc.print', $id)}}" role="menuitem" target="_blank"><i class="icon wb-eye" aria-hidden="true"></i> View</a>
      @can('student.hsc.delete')
        <a class="dropdown-item delete_data" data-id="{{$id}}" href="{{route('students.hsc.destroy', $id)}}" role="menuitem"><i class="icon wb-trash" aria-hidden="true"></i> Delete</a>
      @endcan
  </div>
</div>