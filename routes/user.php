<?php

use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\HomeController;
use App\Http\Controllers\Api\User\NowPaymentController;
use App\Http\Controllers\Api\User\OrderItemsController;
use App\Http\Controllers\Api\User\OrdersController;
use App\Http\Controllers\Api\User\PaymentController;
use App\Http\Controllers\Api\User\ServicesController;
use App\Http\Controllers\Api\User\TransactionController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('verify-email', [AuthController::class, 'verifyEmail']);
Route::post('resend-otp', [AuthController::class, 'resendOtp']);

Route::group(['middleware' => ['auth:api', 'scopes:user-api']], function () {
    // User authentication routes
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('api-key', [AuthController::class, 'rotateApiKey']);

    Route::get('home', HomeController::class);

    // User management
    Route::put('me', UserController::class);

    // Payments
    Route::post('payments', [PaymentController::class, 'store']);
    Route::get('payments/currencies', [NowPaymentController::class, 'getCurrencies']);
    Route::post('payments/estimate', [NowPaymentController::class, 'estimate']);
});

Route::prefix('v1')->group(function () {
    Route::group(['middleware' => ['auth:api', 'scopesAny:external-api,user-api']], function () {
        Route::get('me', [AuthController::class, 'getUserProfile']);

        Route::apiResource('orders', OrdersController::class)->only(['index', 'store']);
        Route::apiResource('phone-numbers', OrderItemsController::class)->only(['index']);
        Route::post('phone-numbers/{orderItem}/cancel', [OrderItemsController::class, 'cancel']);
        Route::post('phone-numbers/{orderItem}/reuse', [OrderItemsController::class, 'reuse']);
        // Transactions
        Route::get('transactions', [TransactionController::class, 'index']);
        Route::get('payments', [PaymentController::class, 'index']);
        Route::get('services', ServicesController::class);
    });
});
