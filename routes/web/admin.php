<?php
use Illuminate\Support\Facades\Route;

// start invoice generate
Route::group(['namespace'=>'Admin', 'middleware' => 'auth'], function() {
	Route::post('invoice_generate',['as' => 'invoice.generate', 'uses' => 'AdminInvoiceController@generate_invoice']);
	Route::get('invoice_generate_promotion_hsc',['as' => 'invoice.generate.promotion.hsc', 'uses' => 'AdminInvoiceController@generate_invoice_2nd_promotion_hsc']);
	Route::get('invoice_generate_others_fee',['as' => 'invoice.others.fee', 'uses' => 'AdminInvoiceController@generate_invoice_others_fee_hsc']);

	// degree formfillup invoice generate
	Route::get('invoice_generate_degree_formfillup',['as' => 'invoice.generate.formfillup.degree', 'uses' => 'AdminInvoiceController@invoice_generate_degree_formfillup']);

	// masters formfillup invoice generate
	Route::get('invoice_generate_masters_formfillup',['as' => 'invoice.generate.formfillup.masters', 'uses' => 'AdminInvoiceController@invoice_generate_masters_formfillup']);

	// honours formfillup invoice generate
	Route::get('invoice_generate_honours_formfillup',['as' => 'invoice.generate.formfillup.honours', 'uses' => 'AdminInvoiceController@invoice_generate_honours_formfillup']);

	// hsc2nd admission invoice generate
	Route::get('invoice_generate_hsc2nd_adm',['as' => 'invoice.generate.admission.hsc2nd', 'uses' => 'AdminInvoiceController@invoice_generate_hsc2nd_adm']);

	Route::get('invoice_generate_hsc_admission',['as' => 'invoice.generate.admission.hsc', 'uses' => 'AdminInvoiceController@invoice_generate_hsc_admission']);

	// honours admission invoice generate
	Route::get('invoice_generate_honours_admission',['as' => 'invoice.generate.admission.honours', 'uses' => 'AdminInvoiceController@invoice_generate_honours_admission']);

	// masters admission invoice generate
	Route::get('invoice_generate_masters_admission',['as' => 'invoice.generate.admission.masters', 'uses' => 'AdminInvoiceController@invoice_generate_masters_admission']);
	
	Route::get('invoice_generate_masters1st_admission',['as' => 'invoice.generate.admission.masters1st', 'uses' => 'AdminInvoiceController@invoice_generate_masters1st_admission']);

	// degree admission invoice generate
	Route::get('invoice_generate_degree_admission',['as' => 'invoice.generate.admission.degree', 'uses' => 'AdminInvoiceController@invoice_generate_degree_admission']);
});
// end invoice generate

Route::group(['prefix' => 'admin', 'namespace'=>'Admin', 'as'=> 'admin.', 'middleware' => 'auth'], function() {
	Route::resource('college', 'AdminCollegeController');
	Route::put('college-status/{id}', ['as' => 'college.status', 'uses' => 'AdminCollegeController@status']);
	Route::resource('payslip_header', 'AdminPayslipHeaderController');
	Route::post('payslip_header/datasource', 'AdminPayslipHeaderController@datasource')->name('payslip_header.datasource');
	Route::get('payslip_header/field/{header_id}', 'AdminPayslipHeaderController@fields')->name('payslip_header.field');
	Route::resource('requirement', 'AdminRequirementController');
	Route::resource('payslip_item', 'AdminPayslipItemController');
	Route::resource('payslip_title', 'AdminPayslipTitleController');
	Route::resource('payslip_generator', 'AdminPayslipGeneratorController');
	Route::put('payslip_generator-status/{id}', ['as' => 'payslip_generator.status', 'uses' => 'AdminPayslipGeneratorController@status']);
	Route::any('search/payslip_generator', ['as' => 'payslip_generator.search', 'uses' => 'AdminPayslipGeneratorController@search']);
	Route::resource('permission', 'AdminPermissionController', ['only' => ['index', 'store']]);
	Route::post('permission/datasource', 'AdminPermissionController@datasource')->name('permission.datasource');
	Route::resource('role', 'AdminRoleController');
	Route::resource('permission', 'AdminPermissionController');
	Route::resource('user', 'AdminUserController');
	Route::resource('program', 'AdminProgramController');
	Route::resource('dept', 'AdminDeptController');
	Route::resource('dept_head', 'AdminDeptHeadController');
	Route::resource('course', 'AdminCourseController');
	Route::resource('course_teacher', 'AdminCourseTeacherController');
	Route::resource('user', 'AdminUserController');
	Route::resource('faculty', 'AdminFacultyController');
	Route::resource('fac_head', 'AdminFacHeadController');
	Route::resource('dept', 'AdminDeptController');
	Route::resource('dept_head', 'AdminDeptHeadController');
	Route::resource('circulation', 'AdminCirculationController');
	Route::resource('subject', 'AdminSubjectController');
	Route::any('search/payslip_item', ['as' => 'payslip_item.search', 'uses' => 'AdminPayslipItemController@search']);
	Route::any('id_roll', 'AdminIdRollController@create')->name('id_roll.create');
	Route::any('id_roll/{id}/edit', 'AdminIdRollController@edit')->name('id_roll.edit');
	Route::any('id_roll/new', 'AdminIdRollController@new')->name('id_roll.new');
	Route::any('id_roll/new/store', 'AdminIdRollController@new_store')->name('id_roll.new_store');

	Route::put('user-status/{id}', ['as' => 'user.status', 'uses' => 'AdminUserController@status']);
	Route::put('user-reset-password/{id}', ['as' => 'user.reset.post', 'uses' => 'AdminUserController@postReset']);
	Route::get('user-reset-password/{id}', ['as' => 'user.reset', 'uses' => 'AdminUserController@reset']);


// admission configuration
	Route::get('formfillup/config', ['as' => 'formfillup.config', 'uses' => 'AdminFormfillupController@formfillup_config']);
	Route::post('formfillup/config/datasource', ['as' => 'formfillup.config.datasource', 'uses' => 'AdminFormfillupController@formfillup_config_datasource']);
	Route::get('formfillup/config/edit/{id}', ['as' => 'formfillup.config.edit', 'uses' => 'AdminFormfillupController@formfillup_config_edit']);
	Route::delete('formfillup/config/destroy/{id}', ['as' => 'formfillup.config.destroy', 'uses' => 'AdminFormfillupController@formfillup_config_destroy']);
	Route::post('formfillup/config/store', ['as' => 'formfillup.config.store', 'uses' => 'AdminFormfillupController@formfillup_config_store']);

	// formfillup configuration
	Route::get('admission/config', ['as' => 'admission.config', 'uses' => 'AdminAdmissionController@admission_config']);
	Route::post('admission/config/datasource', ['as' => 'admission.config.datasource', 'uses' => 'AdminAdmissionController@admission_config_datasource']);
	Route::get('admission/config/edit/{id}', ['as' => 'admission.config.edit', 'uses' => 'AdminAdmissionController@admission_config_edit']);
	Route::delete('admission/config/destroy/{id}', ['as' => 'admission.config.destroy', 'uses' => 'AdminAdmissionController@admission_config_destroy']);
	Route::post('admission/config/store', ['as' => 'admission.config.store', 'uses' => 'AdminAdmissionController@admission_config_store']);
	
	Route::resource('admission', 'AdminAdmissionController');

	// end admission configuration

	// start formfillup config

	Route::any('formfillup/hsc', ['as' => 'formfillup.hsc', 'uses' => 'AdminFormfillupController@hscIndex']);
    Route::any('formfillup/hsc/create', ['as' => 'formfillup.hsc.create', 'uses' => 'AdminFormfillupController@hscCreate']);
    Route::any('formfillup/hsc/store', ['as' => 'formfillup.hsc.store', 'uses' => 'AdminFormfillupController@hscStore']);
    Route::any('formfillup/hsc/update/{id}', ['as' => 'formfillup.hsc.update', 'uses' => 'AdminFormfillupController@hscUpdate']);
    Route::any('formfillup/hsc/edit/{id}', ['as' => 'formfillup.hsc.edit', 'uses' => 'AdminFormfillupController@hscEdit']);
    Route::any('formfillup/hsc/destroy/{id}', ['as' => 'formfillup.hsc.destroy', 'uses' => 'AdminFormfillupController@hscDestroy']);
    
    Route::any('formfillup/honours', ['as' => 'formfillup.honours', 'uses' => 'AdminFormfillupController@honIndex']);
    Route::any('formfillup/honours/create', ['as' => 'formfillup.honours.create', 'uses' => 'AdminFormfillupController@honCreate']);
    Route::any('formfillup/honours/store', ['as' => 'formfillup.honours.store', 'uses' => 'AdminFormfillupController@honStore']);
    Route::any('formfillup/honours/update/{id}', ['as' => 'formfillup.honours.update', 'uses' => 'AdminFormfillupController@honUpdate']);
    Route::any('formfillup/honours/edit/{id}', ['as' => 'formfillup.honours.edit', 'uses' => 'AdminFormfillupController@honEdit']);
    Route::any('formfillup/honours/destroy/{id}', ['as' => 'formfillup.honours.destroy', 'uses' => 'AdminFormfillupController@honDestroy']);



    Route::any('formfillup/masters', ['as' => 'formfillup.masters', 'uses' => 'AdminFormfillupController@masIndex']);
    Route::any('formfillup/masters/create', ['as' => 'formfillup.masters.create', 'uses' => 'AdminFormfillupController@masCreate']);
    Route::any('formfillup/masters/store', ['as' => 'formfillup.masters.store', 'uses' => 'AdminFormfillupController@masStore']);
    Route::any('formfillup/masters/update/{id}', ['as' => 'formfillup.masters.update', 'uses' => 'AdminFormfillupController@masUpdate']);
    Route::any('formfillup/masters/edit/{id}', ['as' => 'formfillup.masters.edit', 'uses' => 'AdminFormfillupController@masEdit']);
    Route::any('formfillup/masters/destroy/{id}', ['as' => 'formfillup.masters.destroy', 'uses' => 'AdminFormfillupController@masDestroy']);



    Route::any('formfillup/degree', ['as' => 'formfillup.degree', 'uses' => 'AdminFormfillupController@degIndex']);
    Route::any('formfillup/degree/create', ['as' => 'formfillup.degree.create', 'uses' => 'AdminFormfillupController@degCreate']);
    Route::any('formfillup/degree/store', ['as' => 'formfillup.degree.store', 'uses' => 'AdminFormfillupController@degStore']);
    Route::any('formfillup/degree/update/{id}', ['as' => 'formfillup.degree.update', 'uses' => 'AdminFormfillupController@degUpdate']);
    Route::any('formfillup/degree/edit/{id}', ['as' => 'formfillup.degree.edit', 'uses' => 'AdminFormfillupController@degEdit']);
    Route::any('formfillup/degree/destroy/{id}', ['as' => 'formfillup.degree.destroy', 'uses' => 'AdminFormfillupController@degDestroy']);
	Route::resource('formfillup', 'AdminFormfillupController');

	// end formfillup config

	// start new student controller
	Route::any('newstudent/index', ['as' => 'newstudent.index', 'uses' => 'AdminNewStudentController@index']);
  	Route::any('newstudent/hscnewstudent', ['as' => 'newstudent.hscnewstudent', 'uses' => 'AdminNewStudentController@hscnewstudent']);
  	Route::post('newstudent/hscGroupChange', ['as' => 'newstudent.hscGroupChange', 'uses' => 'AdminNewStudentController@hscGroupChange']);
   	Route::any('newstudent/hscsubmit', ['as' => 'newstudent.hscsubmit', 'uses' => 'AdminNewStudentController@hscsubmit']);

  	Route::any('newstudent/honnewstudent', ['as' => 'newstudent.honnewstudent', 'uses' => 'AdminNewStudentController@honnewstudent']); 
   	Route::any('newstudent/honSubmit', ['as' => 'newstudent.honSubmit', 'uses' => 'AdminNewStudentController@honSubmit']);

   	Route::any('newstudent/masnewstudent', ['as' => 'newstudent.masnewstudent', 'uses' => 'AdminNewStudentController@masnewstudent']);
  	Route::any('newstudent/masSubmit', ['as' => 'newstudent.masSubmit', 'uses' => 'AdminNewStudentController@masSubmit']);

  	Route::any('newstudent/degnewstudent', ['as' => 'newstudent.degnewstudent', 'uses' => 'AdminNewStudentController@degnewstudent']); 
  	Route::any('newstudent/degSubmit', ['as' => 'newstudent.degSubmit', 'uses' => 'AdminNewStudentController@degSubmit']);

});

//Students Management
Route::group(['prefix' => 'admin', 'namespace'=>'Admin','as'=> 'admin.', 'middleware' => 'auth'],function() {
        Route::resource('invoice', 'InvoiceManageController');
        Route::post('invoice/datasource', 'InvoiceManageController@datasource')->name('invoice.datasource');
        Route::post('invoice/delete-all', 'InvoiceManageController@deleteAll')->name('invoice.delete.all');
});
