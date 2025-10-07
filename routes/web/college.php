<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'HSC/formfillup'],function() {
	Route::post('payment/approve', 'HSCFormfillupController@payment_approve');
});

Route::group(['prefix' => 'Admission/Honours','namespace'=>'Admission\Honours',],function() {
	Route::post('payment/approve', 'HonoursAdmissionController@payment_approve');
});

Route::group(['prefix' => 'Admission/Masters','namespace'=>'Admission\Masters',],function() {
	Route::post('payment/approve', 'MastersAdmissionController@payment_approve');
});

Route::group(['prefix' => 'Admission/HSC','namespace'=>'Admission\HSC',],function() {
	Route::post('payment/approve', 'HSCAdmissionController@payment_approve');
});


Route::get('student/hons', 'ApiController@student_hons');

Route::group(['prefix' => 'Honours/formfillup'],function() {
	Route::post('payment/approve', 'HonoursFormfillupController@payment_approve');
});

Route::group(['prefix' => 'Degree/formfillup'],function() {
	Route::post('payment/approve', 'DegreeFormfillupController@payment_approve');
});

Route::group(['prefix' => 'Admission/Masters1st','namespace'=>'Admission\Masters',],function() {
	Route::post('payment/approve', 'Masters1stAdmissionController@payment_approve');
});

Route::group(['prefix' => 'Admission/Degree','namespace'=>'Admission\Degree',],function() {
	Route::post('payment/approve', 'DegreeAdmissionController@payment_approve');
});

Route::group(['prefix' => 'Masters/formfillup'],function() {
	Route::post('payment/approve', 'MastersFormfillupController@payment_approve');
});

Route::group(['prefix' => 'HSC/2nd/Admission/','namespace'=>'Admission\HSC',],function() {
	Route::post('payment/approve', 'HSC2ndAdmissionController@payment_approve');
});