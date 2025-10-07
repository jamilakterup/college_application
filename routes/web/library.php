<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'library', 'middleware' => 'auth','namespace'=>'Library','as'=> 'library.'], function() {

	Route::resource('material', 'LibraryMaterialController');
	Route::resource('member', 'LibraryMemberController');
	Route::post('material/material_catalog', 'LibraryMaterialController@material_catalog')->name('material.material_catalog');
	Route::post('material/material_details', 'LibraryMaterialController@material_details')->name('material.material_details');
	Route::resource('circulation', 'LibraryCirculationController', ['except' => ['create']]);

	Route::get('/', ['as' => 'home', 'uses' => 'LibraryMaterialController@index']);	
	Route::get('check/circulation', ['as' => 'circulation.check', 'uses' => 'LibraryCirculationController@check']);
	Route::get('upload/material', ['as' => 'material.upload', 'uses' => 'LibraryMaterialController@upload']);
	Route::get('csv/material', ['as' => 'material.csv', 'uses' => 'LibraryMaterialController@csv']);

	Route::post('returnbook', ['as' => 'circulation.returnbook', 'uses' => 'LibraryCirculationController@returnBook']);
	Route::post('upload/material/post', ['as' => 'material.postupload', 'uses' => 'LibraryMaterialController@postUpload']);

	Route::any('search/material', ['as' => 'material.search', 'uses' => 'LibraryMaterialController@search']);
	Route::any('search/member', ['as' => 'member.search', 'uses' => 'LibraryMemberController@search']);
	Route::any('search/circulation', ['as' => 'circulation.search', 'uses' => 'LibraryCirculationController@search']);
	Route::any('checkpost/circulation', ['as' => 'circulation.checkpost', 'uses' => 'LibraryCirculationController@checkpost']);

});