@php
    $result = $teacher->teacherEducation;

    $id = $teacher->id;

    $regular=$result->regular;
    $regular_date=$result->regular_date;
    $gazette_date=$result->gazette_date;
    $status=$result->status;
    $permanent_date=$result->permanent_date;
    $paper_pass=$result->paper_pass;
    $finalpass_date=$result->finalpass_date;
    $award_date=$result->award_date;
    $prof_certificate=$result->prof_certificate;
    $prof_institute=$result->prof_institute;
    $prof_location=$result->prof_location ;
    $prof_from=$result->prof_from ;
    $prof_to=$result->prof_to;
@endphp

@push('styles')
    <style>
        .card-dynamic{
            border: 1px solid #ddd; 
            border-radius:3px; 
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
        }
    </style>
@endpush

{{ Form::open(['route' => ['teacher.editTeachereducationinput', ['id'=> $id]], 'method' => 'post', 'class'=> 'form-horizontal', 'files'=> true]) }}

<input type="hidden" id="education_edit" name="education_edit" value="true">

<div class="input_form">
    
    <input type="hidden" disabled="yes" id="teacher_id2" value="<?php echo $id; ?>" /><br />
    
    <h3>
        Academic Qualification 
        <button type="button" onclick="addQualificationField()" class="btn btn-primary"><i class="fa fa-plus fa 2x"></i></button>
    </h3>

    <div class="row" id="qualificationContainer">
        @foreach ($qualifications as $i => $qualification)
        <div class="col-md-4 qualificationData">
            <div class="card card-dynamic">
                <div class="card-header d-flex justify-content-end p-0">
                    <button class="btn btn-link btn-sm remove-qualification" type="button" data-toggle="tooltip" data-placement="top" title="Remove">
                        <i class="fa fa-times fa-2x text-danger"></i>
                    </button>
                </div>
                <div class="card-body">
                        <table class="table" id="qualificationTable-{{$i}}">
                            <tr>
                                <td><label class="form-label"> Level of Education </label></td>
                                <td>
                                    {!! Form::select('edu_level[]', educationLevels(),$qualification['edu_level'], ['class'=> 'form-control', 'required'=> true]) !!}
                                </td>
                            </tr>
                
                            <tr>
                                <td><label class="form-label">Exam/Degree Title </label></td>
                                <td>
                                    <input type="text" name="exam_title[]" class="form-control exam_title2" value="{{$qualification['exam_title']}}"/> 
                                </td>
                            </tr>
                
                            <tr>
                                <td><label class="form-label">Concentration/Major/Group </label></td>
                                <td>
                                    <select class="form-control group2" name="group[]">
                                        <option value="" selected>Select</option>
                                        <?php
                                        $results=DB::select('select distinct(sub_name) from uni_subject order by sub_name asc');
                                        
                                        foreach($results as $result)
                                        {   
                                            if($result->sub_name==$qualification['group'])
                                            echo "<option value='{$result->sub_name}' selected='yes'>$result->sub_name</option>";
                                            else
                                            echo "<option value='{$result->sub_name}' >$result->sub_name</option>";
                                            
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                
                            <tr>
                                <td><label class="form-label">Institute Name/Board </label></td>
                                <td>
                                    <select class="form-control" name="institute_name[]"  >
                                        <option value="" selected>Select</option>
                                        <?php
                                        
                                        $results=DB::select('select distinct(name) from university_list order by name asc');
                                        foreach($results as $result)
                                        {
                                            if($result->name==$qualification['institute_name'])
                                            echo "<option value='{$result->name}' selected='yes'>$result->name</option>";
                                            else
                                            echo "<option value='{$result->name}'>$result->name</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                
                            <tr>
                                <td><label class="form-label">Result</label></td>
                                <td>
                                    {!! Form::select('result[]', educationResults(),$qualification['result'], ['class'=> 'form-control']) !!}
                                </td>
                            </tr>
                
                            <tr>
                                <td><label class="form-label">Marks(%)/CGPA/GPA </label></td>
                                <td>
                                    <input type="text" class="form-control marks" name="marks[]" value="{{$qualification['marks']}}"/>
                                </td>
                            </tr>
                
                            <tr>
                                <td><label class="form-label">Year of Passing</label></td>
                                <td>
                                    {!! Form::select('passing_year[]', year_generate(), $qualification['passing_year'], ['class'=> 'form-control']) !!}
                                </td>
                            </tr>
                
                            <tr>
                                <td><label class="form-label">Duration</label></td>
                                <td>
                                    <input type="text" class="form-control" name="duration[]" value="{{$qualification['duration']}}" />
                                </td>
                            </tr>
                
                            <tr>
                                <td><label class="form-label">Achievement</label></td>
                                <td>
                                    <input type="text" class="form-control achieve" name="achieve[]" value="{{$qualification['achieve']}}" />
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
            
    <h3>
        Foundation Training / Others Training
        <button type="button" onclick="addTrainingField()" class="btn btn-primary"><i class="fa fa-plus fa 2x"></i></button>
    </h3>

    <div class="row" id="trainingContainer">
        @foreach ($trainings as $i => $training)
        <div class="col-md-4 trainingData">
            <div class="card card-dynamic">
                <div class="card-header d-flex justify-content-end p-0">
                    <button class="btn btn-link btn-sm remove-training" type="button" data-toggle="tooltip" data-placement="top" title="Remove">
                        <i class="fa fa-times fa-2x text-danger"></i>
                    </button>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <td><label class="form-label">Batch No</label></td>
                            <td>
                                <input type="text" class="form-control batch_no" name="batch_no[]" value="{{$training['batch_no']}}" />
                            </td>
                        </tr>
            
                        <tr>
                            <td><label class="form-label"> Training Title</label></td>
                            <td>
                                <input type="text" class="form-control training_title" name="training_title[]" value="{{$training['training_title']}}" required />
                            </td>
                        </tr>
            
                        <tr>
                            <td><label class="form-label">Training Topics</label> </td>
                            <td>
                                <input type="text" name="training_topics[]" class="form-control training_topics" value="{{$training['training_topics']}}" />
                            </td>
                        </tr>
            
                        <tr>
                            <td><label class="form-label">Institute</label></td>
                            <td>
                                <input type="text" name="training_institute[]" class="form-control training_institute" value="{{$training['training_institute']}}"/>
                            </td>
                        </tr>
            
                        <tr>
                            <td><label class="form-label">Country</label></td>
                            <td>
                                <input type="text" name="training_country[]" class="form-control training_country" value="{{$training['training_country']}}" name="training_country[]"/>
                            </td>
                        </tr>
            
                        <tr>
                            <td><label class="form-label">Location</label></td>
                            <td>
                                <input type="text" name="training_location[]" class="form-control training_location" value="{{$training['training_location']}}" name="training_location"/>
                            </td>
                        </tr>
            
                        <tr>
                            <td><label>Training Year </label></td>
                            <td>
                                <input type="text" name="training_year[]" class="form-control training_year" value="{{$training['training_year']}}" name="training_year[]"/>
                            </td>
                        </tr>
            
                        <tr>
                            <td><label class="form-label">Duration</label></td>
                            <td>
                                <input name="training_from[]" placeholder="From" type="text" class="form-control datepickr training_from" value="{{$training['training_from']}}"  />
            
                                <input placeholder="To" type="text" class="form-control datepickr training_to" value="{{$training['training_to']}}" name="training_to[]"/>
                            </td>
                        </tr>
            
                        <tr>
                            <td><label class="form-label">Period</label></td>
                            <td>
                                <input type="text" class="form-control training_period" value="{{$training['training_period']}}" name="training_period[]"/>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
        
        
    
    
    <h3>Regularization</h3>

    <table class="table">
        <tr>
            <td><label> Regularized:</label></td>
            <td>
                <select class="form-control" name="regular" id="regular">
                    <option <?php if($regular=='yes') echo "selected"; ?> value='yes'>Yes</option>
                    <option <?php if($regular=='no') echo "selected"; ?> value='no'>No</option>
                </select>
            </td>
        </tr>

        <tr>
            <td><label>Date Of Regularization:</label></td>
            <td>
                <input class="form-control datepickr" type="text" name="regular_date" id="regular_date" value="<?php echo $regular_date; ?>" />
            </td>
        </tr>
    </table>

    <h3>Professional Examination</h3>

    <table class="table">
        <tr>
            <td><strong>Departmental Exam:</strong></td>
            <td></td>
        </tr>

        <tr>
            <td><label class="form-label">Gazette Notification Date:</label></td>
            <td>
                <input class="form-control datepickr" type="text" name="gazette_date" id="gazette_date" value="<?php echo $gazette_date; ?>" />
            </td>
        </tr>

        <tr>
            <td><label class="form-label">Status :</label></td>
            <td>
                <input class="form-control" type="text" name="status" id="status" value="<?php echo $status; ?>"/>
            </td>
        </tr>

        <tr>
            <td><label class="form-label">Permanent Date :</label></td>
            <td>
                <input class="form-control datepickr" type="text" name="permanent_date" id="permanent_date" value="<?php echo $permanent_date; ?>" />
            </td>
        </tr>

        <tr>
            <td><strong>Senior Scale Exam :</strong></td>
            <td></td>
        </tr>

        <tr>
            <td><label>Three Paper Pass:</label></td>
            <td>
                <select class="form-control" name="paper_pass" id="paper_pass">
                    <option <?php if($paper_pass=='yes') echo "selected"; ?> value='yes'>Yes</option>
                    <option <?php if($paper_pass=='no') echo "selected"; ?> value='no'>No</option>
                </select>
            </td>
        </tr>

        <tr>
            <td><label>Final Pass Date(Gazette):</label></td>
            <td>
                <input class="form-control datepickr" type="text" name="finalpass_date" id="finalpass_date" value="<?php echo $finalpass_date; ?>" />
            </td>
        </tr>

        <tr>
            <td><label>Award Date(Senior Scale):</label></td>
            <td>
                <input class="form-control datepickr" type="text" name="award_date" id="award_date" value="<?php echo $award_date; ?>" />
            </td>
        </tr>

        <tr>
            <td><strong>Professional Qualification</strong></td>
            <td></td>
        </tr>
        <tr>
            <td><label>Certification :</label></td>
            <td>
                <input class="form-control" type="text" name="prof_certificate" id="prof_certificate" value="<?php echo $prof_certificate; ?>" />
            </td>
        </tr>
        <tr>
            <td><label>Institute :</label></td>
            <td>
                <input class="form-control" type="text" name="prof_institute" id="prof_institute" value="<?php echo $prof_institute; ?>" />
            </td>
        </tr>

        <tr>
            <td><label>Location :</label></td>
            <td>
                <input class="form-control" type="text" name="prof_location" id="prof_location" value="<?php echo $prof_location; ?>"/> 
            </td>
        </tr>

        <tr>
            <td><label>From :</label></td>
            <td>
                <input class="form-control datepickr" type="text" name="prof_from" id="prof_from" value="<?php echo $prof_from; ?>"/> 
            </td>
        </tr>

        <tr>
            <td><label>To :</label></td>
            <td>
                <input class="form-control datepickr" type="text" name="prof_to" id="prof_to" value="<?php echo $prof_to; ?>" />
            </td>
        </tr>
    </table>
</div> 

<div class="form-group row">
    <div class="col-md-12 d-flex justify-content-center">
      <button class="btn btn-primary"><i class="fa fa-check"></i> Update</button>
    </div>
</div>

{!! Form::close() !!}

@push('scripts')
    <script>
        function addQualificationField() {
            var dataContainer = $("#qualificationContainer").find(".qualificationData:first").clone();
            dataContainer.find("select").each(function() {
                $(this).find("option:first").prop("selected", true);
            });
            dataContainer.find("input").val("");
            $("#qualificationContainer").append(dataContainer);
            load_modal_particles();
        }

        $(document).on('click', '.remove-qualification', function(){
            $(this).closest(".qualificationData").remove();
        })

        function addTrainingField() {
            var dataContainer = $("#trainingContainer").find(".trainingData:first").clone();
            dataContainer.find("select").each(function() {
                $(this).find("option:first").prop("selected", true);
            });
            dataContainer.find("input").val("");
            $("#trainingContainer").append(dataContainer);
            load_modal_particles();
        }

        $(document).on('click', '.remove-training', function(){
            $(this).closest(".trainingData").remove();
        })
    </script>
@endpush