<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\StudentPanel\StudentController;
use App\Http\Controllers\StudentPanel\DocumentController;

Route::get('/student-login', 'StudentAuthController@showLoginForm')->name('student.login');
Route::post('/student-login', 'StudentAuthController@login');

Route::post('/student-logout', 'StudentAuthController@logout')->name('student.logout');

Route::get('/register-student', 'StudentAuthController@showRegistrationForm')->name('student.register');
Route::post('/register-student', 'StudentAuthController@register');

Route::get('/student/dashboard', 'StudentPanel\StudentController@dashboard')->name('student.dashboard');

Route::get('/student/profile', 'StudentPanel\StudentController@profile')->name('student.profile');
Route::get('/student/edit/profile', 'StudentPanel\StudentController@editProfile')->name('student.edit.profile');
Route::get('/student/change-password', 'StudentPanel\StudentController@changePassword')->name('student.change.password');


Route::middleware(['auth:student'])->prefix('student')->name('student.')->group(function () {   
    Route::get('/document', 'StudentPanel\DocumentController@documents')->name('document.index');
    Route::get('/document/{id}/show', 'StudentPanel\DocumentController@showDocument')->name('document.show');
    Route::get('/document/{id}/download', 'StudentPanel\DocumentController@downloadDocument')->name('document.download');
    // Payment routes
    Route::get('/payment/{documentTypeId}', [DocumentController::class, 'createPayment'])->name('payment.create');
    Route::post('/payment/{documentTypeId}', [DocumentController::class, 'confirmPayment'])->name('payment.store');
    Route::get('/payment-history', [DocumentController::class, 'paymentHistory'])->name('payment.history');
});