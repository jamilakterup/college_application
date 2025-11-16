<?php
use Illuminate\Support\Facades\Route;

//Students Route
Route::group(['prefix' => 'students', 'namespace'=>'Student', 'middleware' => 'auth'],function() {

        Route::get('/', ['as' => 'student', 'uses' => 'StudentController@student']);
        // Route::get('honours', ['as' => 'student.honours', 'uses' => 'HonoursController@student']);

        Route::post('/drop',function()
		{

           if(Request::ajax()){
           		//$groups=Input::get('groups');
           		echo "hklk";
           		echo json_encode("hello");	
			}
		});

		// Probale List Section
		
		Route::get('merit-list', 'MeritListController@index')->name('student.meritlist.index');
		// index
		Route::get('merit-list/honours', 'MeritListController@honours_index')->name('student.meritlist.honours');
		Route::get('merit-list/masters', 'MeritListController@masters_index')->name('student.meritlist.masters');
		Route::get('merit-list/degree', 'MeritListController@degree_index')->name('student.meritlist.degree');
		Route::get('merit-list/hsc', 'MeritListController@hsc_index')->name('student.meritlist.hsc');
		Route::get('merit-list/create', 'MeritListController@create')->name('student.meritlist.create');
		// datasource
		Route::post('merit-list/honours/datasource', 'MeritListController@honours_datasource')->name('student.meritlist.honours.datasource');
		Route::post('merit-list/masters/datasource', 'MeritListController@masters_datasource')->name('student.meritlist.masters.datasource');
		Route::post('merit-list/degree/datasource', 'MeritListController@degree_datasource')->name('student.meritlist.degree.datasource');
		Route::post('merit-list/hsc/datasource', 'MeritListController@hsc_datasource')->name('student.meritlist.hsc.datasource');
		// edit
		Route::get('merit-list/honours/edit/{auto_id}', 'MeritListController@honours_edit')->name('student.meritlist.honours.edit');
		Route::get('merit-list/masters/edit/{auto_id}', 'MeritListController@masters_edit')->name('student.meritlist.masters.edit');
		Route::get('merit-list/degree/edit/{auto_id}', 'MeritListController@degree_edit')->name('student.meritlist.degree.edit');
		Route::get('merit-list/hsc/edit/{auto_id}', 'MeritListController@hsc_edit')->name('student.meritlist.hsc.edit');
		// store/update
		Route::post('merit-list/honours/store', 'MeritListController@honours_store')->name('student.meritlist.honours.store');
		Route::post('merit-list/masters/store', 'MeritListController@masters_store')->name('student.meritlist.masters.store');
		Route::post('merit-list/degree/store', 'MeritListController@degree_store')->name('student.meritlist.degree.store');
		Route::post('merit-list/hsc/store', 'MeritListController@hsc_store')->name('student.meritlist.hsc.store');
		// essentials
		Route::get('merit-list/upload', 'MeritListController@upload')->name('student.meritlist.upload');
		Route::post('merit-list/upload/exe', 'MeritListController@upload_exe')->name('student.meritlist.upload.exe');
		Route::delete('merit-list/destroy', 'MeritListController@destroy')->name('student.meritlist.destroy');
       
});

Route::group(['namespace'=>'Student', 'middleware' => 'auth'],function() {

	Route::any('/hsc/2nd/promotion', ['as' => 'student.hsc.2nd.promotion.index', 'uses' => 'Hsc2ndYearController@hsc_2nd_year_promotion']);

	// Hsc 2nd year invoice
	Route::any('/hsc/2nd/promotion/invoice', ['as' => 'student.hsc.promotion.invoice', 'uses' => 'Hsc2ndYearController@invoice']);
	Route::post('/hsc/promotion/action', ['as' => 'student.hsc.promotion.invoice.action', 'uses' => 'Hsc2ndYearController@invoice_action']);

	// StudentFormFillupController
	Route::group(['before' => 'auth','prefix' => 'formfillup'],function() {
		Route::any('/degree', ['as' => 'student.formfillup.degree', 'uses' => 'StudentsFormfillupController@degreeformfillup']);
		Route::any('/honours', ['as' => 'student.formfillup.honours', 'uses' => 'StudentsFormfillupController@honoursformfillup']);
		Route::any('/masters', ['as' => 'student.formfillup.masters', 'uses' => 'StudentsFormfillupController@mastersformfillup']);

		// hsc ff reports
	    Route::resource('form', 'StudentsFormfillupController');
		Route::any('/ffsearch', ['as' => 'students.formfillup.search', 'uses' => 'StudentsFormfillupController@Search']);

		Route::any('/ffreport', ['as' => 'student.formfillup.report', 'uses' => 'StudentsFormfillupController@generateFFReport']);
	    Route::any('/hscformfillup', ['as' => 'student.formfillup.hscformfillup', 'uses' => 'StudentsFormfillupController@hscformfillup']);
		Route::any('/hscffsearch', ['as' => 'student.hscformfillup.search', 'uses' => 'StudentsFormfillupController@hscSearch']);
		Route::any('/hscffreport', ['as' => 'student.hscformfillup.report', 'uses' => 'Hsc2ndYearController@hscgenerateFFReport']);

		//honours ff report
		Route::any('/honffindex', ['as' => 'honformfillup.honffindex', 'uses' => 'StudentsFormfillupController@Honffindex']);
		Route::any('/honffsearch', ['as' => 'formfillup.honosearch', 'uses' => 'StudentsFormfillupController@HonoSearch']);
		Route::any('/honffreport', ['as' => 'student.formfillup.honreport', 'uses' => 'StudentsFormfillupController@generateHonFFReport']);

		// degree ff report
		Route::any('/degffindex', ['as' => 'degformfillup.degffindex', 'uses' => 'StudentsFormfillupController@Degffindex']);
		Route::any('/degffsearch', ['as' => 'formfillup.degffsearch', 'uses' => 'StudentsFormfillupController@DegSearch']);

		Route::any('/degffreport', ['as' => 'student.formfillup.degffreport', 'uses' => 'StudentsFormfillupController@generateDegFFReport']);

		// master ff report
		Route::any('/mastersffreport', ['as' => 'student.formfillup.mastersffreport', 'uses' => 'StudentsFormfillupController@generateMastersFFReport']);

		// Probale List Section
		
		Route::get('probable-list', 'ProbableListController@index')->name('student.prblist.index');
		// index
		Route::get('probable-list/honours', 'ProbableListController@honours_index')->name('student.prblist.honours');
		Route::get('probable-list/masters', 'ProbableListController@masters_index')->name('student.prblist.masters');
		Route::get('probable-list/degree', 'ProbableListController@degree_index')->name('student.prblist.degree');
		Route::get('probable-list/hsc', 'ProbableListController@hsc_index')->name('student.prblist.hsc');
		Route::get('probable-list/create', 'ProbableListController@create')->name('student.prblist.create');
		// datasource
		Route::post('probable-list/honours/datasource', 'ProbableListController@honours_datasource')->name('student.prblist.honours.datasource');
		Route::post('probable-list/masters/datasource', 'ProbableListController@masters_datasource')->name('student.prblist.masters.datasource');
		Route::post('probable-list/degree/datasource', 'ProbableListController@degree_datasource')->name('student.prblist.degree.datasource');
		Route::post('probable-list/hsc/datasource', 'ProbableListController@hsc_datasource')->name('student.prblist.hsc.datasource');
		// edit
		Route::get('probable-list/honours/edit/{auto_id}', 'ProbableListController@honours_edit')->name('student.prblist.honours.edit');
		Route::get('probable-list/masters/edit/{auto_id}', 'ProbableListController@masters_edit')->name('student.prblist.masters.edit');
		Route::get('probable-list/degree/edit/{auto_id}', 'ProbableListController@degree_edit')->name('student.prblist.degree.edit');
		Route::get('probable-list/hsc/edit/{auto_id}', 'ProbableListController@hsc_edit')->name('student.prblist.hsc.edit');
		// store/update
		Route::post('probable-list/honours/store', 'ProbableListController@honours_store')->name('student.prblist.honours.store');
		Route::post('probable-list/masters/store', 'ProbableListController@masters_store')->name('student.prblist.masters.store');
		Route::post('probable-list/degree/store', 'ProbableListController@degree_store')->name('student.prblist.degree.store');
		Route::post('probable-list/hsc/store', 'ProbableListController@hsc_store')->name('student.prblist.hsc.store');
		// essentials
		Route::get('probable-list/upload', 'ProbableListController@upload')->name('student.prblist.upload');
		Route::post('probable-list/upload/exe', 'ProbableListController@upload_exe')->name('student.prblist.upload.exe');
		Route::delete('probable-list/destroy', 'ProbableListController@destroy')->name('student.prblist.destroy');
	});
	
	// StudentApplicationController
	Route::group(['before' => 'auth','prefix' => 'application'],function() {

		//honours app report
		Route::any('/honours', ['as' => 'student.application.honours', 'uses' => 'StudentApplicationController@honoursapplication']);
		Route::any('/honappsearch', ['as' => 'formfillup.honosearch', 'uses' => 'StudentApplicationController@HonoSearch']);
		Route::any('/honappreport', ['as' => 'student.application.honappreport', 'uses' => 'StudentApplicationController@generateHonAppReport']);

		// degree app report
		Route::any('/degree', ['as' => 'student.application.degree', 'uses' => 'StudentApplicationController@degreeapplication']);
		Route::any('/degappindex', ['as' => 'degformfillup.degffindex', 'uses' => 'StudentApplicationController@Degffindex']);
		Route::any('/degappsearch', ['as' => 'formfillup.degffsearch', 'uses' => 'StudentApplicationController@DegSearch']);

		Route::any('/degappreport', ['as' => 'student.application.degreeAppreport', 'uses' => 'StudentApplicationController@generateDegAppReport']);

		// master app report
		Route::any('/masters', ['as' => 'student.application.masters', 'uses' => 'StudentApplicationController@mastersapplication']);
		Route::any('/mastersappreport', ['as' => 'student.application.mastersAppreport', 'uses' => 'StudentApplicationController@generateMastersAppReport']);
	});

	Route::get('/student/applicaton/download/{type}/{id}', ['as'=> 'student.applicaton.download', 'uses'=>'StudentApplicationController@getApplicationDownload']);


});

Route::group(['before' => 'auth', 'prefix' => 'students/hsc','namespace'=>'Student', 'middleware' => 'auth'],function() {

	Route::get('/', 'HscController@index')->name('student.hsc');
	Route::get('/new', 'HscController@create')->name('students.hsc.create');
	Route::post('/store', 'HscController@store')->name('students.hsc.store');
	Route::get('/edit/{id}', 'HscController@edit')->name('students.hsc.edit');
	Route::delete('/destroy/{id}', 'HscController@destroy')->name('students.hsc.destroy');
	Route::post('/force-promotion', 'HscController@force_promotion')->name('students.hsc.force_promotion');
	Route::post('/datasource', 'HscController@datasource')->name('students.hsc.datasource');
    Route::get('/print/{id}', ['as' => 'students.hsc.print', 'uses' => 'HscController@printDetails']);
		
		Route::any('/hscTcStudents', ['as' => 'students.hsc.hsctcstudents', 'uses' => 'HscController@hscTcStudents']);
		
		Route::any('/hscTcStudentSearch', ['as' => 'students.hsc.hsctcstudentsearch', 'uses' => 'HscController@hscTcStudentSearch']);


	Route::post('tc_student_pdf',['as'=>'students.hsc.tc_student_pdf','uses'=>'HscController@tcStudentPdf']); 
	   
	    Route::any('/upload', ['as' => 'students.hsc.student.upload', 'uses' => 'HscController@Upload']);



	   Route::any('/hscformat', ['as' => 'students.hsc.format', 'uses' => 'HscController@formatDownload']);

	   Route::any('/uploaded', ['as' => 'students.hsc.upload.ext', 'uses' => 'HscController@postUpload']);

	    Route::any('/totlist', ['as' => 'students.hsc.totlist', 'uses' => 'HscController@totlistSelect']);

	    Route::post('/totlistgenerate', ['as' => 'students.hsc.generate', 'uses' => 'HscController@totlistGenerate']);
	    Route::get('/tot/{name}', ['as' => 'students.hsc.tot.download', 'uses' => 'HscController@totlistDownload']);

	    Route::any('/regstudent', ['as' => 'students.hsc.regstudent', 'uses' => 'HscController@regStudent']);
 		Route::any('/hsc_bulk_report_input_action', ['as' => 'students.hsc.bulk_report', 'uses' => 'HscController@bulk_report']); 

/*Route::post('/editdata',function(){

                   if(Request::ajax()){	
                   //$groups=Input::get('groups');
	               echo json_encode("hello");	
}
});*/

	   
});

Route::group(['namespace'=>'Student', 'middleware' => 'auth', 'prefix' => 'students/idcard'],function() {
	Route::any('/', ['as' => 'students.idcard', 'uses' => 'IDCardController@index']);
	Route::any('category_details', ['as' => 'students.idcard.category_details', 'uses' => 'IDCardController@categoryDetails']);

	Route::any('dep_select_faculty', ['as' => 'students.idcard.dep_select_faculty', 'uses' => 'IDCardController@depSelectFaculty']);

	Route::any('id_card_generate', ['as' => 'students.idcard.id_card_generate', 'uses' => 'IDCardController@idCardGenerate']);	

	Route::any('id_card_generate_multi', ['as' => 'students.idcard.id_card_generate_multi', 'uses' => 'IDCardController@idCardGenerateMulti']);	

});

// Degree Controller
Route::group(['prefix' => 'students/degree','namespace'=>'Student', 'middleware' => 'auth'],function() {


	Route::get('/', 'DegreeController@index')->name('students.degree');
	Route::get('/new', 'DegreeController@create')->name('students.degree.create');
	Route::post('/store', 'DegreeController@store')->name('students.degree.store');
	Route::get('/edit/{id}', 'DegreeController@edit')->name('students.degree.edit');
	Route::delete('/destroy/{id}', 'DegreeController@destroy')->name('students.degree.destroy');
	Route::post('/force-promotion', 'DegreeController@force_promotion')->name('students.degree.force_promotion');
	Route::post('/datasource', 'DegreeController@datasource')->name('students.degree.datasource');
    Route::get('/print/{id}', ['as' => 'students.degree.print', 'uses' => 'DegreeController@printDetails']);

	Route::any('/upload', ['as' => 'students.degree.student.upload', 'uses' => 'DegreeController@Upload']);
	Route::any('/regstudent', ['as' => 'students.degree.regstudent', 'uses' => 'DegreeController@regStudent']);
	Route::any('/regsearch', ['as' => 'students.degree.regsearch', 'uses' => 'DegreeController@regSearch']);
			Route::any('/uploaded', ['as' => 'students.degree.upload.ext', 'uses' => 'DegreeController@postUpload']);   
	Route::any('/degformat', ['as' => 'students.degree.format', 'uses' => 'DegreeController@formatDownload']);
	Route::any('/degree_bulk_report_input_action', ['as' => 'students.degree.bulk_report', 'uses' => 'DegreeController@bulk_report']); 


});

// Subject Management Routes
Route::group(['prefix' => 'students', 'namespace'=>'Student', 'middleware' => 'auth'],function() {

	// Course Subject Routes
	Route::get('/course-subject', 'CourseSubjectController@index')->name('students.course-subject.index');
	Route::get('/course-subject/create', 'CourseSubjectController@create')->name('students.course-subject.create');
	Route::post('/course-subject/store', 'CourseSubjectController@store')->name('students.course-subject.store');
	Route::get('/course-subject/edit/{id}', 'CourseSubjectController@edit')->name('students.course-subject.edit');
	Route::put('/course-subject/update/{id}', 'CourseSubjectController@update')->name('students.course-subject.update');
	Route::delete('/course-subject/destroy/{id}', 'CourseSubjectController@destroy')->name('students.course-subject.destroy');
	Route::get('/course-subject/datasource', 'CourseSubjectController@datasource')->name('students.course-subject.datasource');

	// Subject Combination Routes
	Route::get('/combination', 'SubjectCombinationController@index')->name('students.combination.index');
	Route::get('/combination/create', 'SubjectCombinationController@create')->name('students.combination.create');
	Route::post('/combination/store', 'SubjectCombinationController@store')->name('students.combination.store');
	Route::get('/combination/edit/{id}', 'SubjectCombinationController@edit')->name('students.combination.edit');
	Route::put('/combination/update/{id}', 'SubjectCombinationController@update')->name('students.combination.update');
	Route::delete('/combination/destroy/{id}', 'SubjectCombinationController@destroy')->name('students.combination.destroy');
	Route::get('/combination/datasource', 'SubjectCombinationController@datasource')->name('students.combination.datasource');

});

// Masters Controller
Route::group(['prefix' => 'students/masters','namespace'=>'Student', 'middleware' => 'auth'],function() {

	Route::get('/', 'MastersController@index')->name('students.masters');
	Route::get('/new', 'MastersController@create')->name('students.masters.create');
	Route::post('/store', 'MastersController@store')->name('students.masters.store');
	Route::get('/edit/{id}', 'MastersController@edit')->name('students.masters.edit');
	Route::delete('/destroy/{id}', 'MastersController@destroy')->name('students.masters.destroy');
	Route::post('/force-promotion', 'MastersController@force_promotion')->name('students.masters.force_promotion');
	Route::post('/datasource', 'MastersController@datasource')->name('students.masters.datasource');
    Route::get('/print/{id}', ['as' => 'students.masters.print', 'uses' => 'MastersController@printDetails']);
	Route::any('/regstudent', ['as' => 'students.masters.regstudent', 'uses' => 'MastersController@regStudent']);	
	Route::any('/regsearch', ['as' => 'students.masters.regsearch', 'uses' => 'MastersController@regSearch']); 

	Route::any('/masters_bulk_report_input_action', ['as' => 'students.masters.bulk_report', 'uses' => 'MastersController@bulk_report']); 
	Route::post('/admission/mscreport', ['as' => 'students.admission.mscadmreport', 'uses' => 'MastersController@generateMscAdmReport']);
});

//Honours Controller
Route::group(['prefix' => 'students/honours','namespace'=>'Student', 'middleware' => 'auth'],function() {
	Route::get('/', 'HonoursController@index')->name('students.honours');
	Route::get('/new', 'HonoursController@create')->name('students.honours.create');
	Route::post('/store', 'HonoursController@store')->name('students.honours.store');
	Route::get('/edit/{id}', 'HonoursController@edit')->name('students.honours.edit');
	Route::delete('/destroy/{id}', 'HonoursController@destroy')->name('students.honours.destroy');
	Route::post('/force-promotion', 'HonoursController@force_promotion')->name('students.honours.force_promotion');
	Route::post('/datasource', 'HonoursController@datasource')->name('students.honours.datasource');
    Route::get('/print/{id}', ['as' => 'students.honours.print', 'uses' => 'HonoursController@printDetails']);

    Route::any('/regstudent', ['as' => 'students.honours.regstudent', 'uses' => 'HonoursController@regStudent']);
    Route::any('/regsearch', ['as' => 'students.honours.regsearch', 'uses' => 'HonoursController@regSearch']);    

    Route::any('/honours_bulk_report_input_action', ['as' => 'students.honours.bulk_report', 'uses' => 'HonoursController@bulk_report']);
    
    Route::post('/admission/honreport', ['as' => 'students.admission.honadmreport', 'uses' => 'HonoursController@generateHonAdmReport']);
});



Route::group(['prefix' => 'students/migration','namespace'=>'Student', 'middleware' => 'auth'],function() {
	Route::any('/', ['as' => 'students.migration', 'uses' => 'MigrationController@migration']);

	Route::any('/migrationList', ['as' => 'students.migration.list', 'uses' => 'MigrationController@migratedStudentList']);

	Route::any('/migrationListSearch', ['as' => 'students.migration.search', 'uses' => 'MigrationController@migratedStudentListSearch']);

	Route::any('/migrationListupload', ['as' => 'students.migration.list.upload', 'uses' => 'MigrationController@migrationStudentListUpload']);
	Route::any('/singleDelete', ['as' => 'students.migration.list.single.delete', 'uses' => 'MigrationController@migrationStudentSingleDelete']);
	Route::any('/edit/{id}', ['as' => 'students.migration.list.edit', 'uses' => 'MigrationController@migrationStudentEdit']);
	Route::any('/edited', ['as' => 'students.migration.list.edited', 'uses' => 'MigrationController@migrationStudentEditComplete']);
	Route::any('/migrationTable', ['as' => 'students.migration.table', 'uses' => 'MigrationController@migrationTable']);
	Route::any('/migrationCsvUpload', ['as' => 'students.migration.list.csv.upload', 'uses' => 'MigrationController@migrationTableCsvUpload']);
	Route::any('/migrationformat', ['as' => 'students.migration.format', 'uses' => 'MigrationController@formatDownload']);
	Route::any('/migrationCsvUploadExe', ['as' => 'students.migration.migrationlistupload', 'uses' => 'MigrationController@migrationCsvUpload']);
	Route::any('/migrationExe', ['as' => 'students.migration.exe', 'uses' => 'MigrationController@migrationExe']);
});

// Report url
Route::group(['namespace'=>'Student', 'middleware' => 'auth', 'prefix' => 'students/report'],function() {
	// hsc admission
	Route::get('hsc', ['as' => 'students.report.hsc', 'uses' => 'ReportController@hsc_report']);
	Route::any('hsc/admission', ['as' => 'report.hsc.admission', 'uses' => 'ReportController@hsc_admission']);
	Route::post('hsc/admission/generate', ['as' => 'report.hsc.admission.generate', 'uses' => 'ReportController@generateHscAdmReport']);
	Route::any('hsc2nd/admission', ['as' => 'report.hsc2nd.admission', 'uses' => 'ReportController@hsc2nd_admission']);
	Route::post('hsc2nd/admission/generate', ['as' => 'report.hsc2nd.admission.generate', 'uses' => 'ReportController@generateHsc2ndAdmReport']);

	// honours
	Route::get('/honours', ['as' => 'students.report.honours', 'uses' => 'ReportController@honours_report']);

	// masters
	Route::get('/masters', ['as' => 'students.report.masters', 'uses' => 'ReportController@masters_report']);
		// admission
	Route::any('/masters/admission', ['as' => 'report.masters.admission', 'uses' => 'ReportController@masters_admission']);
	Route::post('/masters/admission/generate', ['as' => 'report.masters.admission.generate', 'uses' => 'ReportController@generateMscAdmReport']);
	// form fillup
	Route::any('/masters/form_fillup', ['as' => 'report.masters.ff', 'uses' => 'FormfillupReportController@masters_form_fillup']);
	Route::any('/masters/form_fillup/generate', ['as' => 'report.masters.ff.generate', 'uses' => 'FormfillupReportController@generateMscFFReport']);

	// masters
	Route::get('/degree', ['as' => 'students.report.degree', 'uses' => 'ReportController@degree_report']);
		// admission
	Route::any('/degree/admission', ['as' => 'report.degree.admission', 'uses' => 'ReportController@degree_admission']);
	Route::post('/degree/admission/generate', ['as' => 'report.degree.admission.generate', 'uses' => 'ReportController@generateDegAdmReport']);
		// applicaton
	Route::any('/degree/application', ['as' => 'report.degree.application', 'uses' => 'ReportController@degree_application']);
	Route::post('/degree/application/generate', ['as' => 'report.degree.application.generate', 'uses' => 'ReportController@generateDegAppReport']);

});

// Report url
Route::group(['namespace'=>'Student\Report', 'middleware' => 'auth', 'prefix' => 'students/report'],function() {
	// hsc formfillup
	Route::any('/hsc/formfillup', ['as' => 'report.hsc.ff', 'uses' => 'HSCReportController@hsc_form_fillup']);
	Route::post('/hsc/form_fillup/generate', ['as' => 'report.hsc.ff.generate', 'uses' => 'HSCReportController@generateHSCFFReport']);

	// honours formfillup
	Route::any('/honours/formfillup', ['as' => 'report.honours.ff', 'uses' => 'HonoursReportController@honours_form_fillup']);
	Route::post('/honours/form_fillup/generate', ['as' => 'report.honours.ff.generate', 'uses' => 'HonoursReportController@generateHonsFFReport']);

	// degree formfillup
	Route::any('/degree/formfillup', ['as' => 'report.degree.ff', 'uses' => 'DegreeReportController@degree_form_fillup']);
	Route::post('/degree/form_fillup/generate', ['as' => 'report.degree.ff.generate', 'uses' => 'DegreeReportController@generateDegFFReport']);

	// honours admission
	Route::any('/honours/admission', ['as' => 'report.honours.admission', 'uses' => 'HonoursReportController@honours_admission']);
	Route::post('/honours/admission/generate', ['as' => 'report.honours.admission.generate', 'uses' => 'HonoursReportController@generateHonAdmReport']);

	// applicaton
	Route::any('/honours/application', ['as' => 'report.honours.application', 'uses' => 'HonoursReportController@honours_application']);
	Route::post('/honours/application/generate', ['as' => 'report.honours.application.generate', 'uses' => 'HonoursReportController@generateHonAppReport']);

	// masters admission
	Route::any('/masters/admission', ['as' => 'report.masters.admission', 'uses' => 'MastersReportController@masters_admission']);
	Route::post('/masters/admission/generate', ['as' => 'report.masters.admission.generate', 'uses' => 'MastersReportController@generateMscAdmReport']);

	// applicaton
	Route::any('/masters/application', ['as' => 'report.masters.application', 'uses' => 'MastersReportController@masters_application']);
	Route::post('/masters/application/generate', ['as' => 'report.masters.application.generate', 'uses' => 'MastersReportController@generateMscAppReport']);
});

Route::get('download/csv/format', 'EcmController@download_csv_format')->name('download.csv.format');
Route::post('truncate/table', 'EcmController@truncate_table')->name('truncate.table');

Route::get('download/csv/format', 'EcmController@download_csv_format')->name('download.csv.format');
Route::post('truncate/table', 'EcmController@truncate_table')->name('truncate.table');

Route::group(['namespace'=>'Student\Attendance', 'middleware' => 'auth', 'prefix' => 'students'],function() {
	Route::get('attendance-list', 'StudentAttendanceController@index')->name('students.attendance-list');
	Route::post('attendance-list-datasource', 'StudentAttendanceController@datasource')->name('students.attendance-list-datasource');
	Route::get('attendance-settings', 'AttendanceSettingController@setting')->name('students.attendance-setting');
	Route::post('attendance-settings', 'AttendanceSettingController@settingPost')->name('students.attendance-setting');
	Route::get('send-attendance-sms', 'StudentAttendanceController@sentSMSView')->name('students.send-attendance-sms');
	Route::post('send-attendance-sms', 'StudentAttendanceController@sentSMSPost')->name('students.send-attendance-sms');
	
	Route::get('attendance-sms-log', 'StudentAttendanceController@smsLog')->name('students.attendance-sms-log');
	Route::post('attendance-sms-log-datasource', 'StudentAttendanceController@smsLogDatasource')->name('students.attendance-sms-log-datasource');



});

Route::group(['as'=> 'student.','namespace'=>'Student\Report', 'middleware' => 'auth', 'prefix' => 'students/fees-payment', ''],function() {
	Route::get('/report', 'FeesPaymentReportController@index')->name('fees-payment.report');
	Route::get('/fees-payment-report/data', 'FeesPaymentReportController@getData')->name('fees-payment-report.data');
    Route::get('/fees-payment-report/details', 'FeesPaymentReportController@getDetails')->name('fees-payment-report.details');
    Route::get('/fees-payment-report/summary', 'FeesPaymentReportController@getSummary')->name('fees-payment-report.summary');
    Route::get('/fees-payment-report/export', 'FeesPaymentReportController@export')->name('fees-payment-report.export');
});


Route::get('students/assign-absent-student-send-sms', 'StudentAttendanceController@sentAbsentStudentSMS');