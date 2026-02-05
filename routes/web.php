<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Route::get('/', function () {
//     return view('welcome');
// });

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
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::get('/register/customer', [RegisteredUserController::class, 'createCustomer'])
    ->name('register.customer');

Route::get('/register/supplier', [RegisteredUserController::class, 'createSupplier'])
    ->name('register.supplier');

Route::post('/register/customer', [RegisteredUserController::class, 'storeCustomer']);
Route::post('/register/supplier', [RegisteredUserController::class, 'storeSupplier']);

require __DIR__.'/auth.php';
