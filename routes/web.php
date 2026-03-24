<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PaymongoWebhookController;

Route::controller(EnrollmentController::class)->group(function () {
    Route::get('/', 'landing')->name('home');
    Route::get('/enroll', 'form')->name('enroll.form');
    Route::post('/enroll', 'store')->name('enroll.store');
    Route::get('/enroll/payment', 'payment')->name('enroll.payment');
    Route::post('/enroll/pay', 'pay')->name('enroll.pay');
    Route::get('/enroll/success', 'success')->name('enroll.success');
    Route::get('/enroll/cancel', 'cancel')->name('enroll.cancel');
});

Route::post('/webhooks/paymongo', [PaymongoWebhookController::class, 'handle'])
    ->name('webhooks.paymongo');

// Admin panel root redirect
Route::redirect('/admin', '/admin/enrollments');
