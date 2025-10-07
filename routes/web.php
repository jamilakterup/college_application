<?php

use Illuminate\Support\Facades\Route;

foreach (glob(__DIR__ . '/web/*') as $router_files) {
    (basename($router_files == 'web.php')) ?: (require_once $router_files);
}

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('cache:clear', function () {
    \Artisan::call('config:cache');
    \Artisan::call('cache:clear');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');
    \Artisan::call('optimize');
});



// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return view('dashboard');
// })->name('dashboard');

// Ajax Controller
Route::any('/faculty_department/dropdown', ['as' => 'student.fact_dept.dropdown', 'uses' => 'AjaxController@faculty_department_dropdown']);


//Students Routes
Route::group(['prefix' => 'Admission/HSC', 'namespace' => 'Admission\HSC'], function () {

    Route::any('/', ['as' => 'student.hsc.admission', 'uses' => 'HSCAdmissionController@index']);
    Route::post('/checkMerit', ['as' => 'student.hsc.meritCheck', 'uses' => 'HSCAdmissionController@checkMerit']);
    Route::any('/Form', ['as' => 'student.hsc.admission.form', 'uses' => 'HSCAdmissionController@admissionForm']);
    Route::any('/districtCh', ['as' => 'student.hsc.admission.dist', 'uses' => 'HSCAdmissionController@districtChange']);

    Route::any('/hscInformationSubmit', ['as' => 'student.hsc.admission.hscInformationSubmit', 'uses' => 'HSCAdmissionController@hscInformationSubmit']);


    Route::any('/signin', ['as' => 'student.hsc.admission.signin', 'uses' => 'HSCAdmissionController@hscSignin']);

    Route::post('/retrievepass', ['as' => 'student.hsc.admission.retrievepass', 'uses' => 'HSCAdmissionController@retrievepass']);

    Route::post('/hscStudentSignin', ['as' => 'student.hsc.admission.hscStudentSignin', 'uses' => 'HSCAdmissionController@hscStudentSignin']);

    Route::any('/HscConfirmation', ['as' => 'student.hsc.admission.HscConfirmation', 'uses' => 'HSCAdmissionController@HscConfirmation']);

    Route::post('/formId', ['as' => 'student.hsc.admission.formId', 'uses' => 'HSCAdmissionController@downloadHscForm']);
    Route::get('/formIdtest', ['as' => 'student.hsc.admission.formId', 'uses' => 'HSCAdmissionController@downloadHscFormtest']);

    Route::post('/tidId', ['as' => 'student.hsc.admission.tidId', 'uses' => 'HSCAdmissionController@downloadHscIdCard']);

    Route::post('/slipId', ['as' => 'student.hsc.admission.slipId', 'uses' => 'HSCAdmissionController@downloadSlipId']);

    Route::post('/slipCommitment', ['as' => 'student.hsc.admission.slipCommitment', 'uses' => 'HSCAdmissionController@downloadSlipCommitment']);

    Route::post('/SubjectCodeSequence', ['as' => 'student.hsc.admission.SubjectCodeSequence', 'uses' => 'HSCAdmissionController@SubjectCodeSequence']);

    Route::any('/admissionFee', ['as' => 'student.hsc.admission.admissionFee', 'uses' => 'HSCAdmissionController@admissionFee']);
    Route::any('/dutchbangla', ['as' => 'student.hsc.admission.dutchbangla', 'uses' => 'HSCAdmissionController@dutchbangla']);

    Route::any('/hscimagedownload', ['as' => 'student.hsc.admission.hscimagedownload', 'uses' => 'HSCAdmissionController@hscimagedownload']);
    Route::post('/hscGroupChange', ['as' => 'student.hsc.admission.hscGroupChange', 'uses' => 'HSCAdmissionController@hscGroupChange']);

    Route::any('/logout', ['as' => 'student.hsc.admission.logout', 'uses' => 'HSCAdmissionController@admisionLogout']);
    Route::get('/editForm', ['as' => 'student.hsc.admission.editForm', 'uses' => 'HSCAdmissionController@editForm']);
    Route::post('/updateForm', ['as' => 'student.hsc.admission.updateForm', 'uses' => 'HSCAdmissionController@updateForm']);
});
Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

// Hsc 2nd year promotion
Route::group(['prefix' => 'Hsc/promotion'], function () {
    Route::any('/', ['as' => 'hsc.promotion', 'uses' => 'HscPromotionController@index']);

    Route::any('/check', ['as' => 'hsc.student.promotion.check', 'uses' => 'HscPromotionController@check']);

    Route::any('/view', ['as' => 'hsc.student.promotion.view', 'uses' => 'HscPromotionController@view']);

    Route::any('/dbbl_view', ['as' => 'hsc.student.promotion.dbbl_view', 'uses' => 'HscPromotionController@dbblPageView']);
    Route::any('/paytype', ['as' => 'hsc.student.promotion.paytype', 'uses' => 'HscPromotionController@payType']);
    Route::any('/checktype', ['as' => 'hsc.student.promotion.checktype', 'uses' => 'HscPromotionController@checktype']);

    Route::any('/logout', ['as' => 'hsc.student.promotion.logout', 'uses' => 'HscPromotionController@promotionLogout']);

    Route::any('/dbbl_approve', ['as' => 'hsc.student.promotion.dbbl.approve', 'uses' => 'HscPromotionController@dbblApprove']);

    Route::any('/confirmslip', ['as' => 'hsc.student.promotion.view', 'uses' => 'HscPromotionController@createConfirmSlip']);
});

// Degree Form Fillup
Route::group(['prefix' => 'Degree/formfillup'], function () {

    Route::any('/', ['as' => 'degree.student.formfillup', 'uses' => 'DegreeFormfillupController@index']);
    Route::any('/check', ['as' => 'degree.student.formfillup.check', 'uses' => 'DegreeFormfillupController@check']);
    Route::any('/checktype', ['as' => 'degree.student.formfillup.checktype', 'uses' => 'DegreeFormfillupController@checktype']);
    Route::any('/view', ['as' => 'degree.student.formfillup.view', 'uses' => 'DegreeFormfillupController@view']);
    Route::any('/next_step', ['as' => 'degree.student.formfillup.next_step', 'uses' => 'DegreeFormfillupController@nextStep']);
    Route::any('/dbbl_view', ['as' => 'degree.student.formfillup.dbbl_view', 'uses' => 'DegreeFormfillupController@dbblPageView']);
    Route::any('/payment_view', ['as' => 'degree.student.formfillup.payment_view', 'uses' => 'DegreeFormfillupController@payment_view']);

    Route::any('/confirmslip', ['as' => 'degree.student.formfillup.confirmslip', 'uses' => 'DegreeFormfillupController@createConfirmSlip']);

    Route::any('/dbbl_approve', ['as' => 'degree.student.formfillup.dbbl.approve', 'uses' => 'DegreeFormfillupController@dbblApprove']);
    Route::any('/logout', ['as' => 'degree.formfillup.logout', 'uses' => 'DegreeFormfillupController@formfillupLogout']);
});

// Masters Form Fillup
Route::group(['prefix' => 'Masters/formfillup'], function () {

    Route::any('/', ['as' => 'masters.student.formfillup', 'uses' => 'MastersFormfillupController@index']);
    Route::any('/check', ['as' => 'masters.student.formfillup.check', 'uses' => 'MastersFormfillupController@check']);
    Route::any('/checktype', ['as' => 'masters.student.formfillup.checktype', 'uses' => 'MastersFormfillupController@checktype']);
    Route::any('/view', ['as' => 'masters.student.formfillup.view', 'uses' => 'MastersFormfillupController@view']);
    Route::any('/next_step', ['as' => 'masters.student.formfillup.next_step', 'uses' => 'MastersFormfillupController@nextStep']);
    Route::any('/dbbl_view', ['as' => 'masters.student.formfillup.dbbl_view', 'uses' => 'MastersFormfillupController@dbblPageView']);
    Route::any('/payment_view', ['as' => 'masters.student.formfillup.payment_view', 'uses' => 'MastersFormfillupController@payment_view']);

    Route::any('/confirmslip', ['as' => 'masters.student.formfillup.confirmslip', 'uses' => 'MastersFormfillupController@createConfirmSlip']);

    Route::any('/dbbl_approve', ['as' => 'masters.student.formfillup.dbbl.approve', 'uses' => 'MastersFormfillupController@dbblApprove']);
    Route::any('/logout', ['as' => 'masters.formfillup.logout', 'uses' => 'MastersFormfillupController@formfillupLogout']);
});

// Masters Form Fillup
Route::group(['prefix' => 'Masters1st/formfillup'], function () {

    Route::any('/', ['as' => 'masters1st.student.formfillup', 'uses' => 'Masters1stFormfillupController@index']);
    Route::any('/check', ['as' => 'masters1st.student.formfillup.check', 'uses' => 'Masters1stFormfillupController@check']);
    Route::any('/checktype', ['as' => 'masters1st.student.formfillup.checktype', 'uses' => 'Masters1stFormfillupController@checktype']);
    Route::any('/view', ['as' => 'masters1st.student.formfillup.view', 'uses' => 'Masters1stFormfillupController@view']);
    Route::any('/next_step', ['as' => 'masters1st.student.formfillup.next_step', 'uses' => 'Masters1stFormfillupController@nextStep']);
    Route::any('/dbbl_view', ['as' => 'masters1st.student.formfillup.dbbl_view', 'uses' => 'Masters1stFormfillupController@dbblPageView']);
    Route::any('/payment_view', ['as' => 'masters1st.student.formfillup.payment_view', 'uses' => 'Masters1stFormfillupController@payment_view']);

    Route::any('/confirmslip', ['as' => 'masters1st.student.formfillup.confirmslip', 'uses' => 'Masters1stFormfillupController@createConfirmSlip']);

    Route::any('/dbbl_approve', ['as' => 'masters1st.student.formfillup.dbbl.approve', 'uses' => 'Masters1stFormfillupController@dbblApprove']);
    Route::any('/logout', ['as' => 'masters1st.formfillup.logout', 'uses' => 'Masters1stFormfillupController@formfillupLogout']);
});


// Honours Formfillup
Route::group(['prefix' => 'Honours/formfillup'], function () {

    Route::any('/', ['as' => 'honours.student.formfillup', 'uses' => 'HonoursFormfillupController@index']);
    Route::any('/check', ['as' => 'honours.student.formfillup.check', 'uses' => 'HonoursFormfillupController@check']);
    Route::any('/checktype', ['as' => 'honours.student.formfillup.checktype', 'uses' => 'HonoursFormfillupController@checktype']);
    Route::any('/view', ['as' => 'honours.student.formfillup.view', 'uses' => 'HonoursFormfillupController@view']);
    Route::any('/next_step', ['as' => 'honours.student.formfillup.next_step', 'uses' => 'HonoursFormfillupController@nextStep']);
    Route::any('/dbbl_view', ['as' => 'honours.student.formfillup.dbbl_view', 'uses' => 'HonoursFormfillupController@dbblPageView']);
    Route::any('/payment_view', ['as' => 'honours.student.formfillup.payment_view', 'uses' => 'HonoursFormfillupController@payment_view']);

    Route::any('/confirmslip', ['as' => 'honours.student.formfillup.confirmslip', 'uses' => 'HonoursFormfillupController@createConfirmSlip']);

    Route::any('/dbbl_approve', ['as' => 'honours.student.formfillup.dbbl.approve', 'uses' => 'HonoursFormfillupController@dbblApprove']);
    Route::any('/logout', ['as' => 'honours.formfillup.logout', 'uses' => 'HonoursFormfillupController@formfillupLogout']);
});

// HSC Result Show
Route::any('hsc_result_show', 'Hsc_result\PreHscResultController@hsc_result_show')->name('hsc_result_show');


// Online Application

// honours application
Route::group(['prefix' => 'Application/Honours', 'namespace' => 'Application'], function () {

    Route::any('/', ['as' => 'honours.student.application', 'uses' => 'HonoursApplicationController@index']);
    Route::any('/check', ['as' => 'honours.student.application.check', 'uses' => 'HonoursApplicationController@checkApplication']);
    Route::any('/form', ['as' => 'honours.student.application.form', 'uses' => 'HonoursApplicationController@applicationForm']);
    Route::any('/dbblapplication', ['as' => 'honours.student.application.dbbl', 'uses' => 'HonoursApplicationController@dbblapplication']);
    Route::any('/honAppInformationSubmit', ['as' => 'student.honours.application.honAppInformationSubmit', 'uses' => 'HonoursApplicationController@honAppInformationSubmit']);
    Route::any('/confirmslip', ['as' => 'honours.student.application.confirmslip', 'uses' => 'HonoursApplicationController@confirmslip']);
    Route::any('/logout', ['as' => 'student.honours.application.logout', 'uses' => 'HonoursApplicationController@applicationLogout']);
});

// honours admission
Route::group(['prefix' => 'Admission/Honours', 'namespace' => 'Admission\Honours'], function () {

    Route::any('/', ['as' => 'student.honours.admission', 'uses' => 'HonoursAdmissionController@index']);
    Route::any('/checkMerit', ['as' => 'student.honours.admission.check', 'uses' => 'HonoursAdmissionController@check']);
    Route::any('/Form', ['as' => 'student.honours.admission.form', 'uses' => 'HonoursAdmissionController@admissionForm']);
    Route::any('/dbblapplication', ['as' => 'student.honours.admission.dbbl', 'uses' => 'HonoursAdmissionController@dbblapplication']);
    Route::any('/honAdmInformationSubmit', ['as' => 'student.honours.admission.honAdmInformationSubmit', 'uses' => 'HonoursAdmissionController@honAdmInformationSubmit']);
    Route::any('/confirmslip', ['as' => 'student.honours.admission.confirmslip', 'uses' => 'HonoursAdmissionController@confirmslip']);

    Route::any('/signin', ['as' => 'student.honours.admission.signin', 'uses' => 'HonoursAdmissionController@honSignin']);
    Route::post('/retrievepass', ['as' => 'student.honours.admission.retrievepass', 'uses' => 'HonoursAdmissionController@retrievepass']);

    Route::post('/honStudentSignin', ['as' => 'student.honours.admission.hscStudentSignin', 'uses' => 'HonoursAdmissionController@honStudentSignin']);
    Route::any('/HonConfirmation', ['as' => 'student.honours.admission.HonConfirmation', 'uses' => 'HonoursAdmissionController@HonConfirmation']);
    Route::any('/logout', ['as' => 'student.honours.admission.logout', 'uses' => 'HonoursAdmissionController@admisionLogout']);
    Route::post('/slipId', ['as' => 'student.honours.admission.slipId', 'uses' => 'HonoursAdmissionController@downloadSlipId']);
    Route::post('/formId', ['as' => 'student.honours.admission.formId', 'uses' => 'HonoursAdmissionController@downloadHonForm']);
});

// masters application
Route::group(['prefix' => 'Application/Masters', 'namespace' => 'Application'], function () {

    Route::any('/', ['as' => 'masters.student.application', 'uses' => 'MastersApplicationController@index']);
    Route::any('/check', ['as' => 'masters.student.application.check', 'uses' => 'MastersApplicationController@checkApplication']);
    Route::any('/form', ['as' => 'masters.student.application.form', 'uses' => 'MastersApplicationController@applicationForm']);
    Route::any('/dbblapplication', ['as' => 'masters.student.application.dbbl', 'uses' => 'MastersApplicationController@dbblapplication']);
    Route::any('/mscAppInformationSubmit', ['as' => 'student.masters.application.mscAppInformationSubmit', 'uses' => 'MastersApplicationController@mscAppInformationSubmit']);
    Route::any('/confirmslip', ['as' => 'masters.student.application.confirmslip', 'uses' => 'MastersApplicationController@confirmslip']);
    Route::post('/formId', ['as' => 'student.masters.application.formId', 'uses' => 'MastersApplicationController@downloadHonsForm']);

    Route::any('/signin', ['as' => 'student.masters.application.signin', 'uses' => 'MastersApplicationController@mscSignin']);
    Route::post('/retrievepass', ['as' => 'student.masters.application.retrievepass', 'uses' => 'MastersApplicationController@retrievepass']);
    Route::post('/mastersStudentSignin', ['as' => 'student.masters.application.hscStudentSignin', 'uses' => 'MastersApplicationController@mscStudentSignin']);
    Route::any('/logout', ['as' => 'student.masters.application.logout', 'uses' => 'MastersApplicationController@applicationLogout']);
});

// masters private registration
Route::group(['prefix' => 'Registration/Masters/Private', 'namespace' => 'Application'], function () {

    Route::any('/', ['as' => 'student.masters.private.reg', 'uses' => 'MastersPrivateRegController@index']);
    Route::any('/check', ['as' => 'student.masters.private.reg.check', 'uses' => 'MastersPrivateRegController@checkApplication']);
    Route::any('/form', ['as' => 'student.masters.private.reg.form', 'uses' => 'MastersPrivateRegController@applicationForm']);
    Route::any('/dbblapplication', ['as' => 'student.masters.private.reg.dbbl', 'uses' => 'MastersPrivateRegController@dbblapplication']);
    Route::any('/mscRegInformationSubmit', ['as' => 'student.masters.private.reg.mscAppInformationSubmit', 'uses' => 'MastersPrivateRegController@mscAppInformationSubmit']);
    Route::any('/confirmslip', ['as' => 'student.masters.private.reg.confirmslip', 'uses' => 'MastersPrivateRegController@confirmslip']);
    Route::any('/logout', ['as' => 'student.masters.private.reg.logout', 'uses' => 'MastersPrivateRegController@applicationLogout']);
});

// degree application
Route::group(['prefix' => 'Application/Degree', 'namespace' => 'Application'], function () {

    Route::any('/', ['as' => 'degree.student.application', 'uses' => 'DegreeApplicationController@index']);
    Route::any('/check', ['as' => 'degree.student.application.check', 'uses' => 'DegreeApplicationController@checkApplication']);
    Route::any('/form', ['as' => 'degree.student.application.form', 'uses' => 'DegreeApplicationController@applicationForm']);
    Route::any('/dbblapplication', ['as' => 'degree.student.application.dbbl', 'uses' => 'DegreeApplicationController@dbblapplication']);
    Route::any('/degAppInformationSubmit', ['as' => 'student.degree.application.degAppInformationSubmit', 'uses' => 'DegreeApplicationController@degAppInformationSubmit']);
    Route::any('/confirmslip', ['as' => 'degree.student.application.confirmslip', 'uses' => 'DegreeApplicationController@confirmslip']);
    Route::post('/formId', ['as' => 'student.degree.application.formId', 'uses' => 'DegreeApplicationController@downloadDegForm']);

    Route::any('/signin', ['as' => 'student.degree.application.signin', 'uses' => 'DegreeApplicationController@degSignin']);
    Route::post('/retrievepass', ['as' => 'student.degree.application.retrievepass', 'uses' => 'DegreeApplicationController@retrievepass']);
    Route::post('/degStudentSignin', ['as' => 'student.degree.application.degStudentSignin', 'uses' => 'DegreeApplicationController@degStudentSignin']);
    Route::any('/logout', ['as' => 'student.degree.application.logout', 'uses' => 'DegreeApplicationController@applicationLogout']);
});

// masters private registration
Route::group(['prefix' => 'Registration/Degree/Private', 'namespace' => 'Application'], function () {

    Route::any('/', ['as' => 'student.degree.private.reg', 'uses' => 'DegreePrivateRegController@index']);
    Route::any('/check', ['as' => 'student.degree.private.reg.check', 'uses' => 'DegreePrivateRegController@checkApplication']);
    Route::any('/form', ['as' => 'student.degree.private.reg.form', 'uses' => 'DegreePrivateRegController@applicationForm']);
    Route::any('/dbblapplication', ['as' => 'student.degree.private.reg.dbbl', 'uses' => 'DegreePrivateRegController@dbblapplication']);
    Route::any('/mscRegInformationSubmit', ['as' => 'student.degree.private.reg.degAppInformationSubmit', 'uses' => 'DegreePrivateRegController@degAppInformationSubmit']);
    Route::any('/confirmslip', ['as' => 'student.degree.private.reg.confirmslip', 'uses' => 'DegreePrivateRegController@confirmslip']);
    Route::any('/logout', ['as' => 'student.degree.private.reg.logout', 'uses' => 'DegreePrivateRegController@applicationLogout']);
});


// masters application
Route::group(['prefix' => 'Application/Masters1st', 'namespace' => 'Application'], function () {

    Route::any('/', ['as' => 'student.masters1st.application', 'uses' => 'Masters1stApplicationController@index']);
    Route::any('/check', ['as' => 'student.masters1st.application.check', 'uses' => 'Masters1stApplicationController@checkApplication']);
    Route::any('/form', ['as' => 'student.masters1st.application.form', 'uses' => 'Masters1stApplicationController@applicationForm']);
    Route::any('/dbblapplication', ['as' => 'student.masters1st.application.dbbl', 'uses' => 'Masters1stApplicationController@dbblapplication']);
    Route::any('/mscAppInformationSubmit', ['as' => 'student.masters1st.application.mscAppInformationSubmit', 'uses' => 'Masters1stApplicationController@mscAppInformationSubmit']);
    Route::any('/confirmslip', ['as' => 'student.masters1st.application.confirmslip', 'uses' => 'Masters1stApplicationController@confirmslip']);
    Route::post('/formId', ['as' => 'student.masters1st.application.formId', 'uses' => 'Masters1stApplicationController@downloadHonsForm']);

    Route::any('/signin', ['as' => 'student.masters1st.application.signin', 'uses' => 'Masters1stApplicationController@mscSignin']);
    Route::post('/retrievepass', ['as' => 'student.masters1st.application.retrievepass', 'uses' => 'Masters1stApplicationController@retrievepass']);
    Route::post('/mastersStudentSignin', ['as' => 'student.masters1st.application.hscStudentSignin', 'uses' => 'Masters1stApplicationController@mscStudentSignin']);
    Route::any('/logout', ['as' => 'student.masters1st.application.logout', 'uses' => 'Masters1stApplicationController@applicationLogout']);
});

// masters admission
Route::group(['prefix' => 'Admission/Masters', 'namespace' => 'Admission\Masters'], function () {

    Route::any('/', ['as' => 'student.masters.admission', 'uses' => 'MastersAdmissionController@index']);
    Route::any('/checkMerit', ['as' => 'student.masters.admission.check', 'uses' => 'MastersAdmissionController@checkMerit']);
    Route::any('/Form', ['as' => 'student.masters.admission.form', 'uses' => 'MastersAdmissionController@admissionForm']);
    Route::any('/dbblapplication', ['as' => 'student.masters.admission.dbbl', 'uses' => 'MastersAdmissionController@dbblapplication']);
    Route::any('/mscAdmInformationSubmit', ['as' => 'student.masters.admission.mscAdmInformationSubmit', 'uses' => 'MastersAdmissionController@mscAdmInformationSubmit']);
    Route::any('/confirmslip', ['as' => 'student.masters.admission.confirmslip', 'uses' => 'MastersAdmissionController@confirmslip']);

    Route::any('/signin', ['as' => 'student.masters.admission.signin', 'uses' => 'MastersAdmissionController@mscSignin']);

    Route::post('/mscStudentSignin', ['as' => 'student.masters.admission.mscStudentSignin', 'uses' => 'MastersAdmissionController@mscStudentSignin']);
    Route::any('/mscConfirmation', ['as' => 'student.masters.admission.mscConfirmation', 'uses' => 'MastersAdmissionController@mscConfirmation']);
    Route::any('/logout', ['as' => 'student.masters.admission.logout', 'uses' => 'MastersAdmissionController@admisionLogout']);
    Route::post('/slipId', ['as' => 'student.masters.admission.slipId', 'uses' => 'MastersAdmissionController@downloadSlipId']);
    Route::post('/formId', ['as' => 'student.masters.admission.formId', 'uses' => 'MastersAdmissionController@downloadMscForm']);
    Route::post('/retrievepass', ['as' => 'student.masters.application.retrievepass', 'uses' => 'MastersAdmissionController@retrievepass']);
});

Route::group(['prefix' => 'Admission/Masters1st', 'namespace' => 'Admission\Masters'], function () {
    Route::any('/', ['as' => 'student.masters1st.admission', 'uses' => 'Masters1stAdmissionController@index']);
    Route::post('/checkMerit', ['as' => 'student.masters1st.meritCheck', 'uses' => 'Masters1stAdmissionController@checkMerit']);

    Route::get('/faculty', ['as' => 'student.masters1st.faculty', 'uses' => 'Masters1stAdmissionController@faculty']);

    Route::any('/Form', ['as' => 'student.masters1st.admission.form', 'uses' => 'Masters1stAdmissionController@admissionForm']);

    Route::any('/districtCh', ['as' => 'student.masters1st.admission.dist', 'uses' => 'Masters1stAdmissionController@districtChange']);

    Route::any('/mastersInformationSubmit', ['as' => 'student.masters1st.admission.mastersInformationSubmit', 'uses' => 'Masters1stAdmissionController@mastersInformationSubmit']);

    Route::any('/signin', ['as' => 'student.masters1st.admission.signin', 'uses' => 'Masters1stAdmissionController@mastersSignin']);

    Route::post('/retrievepass', ['as' => 'student.masters1st.admission.retrievepass', 'uses' => 'Masters1stAdmissionController@retrievepass']);

    Route::post('/mastersStudentSignin', ['as' => 'student.masters1st.admission.mastersStudentSignin', 'uses' => 'Masters1stAdmissionController@mastersStudentSignin']);

    Route::any('/MastersConfirmation', ['as' => 'student.masters1st.admission.MastersConfirmation', 'uses' => 'Masters1stAdmissionController@MastersConfirmation']);

    Route::any('/logout', ['as' => 'student.masters1st.admission.logout', 'uses' => 'Masters1stAdmissionController@admisionLogout']);

    Route::any('/admissionFee', ['as' => 'student.masters1st.admission.admissionFee', 'uses' => 'Masters1stAdmissionController@mastersadmissionFee']);

    Route::post('/slipId', ['as' => 'student.masters1st.admission.slipId', 'uses' => 'Masters1stAdmissionController@downloadSlipId']);

    Route::post('/formId', ['as' => 'student.masters1st.admission.formId', 'uses' => 'Masters1stAdmissionController@downloadMscForm']);
});

// degree admission
Route::group(['prefix' => 'Admission/Degree', 'namespace' => 'Admission\Degree'], function () {

    Route::any('/', ['as' => 'student.degree.admission', 'uses' => 'DegreeAdmissionController@index']);
    Route::any('/checkMerit', ['as' => 'student.degree.admission.check', 'uses' => 'DegreeAdmissionController@checkMerit']);
    Route::any('/Form', ['as' => 'student.degree.admission.form', 'uses' => 'DegreeAdmissionController@admissionForm']);
    Route::any('/dbblapplication', ['as' => 'student.degree.admission.dbbl', 'uses' => 'DegreeAdmissionController@dbblapplication']);
    Route::any('/degAdmInformationSubmit', ['as' => 'student.degree.admission.degAdmInformationSubmit', 'uses' => 'DegreeAdmissionController@degAdmInformationSubmit']);
    Route::any('/confirmslip', ['as' => 'student.degree.admission.confirmslip', 'uses' => 'DegreeAdmissionController@confirmslip']);

    Route::any('/signin', ['as' => 'student.degree.admission.signin', 'uses' => 'DegreeAdmissionController@degSignin']);

    Route::post('/degStudentSignin', ['as' => 'student.degree.admission.degStudentSignin', 'uses' => 'DegreeAdmissionController@degStudentSignin']);
    Route::any('/degConfirmation', ['as' => 'student.degree.admission.degConfirmation', 'uses' => 'DegreeAdmissionController@degConfirmation']);
    Route::any('/logout', ['as' => 'student.degree.admission.logout', 'uses' => 'DegreeAdmissionController@admisionLogout']);
    Route::post('/slipId', ['as' => 'student.degree.admission.slipId', 'uses' => 'DegreeAdmissionController@downloadSlipId']);
    Route::post('/formId', ['as' => 'student.degree.admission.formId', 'uses' => 'DegreeAdmissionController@downloadDegForm']);
    Route::post('/retrievepass', ['as' => 'student.degree.application.retrievepass', 'uses' => 'DegreeAdmissionController@retrievepass']);
});

// HSC 2nd Admission
Route::group(['prefix' => 'HSC/2nd/Admission', 'namespace' => 'Admission\HSC'], function () {

    Route::any('/', ['as' => 'hsc2nd.student.adm', 'uses' => 'HSC2ndAdmissionController@index']);
    Route::any('/check', ['as' => 'hsc2nd.student.adm.check', 'uses' => 'HSC2ndAdmissionController@check']);
    Route::any('/checktype', ['as' => 'hsc2nd.student.adm.checktype', 'uses' => 'HSC2ndAdmissionController@checktype']);
    Route::any('/view', ['as' => 'hsc2nd.student.adm.view', 'uses' => 'HSC2ndAdmissionController@view']);
    Route::any('/next_step', ['as' => 'hsc2nd.student.adm.next_step', 'uses' => 'HSC2ndAdmissionController@nextStep']);
    Route::any('/dbbl_view', ['as' => 'hsc2nd.student.adm.dbbl_view', 'uses' => 'HSC2ndAdmissionController@dbblPageView']);
    Route::any('/payment_view', ['as' => 'hsc2nd.student.adm.payment_view', 'uses' => 'HSC2ndAdmissionController@payment_view']);

    Route::any('/confirmslip', ['as' => 'hsc2nd.student.adm.confirmslip', 'uses' => 'HSC2ndAdmissionController@createConfirmSlip']);

    Route::any('/dbbl_approve', ['as' => 'hsc2nd.student.adm.dbbl.approve', 'uses' => 'HSC2ndAdmissionController@dbblApprove']);
    Route::any('/logout', ['as' => 'hsc2nd.adm.logout', 'uses' => 'HSC2ndAdmissionController@formfillupLogout']);
});

Route::get('manage_student_data', 'ApiController@manage_data');
Route::get('masters_ff_total_sub_assign', 'ApiController@masters_ff_total_sub_assign');
Route::get('generate_deg_ff_student', 'ApiController@generate_deg_ff_student');
Route::get('generate_hons_ff_student', 'ApiController@generate_hons_ff_student');

// HSC Form Fillup
Route::group(['prefix' => 'HSC/formfillup'], function () {
    Route::any('/', ['as' => 'hsc.student.formfillup', 'uses' => 'HSCFormfillupController@index']);
    Route::any('/check', ['as' => 'hsc.student.formfillup.check', 'uses' => 'HSCFormfillupController@check']);
    Route::any('/checktype', ['as' => 'hsc.student.formfillup.checktype', 'uses' => 'HSCFormfillupController@checktype']);
    Route::any('/view', ['as' => 'hsc.student.formfillup.view', 'uses' => 'HSCFormfillupController@view']);
    Route::any('/next_step', ['as' => 'hsc.student.formfillup.next_step', 'uses' => 'HSCFormfillupController@nextStep']);
    Route::any('/dbbl_view', ['as' => 'hsc.student.formfillup.dbbl_view', 'uses' => 'HSCFormfillupController@dbblPageView']);
    Route::any('/payment_view', ['as' => 'hsc.student.formfillup.payment_view', 'uses' => 'HSCFormfillupController@payment_view']);
    Route::any('/confirmslip', ['as' => 'hsc.student.formfillup.confirmslip', 'uses' => 'HSCFormfillupController@createConfirmSlip']);
    Route::any('/dbbl_approve', ['as' => 'hsc.student.formfillup.dbbl.approve', 'uses' => 'HSCFormfillupController@dbblApprove']);
    Route::any('/logout', ['as' => 'degree.formfillup.logout', 'uses' => 'HSCFormfillupController@formfillupLogout']);
});

Route::post('student/instruction', ['as' => 'student.instruction', 'uses' => 'HomeController@student_instruction']);

// Students Routes
// Route::group(['prefix' => 'admission/hsc','namespace'=>'Admission'],function() {

//     Route::any('/', ['as' => 'student.admission.hsc', 'uses' => 'HSCAdmissionController@index']);
//     Route::post('/step1', ['as' => 'student.admission.hsc.step1', 'uses' => 'HSCAdmissionController@step1']);
//     Route::post('/checkMerit', ['as' => 'student.hsc.meritCheck', 'uses' => 'HSCAdmissionController@checkMerit']);
//     Route::any('/Form', ['as' => 'student.hsc.admission.form', 'uses' => 'HSCAdmissionController@admissionForm']);
//     Route::any('/districtCh', ['as' => 'student.hsc.admission.dist', 'uses' => 'HSCAdmissionController@districtChange']);

//     Route::post('/hscGroupChange', ['as' => 'student.hsc.admission.hscGroupChange', 'uses' => 'HSCAdmissionController@hscGroupChange']);

//     Route::any('/hscInformationSubmit', ['as' => 'student.hsc.admission.hscInformationSubmit', 'uses' => 'HSCAdmissionController@hscInformationSubmit']);


//     Route::any('/signin', ['as' => 'student.hsc.admission.signin', 'uses' => 'HSCAdmissionController@hscSignin']);  

//     Route::post('/retrievepass', ['as' => 'student.hsc.admission.retrievepass', 'uses' => 'HSCAdmissionController@retrievepass']);  

//     Route::post('/hscStudentSignin', ['as' => 'student.hsc.admission.hscStudentSignin', 'uses' => 'HSCAdmissionController@hscStudentSignin']);  

//     Route::any('/HscConfirmation', ['as' => 'student.hsc.admission.HscConfirmation', 'uses' => 'HSCAdmissionController@HscConfirmation']);

//     Route::post('/formId', ['as' => 'student.hsc.admission.formId', 'uses' => 'HSCAdmissionController@downloadHscForm']);  
//     Route::get('/formIdtest', ['as' => 'student.hsc.admission.formId', 'uses' => 'HSCAdmissionController@downloadHscFormtest']);  

//     Route::post('/tidId', ['as' => 'student.hsc.admission.tidId', 'uses' => 'HSCAdmissionController@downloadHscIdCard']); 

//     Route::post('/slipId', ['as' => 'student.hsc.admission.slipId', 'uses' => 'HSCAdmissionController@downloadSlipId']); 

//     Route::post('/SubjectCodeSequence', ['as' => 'student.hsc.admission.SubjectCodeSequence', 'uses' => 'HSCAdmissionController@SubjectCodeSequence']); 

//     Route::any('/admissionFee', ['as' => 'student.hsc.admission.admissionFee', 'uses' => 'HSCAdmissionController@admissionFee']);
//     Route::any('/dutchbangla', ['as' => 'student.hsc.admission.dutchbangla', 'uses' => 'HSCAdmissionController@dutchbangla']);

//      Route::any('/hscimagedownload', ['as' => 'student.hsc.admission.hscimagedownload', 'uses' => 'HSCAdmissionController@hscimagedownload']);

//     Route::any('/logout', ['as' => 'student.hsc.admission.logout', 'uses' => 'HSCAdmissionController@admisionLogout']);

// });

Route::get('manage_student_data', 'ApiController@manage_data');

Route::get('result', 'UserController@hscResult')->name('hsc_result.result');
Route::post('result', 'UserController@hscResultSearch')->name('hsc_result.search');
Route::post('result-pdf', 'UserController@hscResultPdf')->name('hsc_result.result-pdf');


Route::any('/imagedownload', ['as' => 'student.imagedownload', 'uses' => 'ApiController@imagedownload']);

Route::any('get-admit-card', 'HomeController@getAdmitCard')->name('get-admit-card');
Route::post('download-admit-card', 'HomeController@downloadAdmitCard')->name('download-admit-card');

// Degree Form Fillup
Route::group(['prefix' => 'fees-payment'], function () {

    Route::any('/', ['as' => 'fees-payment.index', 'uses' => 'FeesPaymentController@index']);
    Route::any('/check', ['as' => 'fees.check-eligibility', 'uses' => 'FeesPaymentController@checkEligibility']);
    Route::any('/form', ['as' => 'fees-payment.form', 'uses' => 'FeesPaymentController@showForm']);
    Route::any('/submit', ['as' => 'fees-payment.submit', 'uses' => 'FeesPaymentController@submitForm']);
    Route::any('/payment-view', ['as' => 'fees-payment.payment-view', 'uses' => 'FeesPaymentController@paymentView']);
    Route::any('/submit/payment-information', ['as' => 'fees-payment.payment-information.submit', 'uses' => 'FeesPaymentController@submitPaymentInformation']);

    Route::any('/confirmation', ['as' => 'fees-payment.confirmation', 'uses' => 'FeesPaymentController@confirmation']);

    Route::any('/download-slip', ['as' => 'fees-payment.download-slip', 'uses' => 'FeesPaymentController@downloadSlip']);
});
