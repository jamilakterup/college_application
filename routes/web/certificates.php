<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Student\Certificate', 'middleware' => 'auth', 'prefix' => 'certificates'], function () {

    // Testimonial Routes
    Route::get('/testimonial', 'TestimonialController@index')->name('certificates.testimonial.index');
    Route::get('/testimonial/create', 'TestimonialController@create')->name('certificates.testimonial.create');
    Route::post('/testimonial/store', 'TestimonialController@store')->name('certificates.testimonial.store');
    Route::get('/testimonial/{id}', 'TestimonialController@show')->name('certificates.testimonial.show');
    Route::get('/testimonial/{id}/edit', 'TestimonialController@edit')->name('certificates.testimonial.edit');
    Route::put('/testimonial/{id}', 'TestimonialController@update')->name('certificates.testimonial.update');
    Route::delete('/testimonial/{id}', 'TestimonialController@destroy')->name('certificates.testimonial.destroy');
    Route::get('/testimonial/{id}/pdf', 'TestimonialController@generatePdf')->name('certificates.testimonial.pdf');
    Route::get('/testimonial-search', 'TestimonialController@search')->name('certificates.testimonial.search');
    Route::post('/testimonial-bulk-delete', 'TestimonialController@bulkDelete')->name('certificates.testimonial.bulk-delete');
    Route::post('/testimonial-bulk-status', 'TestimonialController@bulkStatusUpdate')->name('certificates.testimonial.bulk-status');
    Route::post('/testimonial-bulk-pdf', 'TestimonialController@bulkPdfDownload')->name('certificates.testimonial.bulk-pdf');
    Route::get('/testimonial-upload', 'TestimonialController@uploadForm')->name('certificates.testimonial.upload');
    Route::post('/testimonial-upload-csv', 'TestimonialController@uploadCsv')->name('certificates.testimonial.upload.csv');
    Route::get('/testimonial-sample', 'TestimonialController@downloadSample')->name('certificates.testimonial.sample');

    // Character Certificate Routes (Prottoyon Potro)
    Route::get('/character', 'CharacterCertificateController@index')->name('certificates.character.index');
    Route::get('/character/create', 'CharacterCertificateController@create')->name('certificates.character.create');
    Route::post('/character/store', 'CharacterCertificateController@store')->name('certificates.character.store');
    Route::get('/character/{id}', 'CharacterCertificateController@show')->name('certificates.character.show');
    Route::get('/character/{id}/edit', 'CharacterCertificateController@edit')->name('certificates.character.edit');
    Route::put('/character/{id}', 'CharacterCertificateController@update')->name('certificates.character.update');
    Route::delete('/character/{id}', 'CharacterCertificateController@destroy')->name('certificates.character.destroy');
    Route::get('/character/{id}/pdf', 'CharacterCertificateController@generatePdf')->name('certificates.character.pdf');
    Route::get('/character-search', 'CharacterCertificateController@search')->name('certificates.character.search');
    Route::get('/character-statistics', 'CharacterCertificateController@statistics')->name('certificates.character.statistics');
    Route::post('/character-bulk-delete', 'CharacterCertificateController@bulkDelete')->name('certificates.character.bulk-delete');
    Route::post('/character-bulk-status', 'CharacterCertificateController@bulkStatusUpdate')->name('certificates.character.bulk-status');
    Route::post('/character-bulk-pdf', 'CharacterCertificateController@bulkPdfDownload')->name('certificates.character.bulk-pdf');
    Route::get('/character-upload', 'CharacterCertificateController@uploadForm')->name('certificates.character.upload');
    Route::post('/character-upload-csv', 'CharacterCertificateController@uploadCsv')->name('certificates.character.upload.csv');
    Route::get('/character-sample', 'CharacterCertificateController@downloadSample')->name('certificates.character.sample');

    // Transfer Certificate Routes
    Route::get('/transfer', 'TransferCertificateController@index')->name('certificates.transfer.index');
    Route::get('/transfer/create', 'TransferCertificateController@create')->name('certificates.transfer.create');
    Route::post('/transfer/store', 'TransferCertificateController@store')->name('certificates.transfer.store');
    Route::get('/transfer/{id}', 'TransferCertificateController@show')->name('certificates.transfer.show');
    Route::get('/transfer/{id}/edit', 'TransferCertificateController@edit')->name('certificates.transfer.edit');
    Route::put('/transfer/{id}', 'TransferCertificateController@update')->name('certificates.transfer.update');
    Route::delete('/transfer/{id}', 'TransferCertificateController@destroy')->name('certificates.transfer.destroy');
    Route::get('/transfer/{id}/pdf', 'TransferCertificateController@generatePdf')->name('certificates.transfer.pdf');
    Route::get('/transfer-search', 'TransferCertificateController@search')->name('certificates.transfer.search');
    Route::get('/transfer-statistics', 'TransferCertificateController@statistics')->name('certificates.transfer.statistics');
    Route::get('/transfer-export', 'TransferCertificateController@export')->name('certificates.transfer.export');
    Route::post('/transfer-bulk-delete', 'TransferCertificateController@bulkDelete')->name('certificates.transfer.bulk-delete');
    Route::post('/transfer-bulk-status', 'TransferCertificateController@bulkStatusUpdate')->name('certificates.transfer.bulk-status');
    Route::post('/transfer-bulk-pdf', 'TransferCertificateController@bulkPdfDownload')->name('certificates.transfer.bulk-pdf');
    Route::get('/transfer-upload', 'TransferCertificateController@uploadForm')->name('certificates.transfer.upload');
    Route::post('/transfer-upload-csv', 'TransferCertificateController@uploadCsv')->name('certificates.transfer.upload.csv');
    Route::get('/transfer-sample', 'TransferCertificateController@downloadSample')->name('certificates.transfer.sample');
});