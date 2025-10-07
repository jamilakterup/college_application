<?php
use Illuminate\Support\Facades\Route;

Route::group(array('prefix' => 'hall', 'middleware' => 'auth', 'as'=> 'hall.','namespace'=>'Hall'),function() {
	Route::resource('/', 'HallController');
	Route::get('{id}/edit', 'HallController@edit')->name('edit');
	Route::put('{id}', 'HallController@update')->name('update');
	Route::any('/add_hall', ['as' => 'add_hall', 'uses' => 'HallController@addHall']);
	Route::any('hallAddInput', ['as' => 'hallAddInput', 'uses' => 'HallController@hallAddInput']);
	Route::any('editHall', ['as' => 'editHall', 'uses' => 'HallController@editHall']);
	Route::any('hallEditInput', ['as' => 'hallEditInput', 'uses' => 'HallController@hallEditInput']);
	Route::any('allocate_seat', ['as' => 'allocate_seat', 'uses' => 'HallController@allocateSeat']);	
	Route::any('hallAllocationRoomSelect', ['as' => 'hallAllocationRoomSelect', 'uses' => 'HallController@hallAllocationRoomSelect']);
	Route::any('hallSeatAllocationselect', ['as' => 'hallSeatAllocationselect', 'uses' => 'HallController@hallSeatAllocationselect']);	
	Route::any('seatAllocationInput', ['as' => 'seatAllocationInput', 'uses' => 'HallController@seatAllocationInput']);
	Route::any('detailsSeatAllocation', ['as' => 'detailsSeatAllocation', 'uses' => 'HallController@detailsSeatAllocation']);
	Route::any('editSeatAllocation', ['as' => 'editSeatAllocation', 'uses' => 'HallController@editSeatAllocation']);
	Route::any('hallAllocationRoomSelect2', ['as' => 'hallAllocationRoomSelect2', 'uses' => 'HallController@hallAllocationRoomSelect2']);	
	Route::any('hallAllocationSeatSelect2', ['as' => 'hallAllocationSeatSelect2', 'uses' => 'HallController@hallAllocationSeatSelect2']);
	Route::any('editSeatAllocationInput', ['as' => 'editSeatAllocationInput', 'uses' => 'HallController@editSeatAllocationInput']);
	Route::any('deleteSeatAllocation', ['as' => 'deleteSeatAllocation', 'uses' => 'HallController@deleteSeatAllocation']);									
});