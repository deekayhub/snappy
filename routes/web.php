<?php

use App\Http\Controllers\Admin\CategoryFieldController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrganisationCategoryController;
use App\Http\Controllers\Admin\PageSectionController;
use App\Http\Controllers\Admin\QuoteManagementController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\SubscriptionSettingController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerJobController;
use App\Http\Controllers\CustomerPanelController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPanelController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;







Route::get('/migrate', function () {
    Artisan::call('migrate', ['--force' => true]);
    return 'Migrated';
});
Route::get('/migrate-fresh', function () {
    Artisan::call('migrate:fresh', [
        '--force' => true
    ]);

    return 'Database migrated fresh';
});
Route::get('/db-seed', function () {
    Artisan::call('db:seed', [
        '--force' => true
    ]);

    return 'Database seeded';
});

// Route::get('/test-mail', function () { 
//     $job = CustomerJob::first(); 
//     $quote = Quote::first(); 
//     Mail::to('your-email@example.com')->send(new JobQuoteSubmittedMail($job, $quote)); 
//     return 'Mail sent successfully!'; 
// });

Route::get('/', [HomeController::class, 'index'])->name('home'); 
Route::get('/supplier', [HomeController::class, 'supplier'])->middleware(['auth', 'role:supplier'])->name('supplier');
Route::get('/how-it-work', [HomeController::class, 'howItWork'])->name('how-it-work');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/contact-us', [HomeController::class, 'contactUs'])->name('contact-us');
Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::match(['post', 'patch'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', function () {
        return match (true) {
            auth()->user()->hasAnyRole(['superadmin', 'admin']) => redirect()->route('admin.dashboard'),
            auth()->user()->hasRole('supplier') => redirect()->route('supplier-panel.dashboard'),
            auth()->user()->hasRole('customer') => redirect()->route('customer-panel.dashboard'),
            default => redirect()->route('home'),
        };
    })->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:customer'])->group(function () {
    Route::get('/post-job', [CustomerJobController::class, 'create'])->name('customer.jobs.create');
    Route::post('/post-job', [CustomerJobController::class, 'store'])->name('customer.jobs.store');
    Route::get('/customer/quotes', [CustomerPanelController::class, 'quotes'])->name('customer.quotes.index');
    Route::patch('/customer/quotes/{quote}/status', [QuoteController::class, 'updateStatus'])->name('customer.quotes.status');
    Route::post('/customer/quotes/{quote}/rating', [QuoteController::class, 'rateSupplier'])->name('customer.quotes.rating');
});

Route::middleware(['auth', 'verified', 'role:superadmin|admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/jobs', [CustomerJobController::class, 'index'])->name('jobs');
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
    Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'supplierDestroy'])->name('suppliers.destroy');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
    Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{id}', [CustomerController::class, 'customerDestroy'])->name('customers.destroy');
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
    
    Route::get('/category-fields', [CategoryFieldController::class, 'index'])->name('categories.fields');
    Route::post('category-fields/store', [CategoryFieldController::class, 'store'])->name('category-fields.store');
    Route::get('/category-fields/edit/{id}', [CategoryFieldController::class, 'edit'])->name('categories.fields.edit');
    Route::put('/category-fields/update/{id}', [CategoryFieldController::class, 'update'])->name('categories.fields.update');
    Route::delete('/category-fields/destroy/{id}', [CategoryFieldController::class, 'destroy'])->name('categories.fields.destroy');
    Route::post('/category-fields/duplicate', [CategoryFieldController::class, 'duplicate'])->name('category-fields.duplicate');
    
    Route::get('/page-sections', [PageSectionController::class, 'index'])->name('page-sections');
    Route::post('/page-sections', [PageSectionController::class, 'store'])->name('page-sections.store');
    
    Route::post('/organisation-categories/{category}', [PageSectionController::class, 'organisationCategoryUpdate'])->name('organisation-category.update');
    
    Route::get('/subscription-setting', [SubscriptionSettingController::class, 'index'])->name('subscription.settings');
    Route::post('/subscription/add', [SubscriptionSettingController::class, 'store'])->name('subscription.add');
    Route::put('/subscription/update/{plan}', [SubscriptionSettingController::class, 'update'])->name('subscription.update');
    Route::delete('/subscription/delete/{plan}', [SubscriptionSettingController::class, 'destroy'])->name('subscription.destroy');
    Route::post('/subscription/reorder', [SubscriptionSettingController::class, 'reorder'])->name('subscription.reorder');

    Route::get('/features', [FeatureController::class, 'index'])->name('features.index');
    Route::post('/features', [FeatureController::class, 'store'])->name('features.store');
    Route::put('/features/{feature}', [FeatureController::class, 'update'])->name('features.update');
    Route::delete('/features/{feature}', [FeatureController::class, 'destroy'])->name('features.destroy');
});

Route::middleware(['auth', 'verified', 'role:superadmin|admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'adminEdit'])->name('profile');
});

Route::middleware(['auth', 'verified'])->prefix('subscription')->name('subscription.')->group(function () {
    Route::get('/', [SubscriptionController::class, 'index'])->name('index');
    Route::get('/preview/{plan}', [SubscriptionController::class, 'preview'])->name('preview');
    Route::post('/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('checkout');
    Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
    Route::post('/resume', [SubscriptionController::class, 'resume'])->name('resume');
    Route::get('/success', [SubscriptionController::class, 'success'])->name('success');
    Route::get('/invoices', [SubscriptionController::class, 'invoices'])->name('invoices');
    Route::get('/invoices/{invoice}/download', [SubscriptionController::class, 'downloadInvoice'])->name('invoice.download');
});

Route::middleware(['auth', 'verified', 'role:supplier'])->prefix('supplier-panel')->name('supplier-panel.')->group(function () {
    Route::get('/dashboard', [SupplierPanelController::class, 'dashboard'])->name('dashboard');
    Route::get('/jobs', [SupplierPanelController::class, 'jobs'])->name('jobs');
    Route::get('/won-jobs', [SupplierPanelController::class, 'wonJobs'])->name('won.jobs');
    Route::post('/jobs/{job}/quotes', [QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/reports', [SupplierPanelController::class, 'reports'])->name('reports');
    Route::get('/activity', [SupplierPanelController::class, 'activity'])->name('activity');
    Route::get('/profile', [SupplierPanelController::class, 'profile'])->name('profile');
    Route::get('/subscription', [SupplierPanelController::class, 'subscriptionIndex'])->name('subscription.index');
    Route::get('/subscription/preview/{plan}', [SupplierPanelController::class, 'subscriptionPreview'])->name('subscription.preview');
    Route::post('/subscription/checkout/{plan}', [SupplierPanelController::class, 'subscriptionCheckout'])->name('subscription.checkout');
    Route::post('/subscription/cancel', [SupplierPanelController::class, 'subscriptionCancel'])->name('subscription.cancel');
    Route::post('/subscription/resume', [SupplierPanelController::class, 'subscriptionResume'])->name('subscription.resume');
    Route::get('/subscription/success', [SupplierPanelController::class, 'subscriptionSuccess'])->name('subscription.success');
    Route::get('/subscription/invoices', [SupplierPanelController::class, 'subscriptionInvoices'])->name('subscription.invoices');
    Route::get('/subscription/invoices/{invoice}/download', [SupplierPanelController::class, 'downloadSubscriptionInvoice'])->name('subscription.invoice.download');

    Route::middleware('feature:analytics_dashboard')->group(function () {
        Route::get('/analytics', [SupplierPanelController::class, 'analytics'])->name('analytics');
    });

    Route::middleware('feature:early_access_jobs')->group(function () {
        Route::get('/early-jobs', [SupplierPanelController::class, 'earlyJobs'])->name('early.jobs');
    });

    Route::post('/notification-preferences', [SupplierPanelController::class, 'updateNotificationPreferences'])->name('notification-preferences.update');

    Route::middleware('feature:instant_job_alerts')->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });
});

Route::middleware(['auth', 'verified', 'role:customer'])->prefix('customer-panel')->name('customer-panel.')->group(function () {
    Route::get('/dashboard', [CustomerPanelController::class, 'dashboard'])->name('dashboard');
    Route::get('/suppliers', [CustomerPanelController::class, 'suppliers'])->name('suppliers');
    Route::get('/suppliers-details/{id}', [CustomerPanelController::class, 'suppliersDetails'])->name('suppliers.details');
    Route::get('/jobs', [CustomerPanelController::class, 'jobs'])->name('jobs');
    Route::get('/jobs/create', [CustomerPanelController::class, 'createJob'])->name('jobs.create');
    Route::get('/jobs/{job}', [CustomerPanelController::class, 'showJob'])->name('jobs.show');
    Route::get('/jobs/{job}/edit', [CustomerPanelController::class, 'editJob'])->name('jobs.edit');
    Route::patch('/jobs/{job}', [CustomerPanelController::class, 'updateJob'])->name('jobs.update');
    Route::delete('/jobs/{job}', [CustomerPanelController::class, 'destroyJob'])->name('jobs.destroy');
    Route::post('/jobs', [CustomerPanelController::class, 'store'])->name('jobs.store');
    Route::get('/get-category-fields/{id}', [CustomerPanelController::class, 'getCategoryFields'])->name('get.category.fields');
    Route::get('/quotes', [CustomerPanelController::class, 'quotes'])->name('quotes');
    Route::get('/profile', [CustomerPanelController::class, 'profile'])->name('profile');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'customerIndex'])->name('index');
        Route::post('/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'customerMarkAsRead'])->name('read');
        Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'customerMarkAllAsRead'])->name('mark-all-read');
    });
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
