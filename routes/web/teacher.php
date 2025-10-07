<?php
use Illuminate\Support\Facades\Route;

Route::group(array('prefix' => 'teachers', 'namespace'=>'Teacher', 'middleware' => 'auth','as'=> 'teacher.',),function() {
	Route::resource('/', 'TeachersController');
	Route::post('/datasource', 'TeachersController@datasource')->name('datasource');
	Route::any('teacherdetails', ['as' => 'teacherdetails', 'uses' => 'TeachersController@teacherdetails']);
	Route::any('teacherpds', ['as' => 'teacherpds', 'uses' => 'TeachersController@teacherpds']);
	Route::any('releaseteacher', ['as' => 'releaseteacher', 'uses' => 'TeachersController@releaseteacher']);
	Route::any('releaseteacher/store', ['as' => 'releaseteacher.store', 'uses' => 'TeachersController@releaseteacherStore']);
	Route::any('printReleaseLetter', ['as' => 'printReleaseLetter', 'uses' => 'TeachersController@printReleaseLetter']);
	Route::any('printJoinLetter', ['as' => 'printJoinLetter', 'uses' => 'TeachersController@printJoinLetter']);
	Route::any('editTeacher', ['as' => 'editTeacher', 'uses' => 'TeachersController@editTeacher']);
	Route::any('editTeacherPersonal', ['as' => 'editTeacherPersonal', 'uses' => 'TeachersController@editTeacherPersonal']);
	Route::any('ajaximageTeacher', ['as' => 'ajaximageTeacher', 'uses' => 'TeachersController@ajaximageTeacher']);	
	Route::any('editTeachereducationinput', ['as' => 'editTeachereducationinput', 'uses' => 'TeachersController@editTeachereducationinput']);	
	Route::any('editTeacheremploymentinput', ['as' => 'editTeacheremploymentinput', 'uses' => 'TeachersController@editTeacheremploymentinput']);
	Route::any('editTeacherappointmentinput', ['as' => 'editTeacherappointmentinput', 'uses' => 'TeachersController@editTeacherappointmentinput']);
	Route::any('editTeachercareerinput', ['as' => 'editTeachercareerinput', 'uses' => 'TeachersController@editTeachercareerinput']);	
	Route::any('joinTeacheraction', ['as' => 'joinTeacheraction', 'uses' => 'TeachersController@joinTeacheraction']);	
	Route::any('deleteTeacher', ['as' => 'deleteTeacher', 'uses' => 'TeachersController@deleteTeacher']);								
        Route::any('/idcard', ['as' => 'idcard', 'uses' => 'TeacherIdCardController@idcard']);
	Route::any('/generateidcard', ['as' => 'generateidcard', 'uses' => 'TeacherIdCardController@generateidcard']);
	
	Route::resource('designation', 'DesignationController');
	Route::resource('university-list', 'UniversityListController');
	Route::resource('subject-list', 'SubjectListController');

	Route::post('designation/datasource', 'DesignationController@datasource')->name('designation.datasource');
	Route::post('university-list/datasource', 'UniversityListController@datasource')->name('university-list.datasource');
	Route::post('subject-list/datasource', 'SubjectListController@datasource')->name('subject-list.datasource');
});