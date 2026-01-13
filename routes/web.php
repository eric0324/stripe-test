<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PaymentController::class, 'index']);
Route::post('/payment/intent', [PaymentController::class, 'createPaymentIntent']);
Route::post('/payment/subscription', [PaymentController::class, 'createSubscription']);
Route::post('/payment/coupon', [PaymentController::class, 'applyCoupon']);
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::post('/webhook/stripe', [PaymentController::class, 'webhook']);
