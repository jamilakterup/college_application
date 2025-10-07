{!! Form::open(['route'=> 'student.admission.hsc.step1', 'method'=> 'post', 'files'=> true, 'id' => 'adm-form-data', 'class'=> 'col-sm-4']) !!}
    <div class="tab-pane fade show active" id="steparrow-step1-info" role="tabpanel" aria-labelledby="steparrow-step1-info-tab">
        <div>
            <div class="mb-3">
                {!! Form::text('ssc_roll',null, ['class'=> 'form-control', 'placeholder'=> 'SSC Roll']) !!}
                <div class='invalid-feedback'></div>
            </div>

            <div class="mb-3">
                {!! Form::select('ssc_board',selective_boards(), null, ['class'=> 'form-control selectize']) !!}
                <div class='invalid-feedback'></div>
            </div>

            <div class="mb-3">
                {!! Form::select('ssc_passing_year',selective_multiple_passing_year(), null, ['class'=> 'form-control selectize']) !!}
                <div class='invalid-feedback'></div>
            </div>

            <div class="mb-3">
                {!! Form::text('quota_password',null, ['class'=> 'form-control', 'placeholder'=> 'Quota Password']) !!}
                <div class='invalid-feedback'></div>
            </div>

            {!! Form::hidden('step', 'step1') !!}

        </div>
        <div class="d-flex align-items-start gap-3 mt-4">
            <button type="submit" class="btn btn-success btn-label right ms-auto nexttab
            nexttab" data-nexttab="steparrow-description-info-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next</button>
        </div>
    </div>
{!!Form::close() !!}