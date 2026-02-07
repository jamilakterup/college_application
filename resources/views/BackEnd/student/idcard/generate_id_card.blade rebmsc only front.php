@php
use SimpleSoftwareIO\QrCode\Facades\QrCode;

$mpdf = new \Mpdf\Mpdf(
array_merge(addCustomFontToMpdf(), [
'mode' => 'utf-8',
'format' => [54.102, 85.598],
'default_font' => 'lato',
'font_size' => 14
])
);

$mpdf->SetTitle('ID Card');
$mpdf->SetAuthor('Raj IT');
$mpdf->SetSubject(INS_CODE.' ID Card');
$mpdf->SetProtection(['print','print-highres']);
$mpdf->ignore_invalid_utf8 = true;

foreach($student_info as $val) {

// Common variables
$id = $val->id ?? '';
$class_roll = $val->class_roll ?? '';
$ref_id = $val->refference_id;
$image = $val->image ?? '';
$name = $val->name ?? '';
$birth_date = $val->birth_date ?? '';
$father_name = $val->father_name ?? '';
$mother_name = $val->mother_name ?? '';
$perm_village = $val->permanent_village;
$perm_po = $val->permanent_po;
$perm_ps = $val->permanent_ps;
$perm_dist = $val->permanent_dist;
$faculty_name= $val->faculty_name ?? '';
$dept_name = $val->dept_name ?? '';
$session = $val->session ?? '';
$contact_no = $val->contact_no ?? '';
$blood_group = $val->blood_group ?? '';
$current_level = $val->current_level ?? '';
$class = explode(' ', $current_level)[0];

// Determine admission table and validity
switch ($category) {
case 'hsc': $adm_table = 'hsc_admitted_students'; $validity = '30-06-2027'; break;
case 'honours': $adm_table = 'hons_admitted_student'; $validity = '30-06-2030'; break;
case 'masters': $adm_table = 'masters_admitted_student'; $validity = '31-12-2025'; break;
case 'degree': $adm_table = 'deg_admitted_student'; $validity = '30-06-2028'; break;
}

$admitted_student = DB::table($adm_table)->where('auto_id', $ref_id)->first();
if ($admitted_student && empty($blood_group)) {
$blood_group = $admitted_student->blood_group ?? '';
}


$mpdf->AddPageByArray([
'margin-top'=> 0,
'margin-bottom'=> 0,
'margin-left'=> 0,
'margin-right'=> 0,
]);

// FRONT PAGE ---------------------------
if ($type == 1) {

switch ($category) {
case 'hsc': $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/hsc_idcard_frame_front.pdf'); break;
case 'masters': $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/masters_idcard_front.pdf'); break;
case 'honours': $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/hons_idcard_front.pdf'); break;
case 'degree': $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/degree_idcard_front.pdf'); break;
}

$tplIdx = $mpdf->ImportPage($pagecount);
$mpdf->UseTemplate($tplIdx);

// Photo
$mpdf->Image(public_path("upload/college/{$category}/{$session}/{$image}"), 17.5, 12, 19, 19.25);

// Name text
$name_html = '<div style="width:80%;margin:-4px auto 0;text-align:center;
                font-size:9pt;line-height:1;color:#0302A9;font-weight:bold;">
    <p>'.strtoupper($name).'</p>
</div>';

$mpdf->WriteHTML($name_html);

// Other fields
$mpdf->SetFont('lato','BL',7.8);
$mpdf->SetTextColor(0,0,0);

if ($category == 'honours') { $x_offset = 11; $y_offset = 31.5; }
else { $x_offset = 5; $y_offset = 33; }

$mpdf->WriteText($x_offset+14.5, $y_offset+9.5, $id);
$mpdf->WriteText($x_offset+14.5, $y_offset+13.4, $class_roll);
$mpdf->WriteText($x_offset+14.5, $y_offset+17.7, $class);
$mpdf->WriteText($x_offset+14.5, $y_offset+22, $faculty_name);
$mpdf->WriteText($x_offset+14.5, $y_offset+26.1, $birth_date);
$mpdf->WriteText($x_offset+14.5, $y_offset+30.3, $blood_group.' (VE)');
$mpdf->WriteText($x_offset+14.5, $y_offset+34.8, $contact_no);
$mpdf->WriteText($x_offset+14.5, $y_offset+39, $father_name);

// sign
$mpdf->Image(public_path("img/principal_sig.jpeg"), 38, 74, 16, 6);
}

// BACK PAGE ---------------------------
else {

switch ($category) {
case 'hsc': $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/hsc_idcard_frame_back.pdf'); break;
case 'masters': $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/masters_idcard_back.pdf'); break;
case 'honours': $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/hons_idcard_back.pdf'); break;
case 'degree': $pagecount = $mpdf->SetSourceFile(app_path().'/Libs/degree_idcard_back.pdf'); break;
}

$tplIdx = $mpdf->ImportPage($pagecount);
$mpdf->UseTemplate($tplIdx);

$x_offset = 24.3;
$y_offset = 14.6;

$mpdf->SetFont('lato','BL',6.3);

$qrData = "ID: {$id}\nName: {$name}\nContact: {$contact_no}";
$qrPath = storage_path("app/public/qr_{$id}.png");

QrCode::format('png')
->size(200)
->margin(1)
->generate($qrData, $qrPath);

$mpdf->Image($qrPath, 15.2, 9.2, 23.5, 23.5);

$mpdf->SetFont('lato','BL',7.5);
$mpdf->WriteText(28, 80, '08-10-2025');
}
}

echo $mpdf->Output('id card -'.$current_level.'.pdf',"I");
exit();

@endphp