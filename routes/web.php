<?php

use App\Http\Controllers\EnrollmentBalanceController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PaymongoWebhookController;
use Illuminate\Support\Facades\Route;

Route::controller(EnrollmentController::class)->group(function () {
    Route::get('/', 'landing')->name('home');
    Route::get('/enroll', 'form')->name('enroll.form');
    Route::post('/enroll', 'store')->name('enroll.store');
    Route::get('/enroll/payment', 'payment')->name('enroll.payment');
    Route::post('/enroll/pay', 'pay')->name('enroll.pay');
    Route::get('/enroll/success', 'success')->name('enroll.success');
    Route::get('/enroll/cancel', 'cancel')->name('enroll.cancel');
});

Route::get('/enroll/balance/{reference_number}', [EnrollmentBalanceController::class, 'show'])
    ->middleware('signed')
    ->name('enroll.balance');

Route::post('/enroll/balance/pay', [EnrollmentBalanceController::class, 'pay'])
    ->name('enroll.balance.pay');

Route::post('/webhooks/paymongo', [PaymongoWebhookController::class, 'handle'])
    ->name('webhooks.paymongo');

// Admin panel root redirect
Route::redirect('/admin', '/admin/enrollments');
