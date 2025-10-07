<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/get_hsc_subject_data', 'ApiController@get_hsc_subject_data');

Route::post('/get_deptartment_options/{faculty}', 'ApiController@get_deptartment_options');
Route::post('/get_thana_options/{district}', 'ApiController@get_district_options');
Route::post('/get_upazilas/{district}', 'ApiController@getUpazila');
Route::post('/hscGroupChange', ['as' => 'student.hsc.hscGroupChange', 'uses' => 'ApiController@hscGroupChange']);
Route::post('get-exam-options/{class_id}', 'ApiController@getExamOptions')->name('get-exam-options');

Route::post('sync-device-attendances', 'DeviceAttendanceApiController@syncDeviceAttendance');
Route::post('get-ajax-modal', 'ApiController@getAjaxModal')->name('get-modal');
Route::get('getWeekendHolidays', 'ApiController@getWeekendHolidays');