@php

    $mpdf = new \Mpdf\Mpdf(
        array_merge(addCustomFontToMpdf(), [
            'mode' => 'utf-8',
            'format' => [54.102, 85.598],
            'default_font' => 'lato',
            'font_size' => 14,
        ]),
    );

    $mpdf->SetTitle('ID Card');
    $mpdf->SetAuthor('Raj IT');
    $mpdf->SetSubject(INS_CODE . ' ID Card');
    $mpdf->SetProtection(['print', 'print-highres']);
    $mpdf->ignore_invalid_utf8 = true;

    foreach ($student_info as $val) {
        // Common variables
        $ref_id = $val->refference_id;
        $image = $val->image ?? '';
        $name = $val->name ?? '';
        $father_name = $val->father_name ?? '';
        $mother_name = $val->mother_name ?? '';
        $perm_village = $val->permanent_village;
        $perm_po = $val->permanent_po;
        $perm_ps = $val->permanent_ps;
        $perm_dist = $val->permanent_dist;
        $faculty_name = $val->faculty_name ?? '';
        $dept_name = $val->dept_name ?? '';
        $session = $val->session ?? '';
        $birth_date = $val->birth_date ?? '';
        $contact_no = $val->contact_no ?? '';
        $class_roll = $val->class_roll ?? '';
        $blood_group = $val->blood_group ?? '';
        $current_level = $val->current_level ?? '';
        $level = $current_level;
        $class = explode(' ', $level)[0];

        // Determine admission table and validity
        switch ($category) {
            case 'hsc':
                $adm_table = 'hsc_admitted_students';
                $validity = '30-09-2026';
                break;
            case 'honours':
                $adm_table = 'hons_admitted_student';
                $validity = '30-06-2030';
                break;
            case 'masters':
                $adm_table = 'masters_admitted_student';
                $validity = '31-12-2025';
                break;
            case 'degree':
                $adm_table = 'deg_admitted_student';
                $validity = '30-06-2028';
                break;
        }

        $admitted_student = DB::table($adm_table)->where('auto_id', $ref_id)->first();
        if (!is_null($admitted_student) && empty($blood_group)) {
            $blood_group = $admitted_student->blood_group ?? '';
        }

        // Loop through front (1) and back (2)
        for ($type = 1; $type <= 2; $type++) {
            $mpdf->AddPageByArray([
                'margin-top' => 0,
                'margin-bottom' => 0,
                'margin-left' => 0,
                'margin-right' => 0,
            ]);

            if ($type == 1) {
                // Front page
                switch ($category) {
                    case 'hsc':
                        $pagecount = $mpdf->SetSourceFile(app_path() . '/Libs/hsc_idcard_frame_front.pdf');
                        break;
                    case 'masters':
                        $pagecount = $mpdf->SetSourceFile(app_path() . '/Libs/masters_idcard_front.pdf');
                        break;
                    case 'honours':
                        $pagecount = $mpdf->SetSourceFile(app_path() . '/Libs/hons_idcard_front.pdf');
                        break;
                    case 'degree':
                        $pagecount = $mpdf->SetSourceFile(app_path() . '/Libs/degree_idcard_front.pdf');
                        break;
                }

                $tplIdx = $mpdf->ImportPage($pagecount);
                $mpdf->UseTemplate($tplIdx);
                $mpdf->Image(public_path() . "/upload/college/{$category}/{$session}/{$image}", 17.5, 18.7, 19, 19.25);

                // Add front text
                $mpdf->SetFont('lato', 'B', 8);
                $mpdf->WriteHTML('<div style="text-align:center;font-weight:bold;">' . strtoupper($name) . '</div>');

                $x_offset = $category == 'honours' ? 11 + 14.5 : 5 + 14.5;
                $y_offset = $category == 'honours' ? 31.5 + 19.5 : 33 + 19.5;

                $mpdf->SetFont('lato', 'BL', 7.8);
                $mpdf->SetTextColor(0, 0, 0);
                $mpdf->WriteText($x_offset, $y_offset, $class);
                $mpdf->WriteText($x_offset, $y_offset + 5, $faculty_name);
                $mpdf->WriteText($x_offset, $y_offset + 10, $class_roll);
                $mpdf->WriteText($x_offset, $y_offset + 14, $session);
                if ($category == 'hsc') {
                    $mpdf->WriteText($x_offset, $y_offset + 18, $house ?? '');
                }
                if ($category != 'degree' && $category != 'hsc') {
                    $mpdf->WriteText($x_offset, $y_offset + 18, $dept_name ?? '');
                }
            } else {
                // Back page
                switch ($category) {
                    case 'hsc':
                        $pagecount = $mpdf->SetSourceFile(app_path() . '/Libs/hsc_idcard_frame_back.pdf');
                        break;
                    case 'masters':
                        $pagecount = $mpdf->SetSourceFile(app_path() . '/Libs/masters_idcard_back.pdf');
                        break;
                    case 'honours':
                        $pagecount = $mpdf->SetSourceFile(app_path() . '/Libs/hons_idcard_back.pdf');
                        break;
                    case 'degree':
                        $pagecount = $mpdf->SetSourceFile(app_path() . '/Libs/degree_idcard_back.pdf');
                        break;
                }

                $tplIdx = $mpdf->ImportPage($pagecount);
                $mpdf->UseTemplate($tplIdx);

                $x_offset = 24.3;
                $y_offset = 14.3;

                $mpdf->SetFont('lato', 'BL', 6.3);
                $mpdf->SetTextColor(0, 0, 0);
                $mpdf->WriteText($x_offset, $y_offset, $father_name);
                $mpdf->WriteText($x_offset, $y_offset + 5, $mother_name);
                $mpdf->WriteText($x_offset, $y_offset + 10, $perm_village);
                $mpdf->WriteText($x_offset, $y_offset + 15, $perm_po);
                $mpdf->WriteText($x_offset, $y_offset + 20, $perm_ps);
                $mpdf->WriteText($x_offset, $y_offset + 25, $perm_dist);
                $mpdf->WriteText($x_offset, $y_offset + 29.6, $contact_no);
                $mpdf->WriteText($x_offset, $y_offset + 34.7, $blood_group);
                $mpdf->SetFont('lato', 'BL', 7.5);
                $mpdf->SetTextColor(255, 255, 255);
                $mpdf->WriteText($x_offset - 2, $y_offset + 69.4, $validity);
            }
        }
    }

    // $file_name = public_path()."/download/idcard/id_cards.pdf";
    echo $mpdf->Output('id card -' . $current_level . '.pdf', 'I');
    exit();

    $downlink =
        "<center><a href='" .
        url('/') .
        "/download/idcard/id_cards.pdf' target='_blank'>Click to Download</a></center>";

    Session::put('downlink', $downlink);
    return Redirect::route('students.idcard');
@endphp
