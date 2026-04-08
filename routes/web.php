<?php

use App\Http\Services\PhoneServiceService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // $services = collect(PhoneServiceService::getPhoneServices())
    //     ->take(6)
    //     ->map(function ($service) {
    //         return [
    //             'name' => data_get($service, 'name'),
    //             'price' => data_get($service, 'price'),
    //         ];
    //     })
    //     ->all();

    return view('clinic.home', ['services' => []]);
})->name('clinic.home');

Route::get('/about', function () {
    return view('clinic.about');
})->name('clinic.about');

Route::get('/services', function () {
    // $services = collect(PhoneServiceService::getPhoneServices())
    //     ->take(6)
    //     ->map(function ($service) {
    //         return [
    //             'name' => data_get($service, 'name'),
    //             'price' => data_get($service, 'price'),
    //         ];
    //     })
    //     ->all();

    return view('clinic.services', ['services' => []]);
})->name('clinic.services');

Route::get('/contact', function () {
    return view('clinic.contact');
})->name('clinic.contact');

Route::get('/privacy', function () {
    return view('clinic.privacy');
})->name('clinic.privacy');

Route::get('/terms', function () {
    return view('clinic.terms');
})->name('clinic.terms');

Route::get('/v1/docs', function () {
    return view('clinic.docs');
})->name('clinic.docs');

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
