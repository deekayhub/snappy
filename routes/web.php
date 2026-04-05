<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrganisationCategoryController;
use App\Http\Controllers\Admin\QuoteManagementController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerJobController;
use App\Http\Controllers\PageSettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPanelController;
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
    Route::match(['post', 'patch'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/post-job', [CustomerJobController::class, 'create'])->name('customer.jobs.create');
    Route::post('/post-job', [CustomerJobController::class, 'store'])->name('customer.jobs.store');
    Route::get('/customer/quotes', [QuoteController::class, 'customerIndex'])->name('customer.quotes.index');
    Route::patch('/customer/quotes/{quote}/status', [QuoteController::class, 'updateStatus'])->name('customer.quotes.status');

    Route::get('/dashboard', function () {
        return match (true) {
            auth()->user()->hasAnyRole(['superadmin', 'admin']) => redirect()->route('admin.dashboard'),
            auth()->user()->hasRole('supplier') => redirect()->route('supplier-panel.dashboard'),
            auth()->user()->hasRole('customer') => redirect()->route('profile.edit'),
            default => redirect()->route('home'),
        };
    })->name('dashboard');
});

Route::middleware(['auth', 'role:superadmin|admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/jobs', [CustomerJobController::class, 'index'])->name('jobs');
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers');

    Route::get('/invoices', function () {
        return view('admin.invoices.index');
    })->name('invoices');

    Route::get('/reports', function () {
        return view('admin.reports.index');
    })->name('reports');

    Route::get('/quotes', [QuoteManagementController::class, 'index'])->name('quotes');
    Route::get('/categories', [OrganisationCategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [OrganisationCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [OrganisationCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [OrganisationCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/page-settings', [PageSettingsController::class, 'index'])
        ->name('page-settings');
    Route::post('/page-settings', [PageSettingsController::class, 'update'])
        ->name('page-settings.update');
});

Route::middleware(['auth', 'role:superadmin|admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'adminEdit'])->name('profile');
});

Route::middleware(['auth', 'role:supplier'])->prefix('supplier-panel')->name('supplier-panel.')->group(function () {
    Route::get('/dashboard', [SupplierPanelController::class, 'dashboard'])->name('dashboard');
    Route::get('/jobs', [SupplierPanelController::class, 'jobs'])->name('jobs');
    Route::get('/reports', [SupplierPanelController::class, 'reports'])->name('reports');
    Route::get('/activity', [SupplierPanelController::class, 'activity'])->name('activity');
    Route::get('/profile', [SupplierPanelController::class, 'profile'])->name('profile');
    Route::post('/jobs/{job}/quotes', [QuoteController::class, 'store'])->name('quotes.store');
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
