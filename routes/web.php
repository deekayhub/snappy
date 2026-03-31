<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Mail;
// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/test-mail', function () {
//     Mail::raw('This is a test email from Laravel 🚀', function ($message) {
//         $message->to('deekay843424@gmail.com') // 👈 change this
//                 ->subject('Test Email from Laravel');
//     });

//     return 'Test email sent successfully!';
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
    return view('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth',)->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');  
    Route::get('/suppliers', function () {
        return view('admin.suppliers.index');
    })->name('admin.suppliers');
    Route::get('/customers', function () {
        return view('admin.customers.index');
    })->name('admin.customers');
    Route::get('/invoices', function () {
        return view('admin.invoices.index');
    })->name('admin.invoices');
    Route::get('/reports', function () {
        return view('admin.reports.index');
    })->name('admin.reports');
});



Route::get('/register/customer', [RegisteredUserController::class, 'createCustomer'])
    ->name('register.customer');

Route::get('/register/supplier', [RegisteredUserController::class, 'createSupplier'])
    ->name('register.supplier');

Route::post('/register/customer', [RegisteredUserController::class, 'storeCustomer']);
Route::post('/register/supplier', [RegisteredUserController::class, 'storeSupplier']);

require __DIR__.'/auth.php';
