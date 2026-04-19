<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\HomeController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\OrderItemsController;
use App\Http\Controllers\Api\Admin\PaymentController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\TransactionController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\MessagesWebhookController;
use App\Http\Controllers\Api\NowPaymentsWebhookController;
use App\Http\Controllers\Api\ServicesWebhookController;
use App\Http\Controllers\Api\User\ServicesController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::group(["middleware" => ['auth:admin', 'scope:admin-api']], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'getProfile']);
        Route::post('refresh-token', [AuthController::class, 'refreshToken']);
        // Admin management
        Route::apiResource('admins', AdminController::class)->except(['show']);

        // User management
        Route::apiResource('users', UserController::class)->only(['index', 'update']);

        // Payment management
        Route::apiResource('payments', PaymentController::class);

        // Settings management
        Route::get('settings', [SettingController::class, 'index']);
        Route::put('settings/{setting}', [SettingController::class, 'update']);

        Route::get('services', ServicesController::class);
        
        Route::get('home', HomeController::class);
        Route::get('reports', ReportController::class);

        Route::get('phone-numbers', [OrderItemsController::class, 'index']);
        Route::delete('phone-numbers/delete', [OrderItemsController::class, 'bulkDelete']);
        
        Route::get('transactions', [TransactionController::class, 'index']);

        Route::delete('contact-messages/deleteAll', [ContactController::class, 'deleteAllMessages']);
        Route::apiResource('contact-messages', ContactController::class)->except(['update', 'store']);

        Route::get('orders', [OrderController::class, 'index']);
    });
});

Route::post('contact', [ContactController::class, 'store'])->name('contact.send');

Route::post('payments/webhook', [NowPaymentsWebhookController::class, 'handle'])->name('nowpayments.webhook');
Route::post('sms/webhook', [MessagesWebhookController::class, 'handle'])->name('messages.webhook');
Route::post('services/webhook', [ServicesWebhookController::class, 'handle'])->name('messages.webhook');
