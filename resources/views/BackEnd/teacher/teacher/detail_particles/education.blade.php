@php
    $edu_level=$education->education_level;
    $exam_title=$education->exam_title;
    $group=$education->major_group;
    $institute_name=$education->institute_name;
    $result_edu=$education->result;
    $marks=$education->marks;
    $passing_year=$education->passing_year;
    $duration=$education->duration;
    $achievement=$education->achievement;
    $batch_no=$education->batch_no;
    $training_title=$education->training_title;
    $training_topics=$education->training_topics;
    $training_institute=$education->training_institute;
    $training_country=$education->training_country;
    $training_location=$education->training_location;
    $training_year=$education->training_year;
    $training_from=$education->training_from;
    $training_to=$education->training_to;
    $training_period=$education->training_period;
    $regular=$education->regular;
    $regular_date=$education->regular_date;
    $gazette_date=$education->gazette_date;
    $status=$education->status;
    $permanent_date=$education->permanent_date;
    $paper_pass=$education->paper_pass;
    $finalpass_date=$education->finalpass_date;
    $award_date=$education->award_date;
    $prof_certificate=$education->prof_certificate;
    $prof_institute=$education->prof_institute;
    $prof_location=$education->prof_location ;
    $prof_from=$education->prof_from ;
    $prof_to=$education->prof_to;
    
    $edu_level_array=explode(',',$edu_level);
    $group_array=explode(',',$group);
    $institute_name_array=explode(',',$institute_name);
    $result_array=explode(',',$result_edu);
    $marks_array=explode(',',$marks);
    $passing_year_array=explode(',',$passing_year);
    $duration_array=explode(',',$duration);
    $achieve_array=explode(',',$achievement);
    $batch_no_array=explode(',',$batch_no);
    $training_topics_array=explode(',',$training_topics);
    $training_institute_array=explode(',',$training_institute);
    $training_country_array=explode(',',$training_country);
    $training_location_array=explode(',',$training_location);
    $training_year_array=explode(',',$training_year);
    $training_from_array=explode(',',$training_from);
    $training_to_array=explode(',',$training_to);
    $training_period_array=explode(',',$training_period);
    $exam_title_array=explode(',',$exam_title);
    $training_title_array=explode(',',$training_title);
    
    if ($exam_title=='')
        $no_education=1;
    else
    {
        $no_education=count($exam_title_array);
        $no_education--;
    }
    
    if ($training_title=='')
        $no_training=1;
    else
    {
        $no_training=count($training_title_array);
        $no_training--;
    }
@endphp
<div class="header d-flex justify-content-between align-items-center">
    <h4>Academic Qualification</h4>
    <button class="btn btn-default flex-nowrap" type="button" onclick="PrintElem('#print_details2')"><i class="fa fa-print"></i></button>
</div>
<div id='print_details2'>
    <table  align="center" width="100%" class="table table-hover">
        
        <?php for($i=1;$i<=$no_education;$i++) { ?>
            <tr>
                <td>Education Level</td>
                <td><?php if ( isset($edu_level_array[1])) { echo $edu_level_array[$i];}?></td>
            </tr>
            
            <tr>
                <td>Exam/Degree Title</td>
                <td><?php if ( isset($exam_title_array[$i])) { echo $exam_title_array[$i];}?></td>
            </tr>
            
            <tr>
                <td>Concentration/Major/Group</td>
                <td><?php if ( isset($group_array[$i])) { echo $group_array[$i];}?></td>
            </tr>
            
            <tr>
                <td>Institute Name</td>
                <td><?php if ( isset($institute_name_array[$i])) { echo $institute_name_array[$i];}?></td>
            </tr>
            
            <tr>
                <td>Result</td>
                <td><?php if ( isset($result_array[$i])) { echo $result_array[$i];}?></td>
            </tr>
            
            
            <tr>
                <td>Marks(%)/CGPA/GPA</td>
                <td><?php if ( isset($marks_array[$i])) { echo $marks_array[$i];}?></td>
            </tr>
            
            
            <tr>
                <td>Year of Passing</td>
                <td><?php if ( isset($passing_year_array[$i])) { echo $passing_year_array[$i];}?></td>
            </tr>
            
            
            <tr>
                <td>Duration</td>
                <td><?php if ( isset($duration_array[$i])) { echo $duration_array[$i];}?></td>
            </tr>
            
            
            <tr>
                <td>Achievement</td>
                <td><?php if ( isset($achieve_array[$i])) { echo $achieve_array[$i];}?></td>
            </tr>
            <?php } ?>
            <?php for($i=1;$i<=$no_training;$i++) { ?>
                <tr>
                    <td><h3>Foundation Training / Others Training</h3></td>
                    <td>&nbsp;</td>
                </tr>
                
                <tr>
                    <td>Training Title</td>
                    <td><?php if ( isset($training_title_array[$i])) { echo $training_title_array[$i];} ?></td>
                </tr>
                
                <tr>
                    <td>Training Topics</td>
                    <td><?php if ( isset($training_topics_array[$i])) { echo $training_topics_array[$i];} ?></td>
                </tr>
                
                <tr>
                    <td>Institute</td>
                    <td><?php  if ( isset($training_institute_array[$i])) { echo $training_institute_array[$i];} ?></td>
                </tr>
                
                <tr>
                    <td>Country</td>
                    <td><?php if ( isset($training_country_array[$i])) { echo $training_country_array[$i]; }?></td>
                </tr>
                
                <tr>
                    <td>Location</td>
                    <td><?php if ( isset($training_location_array[$i])) { echo $training_location_array[$i]; }?></td>
                </tr>
                
                <tr>
                    <td>Year</td>
                    <td><?php if ( isset($training_year_array[$i])) { echo $training_year_array[$i]; }?></td>
                </tr>
                
                <tr>
                    <td>Duration</td>
                    <td><?php if ( isset($training_from_array[$i]) && isset($training_to_array[$i])) { echo $training_from_array[$i].' - '.$training_to_array[$i]; }?></td>
                </tr>
                
                <tr>
                    <td>Period</td>
                    <td><?php if (  isset($edu_level_array[$i])) { echo $edu_level_array[$i];} ?></td>
                </tr>
                <?php } ?>
                
                
                <tr>
                    <td colspan="2"><h3>Regularization</h3></td>
                    
                </tr>
                
                <tr>
                    <td> Regularized:</td>
                    <td><?php echo $regular; ?></td>
                </tr>
                
                <tr>
                    <td>Date Of Regularization:</td>
                    <td><?php echo $regular_date; ?></td>
                </tr>
                
                
                <tr> 
                    <td colspan="2"><h3>Professional Examination</h3></td>
                </tr>
                
                <tr>
                    <td colspan="2"><b>Departmental Exam:</b></td>
                </tr>
                <tr></tr>
                
                <tr>
                    <td>Gazette Notification Date:</td>
                    <td><?php echo $gazette_date; ?></td>
                </tr>
                
                <tr>
                    <td>Status :</td>
                    <td><?php echo $status; ?></td>
                </tr>
                
                <tr>
                    <td>Permanent Date :</td>
                    <td><?php echo $permanent_date; ?></td>
                </tr>
                
                <tr>
                    <td colspan="2"><b>Senior Scale Exam :</b></td>
                </tr>
                
                <tr>
                    <td>Three Paper Pass:</td>
                    <td><?php echo $paper_pass; ?></td>
                </tr>
                
                <tr>
                    <td>Final Pass Date(Gazette Notification):</td>
                    <td><?php echo $finalpass_date; ?></td>
                </tr>
                
                <tr>
                    <td>Award Date(Senior Scale):</td>
                    <td><?php echo $award_date; ?></td>
                </tr>
                
                
                
                
                <tr>
                    <td colspan="2"><b>Professional Qualification:</b></td>
                </tr>
                
                <tr>
                    <td>Certification :</td>
                    <td><?php echo $prof_certificate; ?></td>
                </tr>
                
                <tr>
                    <td>Institute :</td>
                    <td><?php echo $prof_institute; ?></td>
                </tr>
                
                <tr>
                    <td>Location :</td>
                    <td><?php echo $prof_location; ?></td>
                </tr>
                
                <tr>
                    <td colspan="2"><b>Duration:</b></td>
                </tr>
                
                
                <tr>
                    <td>From :</td>
                    <td><?php echo $prof_from; ?></td>
                </tr>
                
                <tr>
                    <td>To :</td>
                    <td><?php echo $prof_to; ?></td>
                </tr>
            </table>
        </div>