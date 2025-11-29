<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'students', 'namespace' => 'Student', 'middleware' => 'auth'], function () {
    // FeesEligibility Routes
    Route::group(['prefix' => 'fees-eligibility', 'namespace' => 'Fees'], function () {
        Route::get('/', 'FeesEligibilityController@index')->name('student.fees-eligibility.index');
        Route::any('/data', 'FeesEligibilityController@getData')->name('student.fees-eligibility.data');
        Route::any('/summary', 'FeesEligibilityController@getSummary')->name('student.fees-eligibility.summary');
        Route::get('/create', 'FeesEligibilityController@create')->name('student.fees-eligibility.create');
        Route::post('/store', 'FeesEligibilityController@store')->name('student.fees-eligibility.store');
        Route::get('/{id}/edit', 'FeesEligibilityController@edit')->name('student.fees-eligibility.edit');
        Route::put('/{id}', 'FeesEligibilityController@update')->name('student.fees-eligibility.update');
        Route::patch('/{id}/status', 'FeesEligibilityController@updateStatus')->name('student.fees-eligibility.update-status');
        Route::delete('/{id}', 'FeesEligibilityController@destroy')->name('student.fees-eligibility.destroy');

        // Batch Operations
        Route::post('/batch-enable', 'FeesEligibilityController@batchEnable')->name('student.fees-eligibility.batch-enable');
        Route::post('/batch-disable', 'FeesEligibilityController@batchDisable')->name('student.fees-eligibility.batch-disable');
        Route::post('/batch-delete', 'FeesEligibilityController@batchDelete')->name('student.fees-eligibility.batch-delete');

        // CSV Upload Routes
        Route::get('/csv-upload', 'FeesEligibilityController@showCsvUpload')->name('student.fees-eligibility.csv-upload');
        Route::post('/csv-upload', 'FeesEligibilityController@processCsvUpload')->name('student.fees-eligibility.csv-upload.process');
    });

    // FeesConfiguration Routes
    Route::group(['prefix' => 'fees-configuration', 'namespace' => 'Fees'], function () {
        Route::get('/', 'FeesConfigurationController@index')->name('student.fees-configuration.index');
        Route::get('/data', 'FeesConfigurationController@getData')->name('student.fees-configuration.data');
        Route::get('/summary', 'FeesConfigurationController@getSummary')->name('student.fees-configuration.summary');
        Route::get('/create', 'FeesConfigurationController@create')->name('student.fees-configuration.create');
        Route::post('/store', 'FeesConfigurationController@store')->name('student.fees-configuration.store');
        Route::get('/edit/{id}', 'FeesConfigurationController@edit')->name('student.fees-configuration.edit');
        Route::patch('/update/{id}', 'FeesConfigurationController@update')->name('student.fees-configuration.update');
        Route::patch('/{id}/status', 'FeesConfigurationController@updateStatus')->name('student.fees-configuration.update-status');
        Route::delete('/{id}', 'FeesConfigurationController@destroy')->name('student.fees-configuration.destroy');
    });

    // FeesPayment Routes (for payment processing)
    Route::group(['prefix' => 'fees-payment', 'namespace' => 'Fees'], function () {
        Route::any('/report-data', 'FeesPaymentController@getReportData')->name('student.fees-payment.report-data');
        Route::post('/check-eligibility', 'FeesPaymentController@checkEligibility')->name('student.fees-payment.check-eligibility');
        Route::post('/process-payment', 'FeesPaymentController@processPayment')->name('student.fees-payment.process');
        Route::patch('/{id}/status', 'FeesPaymentController@updatePaymentStatus')->name('student.fees-payment.update-status');
        Route::get('/export', 'FeesPaymentController@exportReport')->name('student.fees-payment.export');

        // Payslip related routes
        Route::get('/{id}/slip', 'FeesPaymentController@downloadPaymentSlip')->name('student.fees-payment.slip');
        Route::get('/{id}/slip-details', 'FeesPaymentController@getPaymentSlipDetails')->name('student.fees-payment.slip-details');
        Route::post('/available-headers', 'FeesPaymentController@getAvailablePayslipHeaders')->name('student.fees-payment.available-headers');
        Route::post('/calculate-fees', 'FeesPaymentController@calculateFeesFromPayslip')->name('student.fees-payment.calculate-fees');
    });

    // Registration CSV Update Routes
    Route::group(['prefix' => 'registration-csv', 'namespace' => 'Fees'], function () {
        Route::get('/', 'FeesEligibilityController@showRegistrationCsvUpload')->name('student.registration.csv-upload');
        Route::post('/upload', 'FeesEligibilityController@processRegistrationCsvUpload')->name('student.registration.csv-upload.process');
    });
});

Route::group(['as' => 'student.', 'namespace' => 'Student\Fees', 'middleware' => 'auth', 'prefix' => 'students/fees-payment'], function () {
    Route::get('/report', 'FeesPaymentReportController@index')->name('fees-payment.report');
    Route::get('/fees-payment-report/data', 'FeesPaymentReportController@getData')->name('fees-payment-report.data');
    Route::get('/fees-payment-report/details', 'FeesPaymentReportController@getDetails')->name('fees-payment-report.details');
    Route::get('/fees-payment-report/summary', 'FeesPaymentReportController@getSummary')->name('fees-payment-report.summary');
    Route::get('/fees-payment-report/export', 'FeesPaymentReportController@export')->name('fees-payment-report.export');
});