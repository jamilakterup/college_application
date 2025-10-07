<?php
    //personal
    $id = $details->id;
    $name=$details->name;
    $father_name=$details->father_name;
    $mother_name=$details->mother_Name;
    $birth_date=date('d/m/Y',strtotime($details->birth_date));
    $gender=$details->gender;
    $marital_status=$details->marital_status;
    $nationality=$details->nationality;
    $religion=$details->religion;
    $present_address=$details->present_address;
    $permanent_address=$details->permanent_address;
    $home_district=$details->home_district;
    $phone_office=$details->phone_office;
    $phone_home=$details->phone_home;
    $personal_mobile=$details->personal_mobile;
    $email=$details->email;
    $alternate_email=$details->alternate_email;
    $image=$details->image;

	if($birth_date=='01/01/1970') $birth_date='';

	if($image=='') $image="default_image.jpg";
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
    $regular_date=date("d/m/Y",strtotime($education->regular_date));
    $gazette_date=date("d/m/Y",strtotime($education->gazette_date));
    $status=$education->status;
    $permanent_date=date("d/m/Y",strtotime($education->permanent_date));
    $paper_pass=$education->paper_pass;
    $finalpass_date=date("d/m/Y",strtotime($education->finalpass_date));
    $award_date=date("d/m/Y",strtotime($education->award_date));
    $prof_certificate=$education->prof_certificate;
    $prof_institute=$education->prof_institute;
    $prof_location=$education->prof_location ;
    $prof_from=date("d/m/Y",strtotime($education->prof_from)) ;
    $prof_to=date("d/m/Y",strtotime($education->prof_to));


	if($regular_date=='01/01/1970') $regular_date='';
	if($gazette_date=='01/01/1970') $gazette_date='';
	if($permanent_date=='01/01/1970') $permanent_date='';
	if($finalpass_date=='01/01/1970') $finalpass_date='';
	if($award_date=='01/01/1970') $award_date='';
	if($prof_from=='01/01/1970') $prof_from='';
	if($prof_to=='01/01/1970') $prof_to='';

    $qualifications = filter_empty_array($qualifications);
    $trainings = filter_empty_array($trainings);
	
	//employment
    $employer_name=$employment->employer_name;
    $employer_district=$employment->employer_district;
    $employer_thana=$employment->employer_thana;
    $nature_position=$employment->nature_position;
    $held_position=$employment->held_position;
    $office=$employment->office;
    $responsibility=$employment->responsibility;
    $payment_scale=$employment->payment_scale;
    $present_salary =$employment->present_salary;
    $to_continue=$employment->to_continue;
    $service_area =$employment->service_area;

	
    $dept_name=$details->department ;
    $from_date=date('d/m/Y',strtotime($details->join_date));
    $to_date=$details->release_date;
    $original_position =$details->position;

	
	$results=DB::select('select * from designation WHERE id="'.$original_position.'"');
	foreach($results as $result)
	{
	    $held_position_name=$result->name;
	    break;
	}

	
	$results=DB::select('select * from designation WHERE id="'.$original_position.'"');
	foreach($results as $result)
	{
	    $original_position_name=$result->name;
	    break;
	}

	//career
    $career_from=$career->career_from;
    $career_to=$career->career_to;
    $career_description =$career->career_description;
    $special_memo=$career->special_memo;
    $activity_memo=$career->activity_memo;
    $language_name1=$career->language_name1;
    $read_skill1=$career->read_skill1;
    $write_skill1=$career->write_skill1;
    $speak_skill1=$career->speak_skill1;
    $language_name2=$career->language_name2;
    $read_skill2=$career->read_skill2;
    $write_skill2=$career->write_skill2;
    $speak_skill2=$career->speak_skill2;
    $language_name3=$career->language_name3;
    $read_skill3=$career->read_skill3;
    $write_skill3=$career->write_skill3;
    $speak_skill3=$career->speak_skill3;
    
    $emrgnc_name=$career->emrgnc_name;
    $emrgnc_position_held=$career->emrgnc_position_held;
    $emrgnc_address=$career->emrgnc_address;
    $emrgnc_mobile =$career->emrgnc_mobile ;
    $emrgnc_email =$career->emrgnc_email ;
    $relation=$career->relation;

	//appointment
    $appointment_type=$appointment->appointment_type;
    $bcs_no=$appointment->bcs_no;
    $bcs_position=$appointment->bcs_position;
    $bcs_go_no=$appointment->bcs_go_no;
    $bcs_appointment_date=date("d/m/Y",strtotime($appointment->bcs_appointment_date));
    $institute_name=$appointment->institute_name;
    $bcs_joining_date=date("d/m/Y",strtotime($appointment->bcs_joining_date));
    $bcs_ending_date=date("d/m/Y",strtotime($appointment->bcs_ending_date));
    $bcs_job_field=$appointment->bcs_job_field;
    $psc_no=$appointment->psc_no ;
    $psc_position=$appointment->psc_position;
    $psc_go_no=$appointment->psc_go_no;
    $psc_appointment_date=date("d/m/Y",strtotime($appointment->psc_appointment_date));
    $psc_joining_date=date("d/m/Y",strtotime($appointment->psc_joining_date));
    $private_service=$appointment->private_service;
    $additional_go_no =$appointment->additional_go_no ;
    $absorption_date =date("d/m/Y",strtotime($appointment->absorption_date));
    $effective_service=$appointment->effective_service;
    $assistant_prof_go_no=$appointment->assistant_prof_go_no;
    $assistant_prof_go_date =date("d/m/Y",strtotime($appointment->assistant_prof_go_date));
    $assistant_prof_joining_date =date("d/m/Y",strtotime($appointment->assistant_prof_joining_date));
    $associate_prof_go_no  =$appointment->associate_prof_go_no ;
    $associate_prof_go_date  =date("d/m/Y",strtotime($appointment->associate_prof_go_date));
    $associate_prof_joining_date  =date("d/m/Y",strtotime($appointment->associate_prof_joining_date));
    $prof_go_no =$appointment->prof_go_no;
    $prof_go_date =date("d/m/Y",strtotime($appointment->prof_go_date));
    $prof_joining_date =date("d/m/Y",strtotime($appointment->prof_joining_date));

	if($bcs_appointment_date=='01/01/1970') $bcs_appointment_date='';
	if($bcs_joining_date=='01/01/1970') $bcs_joining_date='';
	if($bcs_ending_date=='01/01/1970') $bcs_ending_date='';
	if($psc_appointment_date=='01/01/1970') $psc_appointment_date='';
	if($psc_joining_date=='01/01/1970') $psc_joining_date='';
	if($absorption_date=='01/01/1970') $absorption_date='';
	if($assistant_prof_go_date=='01/01/1970') $assistant_prof_go_date='';
	if($assistant_prof_joining_date=='01/01/1970') $assistant_prof_joining_date='';
	if($associate_prof_go_date=='01/01/1970') $associate_prof_go_date='';
	if($associate_prof_joining_date=='01/01/1970') $associate_prof_joining_date='';
	if($prof_go_date=='01/01/1970') $prof_go_date='';
	if($prof_joining_date=='01/01/1970') $prof_joining_date='';

    $mpdf = new Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 10,'times']);
	$pagecount = $mpdf->SetSourceFile(app_path().'/libs/pds_template.pdf');
	addMpdfPageSetup($mpdf);

	for($j=1;$j<=$pagecount;$j++)
	{
		$mpdf->AddPage();
		$tplId = $mpdf->ImportPage($j);
		$actualsize = $mpdf->UseTemplate($tplId);
		
		if($j==1)
		{
			$path=URL::to('/').'/'.$image ;
			$html='<img style="border:1px solid black;float:right;margin-top:100px" height="140" width="120" src="'.$path.'"/>';
            if($details->image)
                $mpdf->WriteHTML($html);

            $mpdf->WriteText(52,46.7,$id);
			$mpdf->WriteText(52,63.9,strtoupper($name));
			$mpdf->WriteText(52,72.5,strtoupper($father_name));
			$mpdf->WriteText(52,81.1,strtoupper($mother_name));
			$mpdf->WriteText(52,89,substr($present_address, 0, 54));
			$mpdf->WriteText(52,93.6,substr($present_address, 54, 54));
			$mpdf->WriteText(52,97.6,substr($present_address,108, 54));
			$mpdf->WriteText(52,105.7,$personal_mobile);
			$mpdf->WriteText(130,105.7,$email);
			$mpdf->WriteText(52,115,substr($present_address, 0, 54));
			$mpdf->WriteText(52,119,substr($present_address, 54, 54));
			$mpdf->WriteText(52,123,substr($present_address,108, 54).' - '.$home_district);
			$mpdf->WriteText(52,130.1,$birth_date);
			$mpdf->WriteText(130,130.1,ucfirst($gender));
			$mpdf->WriteText(52,138.9,ucfirst($marital_status));
			$mpdf->WriteText(130,138.9,ucfirst($religion));
			
			$y=230;
            
            foreach($qualifications as $i => $qu){
                if(strlen($qu['exam_title'])<=15)
                    $mpdf->WriteText(23,$y,$qu['exam_title']);
                else
                {
                    $mpdf->WriteText(23,$y,substr($qu['exam_title'], 0, 15));
                    $mpdf->WriteText(23,$y+4,substr($qu['exam_title'], 15, 15));
                }

                if(strlen($qu['group'])<=25)
                $mpdf->WriteText(55,$y,$qu['group']);
                else
                {
                    $mpdf->WriteText(55,$y,substr($qu['group'], 0, 25));
                    $mpdf->WriteText(55,$y+4,substr($qu['group'], 25, 25));
                }

                if(strlen($qu['institute_name'])<=25)
                $mpdf->WriteText(117,$y,$qu['institute_name']);
                else
                {
                    $mpdf->WriteText(117,$y,substr($qu['institute_name'], 0, 25));
                    $mpdf->WriteText(117,$y+4,substr($qu['institute_name'], 25, 25));
                }

                if(strlen($qu['marks'])<=15)
                $mpdf->WriteText(162,$y,$qu['marks']);
                else
                {
                    $mpdf->WriteText(162,$y,substr($qu['marks'], 0, 15));
                    $mpdf->WriteText(162,$y+4,substr($qu['marks'], 15, 15));
                }

                $mpdf->WriteText(192,$y,$qu['passing_year']);

                $y+=12;
            }
		}

		else if($j==2)
		{
			$mpdf->WriteText(17,78,ucfirst($language_name1));
			$mpdf->WriteText(111,78,ucfirst($read_skill1));
			$mpdf->WriteText(133.5,78,ucfirst($speak_skill1));
			$mpdf->WriteText(156,78,ucfirst($write_skill1));
			$mpdf->WriteText(17,86,ucfirst($language_name2));
			$mpdf->WriteText(111,86,ucfirst($read_skill2));
			$mpdf->WriteText(133.5,86,ucfirst($speak_skill2));
			$mpdf->WriteText(156,86,ucfirst($write_skill2));
			$mpdf->WriteText(17,94,ucfirst($language_name3));
			$mpdf->WriteText(111,94,ucfirst($read_skill3));
			$mpdf->WriteText(133.5,94,ucfirst($speak_skill3));
			$mpdf->WriteText(156,94,ucfirst($write_skill3));

			$mpdf->WriteText(66,143,ucfirst(@$original_position_name));
			$mpdf->WriteText(142,143,ucfirst(@$held_position_name));
			$mpdf->WriteText(66,151.6,ucfirst($dept_name));
			$mpdf->WriteText(66,177.4,$from_date);
			$mpdf->WriteText(66,193.7,$payment_scale);
			$mpdf->WriteText(168,193.7,$present_salary);
			$mpdf->WriteText(66,201.8,$appointment_type);
			$mpdf->WriteText(66,226.5,$absorption_date);
			
			if(strlen($effective_service)<=15)
			$mpdf->WriteText(168,226.5,$effective_service);
			else
			{
				$mpdf->WriteText(168,226.5,substr($effective_service, 0, 15));
				$mpdf->WriteText(168,230.5,substr($effective_service, 15, 15));
				$mpdf->WriteText(168,234.5,substr($effective_service, 30, 15));
			}

			if($appointment_type=='BCS')
			{
				$mpdf->WriteText(66,269,$bcs_no);
				$mpdf->WriteText(168,269,$bcs_position);
				$mpdf->WriteText(66,277,$bcs_go_no);
				$mpdf->WriteText(66,281,$bcs_appointment_date);
				$mpdf->WriteText(168,277,$bcs_joining_date);
			}
			else if($appointment_type=='PSC')
			{
				$mpdf->WriteText(66,269,$psc_no);
				$mpdf->WriteText(168,269,$psc_position);
				$mpdf->WriteText(66,277,$psc_go_no);
				$mpdf->WriteText(66,281,$psc_appointment_date);
				$mpdf->WriteText(168,277,$psc_joining_date);
			}

		}

		else if($j==3)
		{
			$mpdf->WriteText(18.5,245,"Assistant Professor");
		    $mpdf->WriteText(89,245,$assistant_prof_go_no.", ".$assistant_prof_go_date);
		    $mpdf->WriteText(163,245,$assistant_prof_joining_date);

		    $mpdf->WriteText(18.5,251,"Associate Professor");
		    $mpdf->WriteText(89,251,$associate_prof_go_no.", ".$associate_prof_go_date);
		    $mpdf->WriteText(163,251,$associate_prof_joining_date);

		    $mpdf->WriteText(18.5,257,"Professor");
		    $mpdf->WriteText(89,257,$prof_go_no.", ".$prof_go_date);
		    $mpdf->WriteText(163,257,$prof_joining_date);

			$mpdf->WriteText(53,30,$permanent_date);
			$mpdf->WriteText(53,38,$status);
			$mpdf->WriteText(115,54,$gazette_date);

			$mpdf->WriteText(53,84,@$batch_no_array[1]);			
			$mpdf->WriteText(117,84,@date("d/m/Y",strtotime($training_from_array[1])));
			$mpdf->WriteText(173,84,@date("d/m/Y",strtotime($training_to_array[1])));
			$mpdf->WriteText(117,89,@$training_period_array[1]);
			$mpdf->WriteText(80,95,$regular_date);
			$mpdf->WriteText(53,106,$finalpass_date);		
		}

		else if($j==4)
		{			
			$y=40;
            foreach($trainings as $training){
                if(strlen($training['training_title'])<=22)
                    $mpdf->WriteText(18,$y,$training['training_title']);
                else
                {
                    $mpdf->WriteText(18,$y,substr($training['training_title'], 0, 22));
                    $mpdf->WriteText(18,$y+4,substr($training['training_title'], 22, 44));
                }

                if(strlen($training['training_institute'])<=27)
                $mpdf->WriteText(73,$y,$training['training_institute']);
                else
                {
                    $mpdf->WriteText(73,$y,substr($training['training_institute'], 0, 27));
                    $mpdf->WriteText(73,$y+4,substr($training['training_institute'], 27, 54));
                }

                if(date("d/m/Y",strtotime($training['training_from']))!='01/01/1970')
                $mpdf->WriteText(135,$y,date("d/m/Y",strtotime($training['training_from'])));
                if(date("d/m/Y",strtotime($training['training_to']))!='01/01/1970')
                $mpdf->WriteText(170,$y,date("d/m/Y",strtotime($training['training_to'])));
                $y+=12;
            }
		}
	}

	$pdfFileName = public_path()."/download/teacher/{$id}.pdf";
    $url = url("/download/teacher/{$id}.pdf");
	$mpdf->Output($pdfFileName, "F");

?>

<center><a style="margin-top: 15px;font-size: 25px;color:red;" href="{{$url}}">Download PDS</a></center>