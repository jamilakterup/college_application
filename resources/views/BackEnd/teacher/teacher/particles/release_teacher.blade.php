{!! Form::open(['route'=> 'teacher.releaseteacher.store', 'method'=> 'post', 'files' => true, 'onSubmit' => 'submitAjaxModalForm(this)']) !!}
<table class="table"> 
    <tr>
        <td>
            Reference No:
        </td>
        <td>
            {!! Form::text('release_ref', $teacher->release_reference_no, ['class'=> 'form-control', 'placeholder'=> 'Release Reference No', 'id'=> 'release_ref']) !!}
        </td>
    </tr>
    
    <tr>
        <td>Outgoing College</td>	
        <td>
            {!! Form::text('outgoing_college', $teacher->outgoing_college, ['class'=> 'form-control', 'placeholder'=> 'Outgoing College', 'id'=> 'outgoing_college']) !!}
        </td>
    </tr>		   	   
    
    <tr>
        <td>Date &amp; Time:</td>
        <td>
            {!! Form::hidden('teacher_id', $relinfo['id']) !!}
            {!! Form::text('release_date', $teacher->release_date, ['class'=>'form-control datepickr', 'placeholder'=> 'yyyy-mm-dddd']) !!}
            {!! Form::text('release_time', $teacher->release_time, ['class'=>'form-control', 'placeholder'=> 'hh:mm:ss (ex- 23:17:13)']) !!}
        </td>
    </tr>	
</table>

<div class="d-flex justify-content-end mt-3">
    <button type="button" class="btn btn-light-danger text-danger font-medium mr-2" data-dismiss="modal">
        Close
    </button>

    <button type="submit" class="btn btn-primary" data-button='save'>Relase</button>
</div>

{!! Form::close() !!}