{!! Form::open(['route'=> 'teacher.designation.store', 'method'=> 'post', 'files' => true, 'onSubmit' => 'submitAjaxModalForm(this)']) !!}
    <div class="mb-3">
        <label for="name" class="form-label">Name*</label>
        {!! Form::text('name', $list->name ?? null ,['class'=> 'form-control form-control-sm']) !!}
    </div>

    <div class="mb-3">
        <label for="type" class="form-label">Type*</label>
        {!! Form::select('type', App\Models\Designation::groupBy('type')->pluck('type', 'type')->toArray() ,$list->type ?? null ,['class'=> 'form-control form-control-sm', 'placeholder'=> '--Select Type--']) !!}
    </div>

    <div class="d-flex justify-content-end" style="margin-top: 10px;">
        <button type="button" class="btn btn-danger font-weight-medium mr-2" data-dismiss="modal">
            Close
        </button>
        <button type="submit" class="btn btn-primary" data-button="save">Save Changes</button>
    </div>

{!! Form::close() !!}