<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('clinic.home');
})->name('clinic.home');

Route::get('/about', function () {
    return view('clinic.about');
})->name('clinic.about');

Route::get('/services', function () {
    return view('clinic.services');
})->name('clinic.services');

Route::get('/contact', function () {
    return view('clinic.contact');
})->name('clinic.contact');

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
