{!! Form::open(['route'=> 'teacher.subject-list.store', 'method'=> 'post', 'files' => true, 'onSubmit' => 'submitAjaxModalForm(this)']) !!}
    <div class="mb-3">
        <label for="name" class="form-label">Subject Name*</label>
        {!! Form::text('name', $list->name ?? null ,['class'=> 'form-control form-control-sm']) !!}
        <div class="invalid-feedback"></div>
    </div>
    
    <div class="d-flex justify-content-end" style="margin-top: 10px;">
        <button type="button" class="btn btn-danger font-weight-medium mr-2" data-dismiss="modal">
            Close
        </button>
        <button type="submit" class="btn btn-primary" data-button="save">Save Changes</button>
    </div>

{!! Form::close() !!}