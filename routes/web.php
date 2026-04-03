<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PageSettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', function () {
        return auth()->user()->hasAnyRole(['superadmin', 'admin'])
            ? redirect()->route('admin.dashboard')
            : redirect()->route('home');
    })->name('dashboard');
});

Route::middleware(['auth', 'role:superadmin|admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers');

    Route::get('/invoices', function () {
        return view('admin.invoices.index');
    })->name('invoices');

    Route::get('/reports', function () {
        return view('admin.reports.index');
    })->name('reports');

    Route::get('/page-settings', [PageSettingsController::class, 'index'])
        ->name('page-settings');
    Route::post('/page-settings', [PageSettingsController::class, 'update'])
        ->name('page-settings.update');
});

Route::middleware('guest')->group(function () {
    Route::get('/register/customer', [RegisteredUserController::class, 'createCustomer'])
        ->name('register.customer');

    Route::get('/register/supplier', [RegisteredUserController::class, 'createSupplier'])
        ->name('register.supplier');

    Route::post('/register/customer', [RegisteredUserController::class, 'storeCustomer']);
    Route::post('/register/supplier', [RegisteredUserController::class, 'storeSupplier']);
});

require __DIR__.'/auth.php';
