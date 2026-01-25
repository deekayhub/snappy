<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');
Route::get('/supplier', function () {
    return view('supplier');
})->name('supplier');
Route::get('/how-it-work', function () {
    return view('how-it-work');
})->name('how-it-work');
Route::get('/faq', function () {
    return view('faq');
})->name('faq');
Route::get('/contact-us', function () {
    return view('contact-us');
})->name('contact-us');