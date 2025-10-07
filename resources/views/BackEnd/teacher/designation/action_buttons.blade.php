<div class="dropdown">
    <button type="button" class="btn btn-sm btn-outline btn-primary dropdown-toggle" id="action"
      data-toggle="dropdown" aria-expanded="false">Actions</button>
    <div class="dropdown-menu" aria-labelledby="action" role="menu">
        <button class="dropdown-item" data-href="{{ route('teacher.designation.edit', $id) }}" data-action='update' onclick="getAjaxModalData(this, 'Edit Designation Details', {{$id}})"><i class="fa fa-pencil text-info me-2"></i> Edit</button>

        <button class="dropdown-item" data-href="{{route('teacher.designation.destroy', $id)}}" onclick="deleteAjaxData(this, 'Delete Designation')"><i class="fa fa-trash text-danger me-2"></i> Delete</button>
    </div>
  </div>