<?php
use Illuminate\Support\Facades\Route;

// Site Setting Controller
Route::get('/settings', 'SettingController@index')->name('admin.settings');
Route::post('/settings', 'SettingController@general_update')->name('settings.general.update');
Route::get('/settings/social', 'SettingController@social')->name('settings.social');
Route::post('/settings/social', 'SettingController@social_update')->name('settings.social.update');

Route::get('/settings/instruction', 'SettingController@instruction')->name('settings.instruction');
Route::post('/settings/instruction', 'SettingController@instruction_update')->name('settings.instruction.update');
Route::any('/settings/configuration/edit', 'SettingController@config_edit')->name('settings.config.edit');
Route::post('/settings/configuration/update', 'SettingController@config_update')->name('settings.config.update');
// End Site Setting Controller