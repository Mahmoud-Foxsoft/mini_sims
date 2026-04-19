<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'clinic.home')->name('clinic.home');

Route::view('/about', 'clinic.about')->name('clinic.about');

Route::view('/services', 'clinic.services')->name('clinic.services');

Route::view('/contact', 'clinic.contact')->name('clinic.contact');

Route::view('/privacy', 'clinic.privacy')->name('clinic.privacy');

Route::view('/terms', 'clinic.terms')->name('clinic.terms');

Route::view('/v1/docs', 'clinic.docs')->name('clinic.docs');

Route::view('/dashboard/{any?}', 'user.dashboard')
    ->where('any', '.*')
    ->name('user.dashboard');

Route::view('/admin/{any?}', 'admin.dashboard')
    ->where('any', '.*')
    ->name('admin.dashboard');

    // Route::get('/test', function () {
    //     event(new \App\Events\ServicesUpdated());
    //     return response()->json(['success' => true]);
    // });
