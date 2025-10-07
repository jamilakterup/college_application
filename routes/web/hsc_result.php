<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'hsc_result','as'=> 'hsc_result.' ,'namespace'=>'Hsc_result', 'middleware' => 'auth'],function() {   
  Route::any('/download_student_sub_data',['as' => 'download_student_sub_data','uses'=>'SettingSubjectInfoController@download_student_sub_data']);   
  
  Route::resource('/', 'HscResultController');
  Route::resource('subject', 'SettingSubjectController');
  Route::resource('group', 'SettingGroupController');  
  Route::resource('class', 'SettingClassController');
  Route::resource('exam', 'SettingExamController');
  Route::resource('class_test', 'ClassTestController');
  Route::resource('marks_input_config', 'MarkInputConfigController');
   Route::resource('subject_info', 'SettingSubjectInfoController');
    Route::resource('result_publish', 'HscResultPublishController');
    Route::resource('admit_card_publish', 'AdmitCardPublishController');


Route::any('/hsc_result_sms', ['as' => 'hsc_result_sms.index', 'uses' => 'HscResultSmsController@index']);  
Route::any('hsc_result_sms/list', ['as' => 'hsc_result_sms.list', 'uses' => 'HscResultSmsController@marklist']);  
Route::any('hsc_result_sms/store', ['as' => 'hsc_result_sms.store', 'uses' => 'HscResultSmsController@store']);



Route::any('/', ['as' => 'hsc_result.index', 'uses' => 'HscResultController@index']);
  Route::any('mark_input/csv', ['as' => 'mark_input.csv', 'uses' => 'MarkInputController@csv']);
  Route::any('mark_input/csv-upload', ['as' => 'mark_input.csv-upload', 'uses' => 'MarkInputController@csvUpload']);
  // Route::any('mark_input/list', ['as' => 'mark_input.list', 'uses' => 'MarkInputController@marklist']);
  Route::any('mark_input/list', ['as' => 'mark_input.list', 'uses' => 'MarkEntryController@marklist']);
    Route::post('hsc_result/mark_input/save-mark', ['as' => 'mark_input.save_mark', 'uses' => 'MarkEntryController@saveMark']);

Route::get('mark_pdf/{session}/{group}/{current_level}/{exam_id}/{subject_id}/{exam_test_id}/{exam_year}',['as'=>'mark_input.mark_pdf','uses'=>'MarkInputController@MarkPdf']); 

  Route::any('admit_card/list', ['as' => 'admit_card.list', 'uses' => 'AdmitCardController@admitlist']);
   Route::any('transcript/list', ['as' => 'transcript.list', 'uses' => 'TranscriptController@transcriptlist']);
  Route::any('exam_date/list', ['as' => 'exam_date.list', 'uses' => 'ExamDateController@exam_date_list']);
  Route::any('attendence_generate/list', ['as' => 'attendence_generate.list', 'uses' => 'ExamAttendenceGenController@attendence_list']);
  Route::any('attendance_sheet/list', ['as' => 'attendance_sheet.list', 'uses' => 'AttendanceSheetController@marklist']);
  Route::get('merit_list/{id}',['as'=>'process.merit-pdf','uses'=>'ResultProcessingController@MeritListPdf']);
  Route::get('merit_list/excel/{id}',['as'=>'process.merit-excel','uses'=>'ResultProcessingController@MeritListExcel']);
  Route::get('tabulation/{id}/{ex_id}',['as'=>'process.tabulation-pdf','uses'=>'ResultProcessingController@TabulationPdf']);
  Route::get('tabulation/excel/{id}/{ex_id}',['as'=>'process.tabulation-excel','uses'=>'ResultProcessingController@TabulationExcel']);
  Route::get('top-ten',['as'=>'process.top-ten.index','uses'=>'ResultProcessingController@topTenIndex']);
  Route::post('top-ten-download',['as'=>'process.top-ten-download','uses'=>'ResultProcessingController@topTenDownload']);
  Route::post('result-reporting-student-charts',['as'=>'process.student-charts','uses'=>'ResultProcessingController@studentChartDetails']);

  Route::resource('transcript', 'TranscriptController');
  Route::resource('admit_card', 'AdmitCardController');
  Route::resource('sticker', 'StickerController');
  Route::resource('attendence_generate', 'ExamAttendenceGenController');
  Route::resource('mark_input', 'MarkInputController');
  Route::resource('attendance_sheet', 'AttendanceSheetController');
  Route::resource('exam_date', 'ExamDateController');
  Route::resource('process', 'ResultProcessingController');
  
  Route::get('hsc_result/process/indivisual/{process_id}',['as'=>'process.indivisual','uses'=>'ResultProcessingController@processIndivisualView']);
  Route::post('hsc_result/process/indivisual',['as'=>'process.indivisual.store','uses'=>'ResultProcessingController@processIndivisual']);

  Route::resource('examparticle', 'SettingExamParticleController'); 
  Route::resource('assign_exam','SettingAssignExamController',['only' => ['index', 'edit', 'update', 'destroy']]);
  Route::resource('assign_exam_particle','SettingAssignExamParticleController',['only'=>['index']]);
  Route::resource('assign_subject', 'SettingAssignSubjectController', ['only' => ['index']]);
  Route::get('assign_subject_from_mark', 'SettingAssignSubjectController@assign_hsc_subject_from_marks')->name('assign_subject_from_mark');
  Route::post('assign_subject_from_mark', ['as' => 'assign_hsc_subject_from_marks.exe','uses'=>'SettingAssignSubjectController@assign_hsc_subject_from_marks_exe']);

  Route::any('/result_reporting', ['as' => 'result_reporting', 'uses' => 'HscResultController@result_reporting']);
  Route::any('/result_reporting_search', ['as' => 'result_reporting_search', 'uses' => 'HscResultController@result_reporting_search']);
  Route::any('/result_reporting_subject_wise', ['as' => 'result_reporting_subject_wise', 'uses' => 'HscResultController@result_reporting_subject_wise']);

  Route::get('/progress_report', ['as' => 'progress_report', 'uses' => 'HscResultController@progress_report']);
  Route::post('/progress_report/generate', ['as' => 'progress_report.generate', 'uses' => 'HscResultController@progress_report_generate']);


 Route::resource('assign_class_test','SettingAssignClasstestController',['only' => ['index', 'edit', 'update', 'destroy']]);

  Route::get('mark_input/load-exam/{id}', 'MarkInputController@getExam');
  Route::get('mark_input/load-subject/{year}/{id}', 'MarkInputController@getSubject');
  Route::get('mark_input/load-classtest/{examid}', 'MarkInputController@getClasstest');

  Route::get('attendance_sheet/load-exam/{id}', 'AttendanceSheetController@getExam');
  Route::get('attendance_sheet/load-subject/{year}/{id}', 'AttendanceSheetController@getSubject');
  Route::get('attendance_sheet/load-classtest/{examid}', 'AttendanceSheetController@getClasstest');
 


  Route::any('search/subject_info', ['as' => 'subject_info.search', 'uses' => 'SettingSubjectInfoController@search']);

  Route::get('assign-subject/edit/{class_id}/{department_id}', ['as' => 'assign_subject.edit', 'uses' => 'SettingAssignSubjectController@edit']);
  Route::put('assign-subject/update/{class_id}/{department_id}', ['as' => 'assign_subject.update', 'uses' => 'SettingAssignSubjectController@update']);

  Route::get('exam_date/edit/{class_id}/{department_id}', ['as' => 'exam_date.edit', 'uses' => 'ExamDateController@edit']);
  Route::put('exam_date/update/{class_id}/{department_id}', ['as' => 'exam_date.update', 'uses' => 'ExamDateController@update']);


  Route::delete('assign-subject/delete/{class_id}/{department_id}', ['as' => 'assign_subject.destroy', 'uses' => 'SettingAssignSubjectController@destroy']);

  Route::get('assign-exam-particle/edit/{class_id}/{department_id}/{subject_id}', ['as' => 'assign_exam_particle.edit', 'uses' => 'SettingAssignExamParticleController@edit']);
  Route::put('assign-exam-particle/update/{class_id}/{department_id}/{subject_id}', ['as' => 'assign_exam_particle.update', 'uses' => 'SettingAssignExamParticleController@update']);
  Route::delete('assign-exam-particle/delete/{class_id}/{department_id}/{subject_id}', ['as' => 'assign_exam_particle.destroy', 'uses' => 'SettingAssignExamParticleController@destroy']);

  Route::get('student_subject_assign', ['as' => 'student_subject.assign', 'uses' => 'SettingAssignSubjectController@student_subject_assign']);

  Route::post('student_subject_assign', ['as' => 'student_subject.assign.update', 'uses' => 'SettingAssignSubjectController@assignSubject_from_hsc_admit']);
  
  
  Route::get('assign_sub_all', ['as' => 'assign_subject_all.assign', 'uses' => 'SettingAssignSubjectController@assignSubject_from_hsc_admit_all']);


});

Route::group(['prefix' => 'pre/hsc_result', 'namespace'=>'Hsc_result', 'as'=> 'pre_hsc_result.', 'middleware' => 'auth'], function() {
  Route::resource('/', 'PreHscResultController');
});