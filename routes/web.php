<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\DoctorDashboardController;
use App\Http\Controllers\Dashboard\StoreDashboardController;
use App\Http\Controllers\Store\ReportController as StoreReportController;
use App\Http\Controllers\Dashboard\UserDashboardController;
use App\Http\Controllers\Doctor\SpinController;
use App\Http\Controllers\Doctor\ReportController as DoctorReportController;
use App\Http\Controllers\Doctor\TargetController as DoctorTargetController;
use App\Http\Controllers\OrderController as UserOrderController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\Admin\ReferralAuditController;
use App\Http\Controllers\Api\PincodeController as ApiPincodeController;
use App\Http\Controllers\Admin\SalesReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TerritoryController;
use App\Http\Controllers\Admin\DoctorApprovalController;
use App\Http\Controllers\Admin\StoreApprovalController;
use App\Http\Controllers\MR\MRStoreController;
use App\Http\Controllers\RoleRequestController;
use Illuminate\Support\Facades\RateLimiter;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rate limiter configuration
RateLimiter::for('login', function ($request) {
    return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
});

RateLimiter::for('api', function ($request) {
    return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

// Prescription access rate limiting (medical data protection)
RateLimiter::for('prescription', function ($request) {
    return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
});

// Order creation rate limiting
RateLimiter::for('orders', function ($request) {
    return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
});

// Admin action rate limiting
RateLimiter::for('admin', function ($request) {
    return \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
});

// Spin action rate limiting (prevent duplicate spins)
RateLimiter::for('spin', function ($request) {
    return \Illuminate\Cache\RateLimiting\Limit::perMinute(2)->by($request->user()?->id ?: $request->ip());
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['guest', 'throttle:login'])->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Public API Routes (Accessible without role restrictions)
|--------------------------------------------------------------------------
*/

// PIN Code Lookup API (for MR Doctor Registration and other forms)
Route::get('/api/pincode/{pin}', [ApiPincodeController::class, 'lookup'])->name('api.pincode.lookup');

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Default dashboard for End Users
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    
    // User Offers Page (module check)
    Route::get('/offers', [\App\Http\Controllers\OfferController::class, 'index'])
        ->name('offers.index')
        ->middleware('module:offers');
});

// Admin Routes (accessible by Admin and Super Admin)
Route::middleware(['auth', 'role:Admin|Super Admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Create Store (uses same form/controller as MR)
    Route::get('/stores/create', [MRStoreController::class, 'create'])->name('stores.create');
    
    // Old Website Settings (kept for backward compat)
    Route::get('site-settings', [\App\Http\Controllers\Admin\SiteSettingController::class, 'edit'])->name('site-settings.edit');
    Route::put('site-settings', [\App\Http\Controllers\Admin\SiteSettingController::class, 'update'])->name('site-settings.update');

    // Homepage CMS Manager
    Route::get('homepage-manager', [\App\Http\Controllers\Admin\HomepageController::class, 'index'])->name('homepage-manager.index');
    Route::get('homepage-manager/{section}/edit', [\App\Http\Controllers\Admin\HomepageController::class, 'edit'])->name('homepage-manager.edit');
    Route::put('homepage-manager/{section}', [\App\Http\Controllers\Admin\HomepageController::class, 'update'])->name('homepage-manager.update');
    Route::post('homepage-manager/{section}/toggle', [\App\Http\Controllers\Admin\HomepageController::class, 'toggle'])->name('homepage-manager.toggle');
    Route::post('homepage-manager/reorder', [\App\Http\Controllers\Admin\HomepageController::class, 'reorder'])->name('homepage-manager.reorder');
    Route::post('homepage-manager/branding', [\App\Http\Controllers\Admin\HomepageController::class, 'updateBranding'])->name('homepage-manager.branding');

    // Homepage Features (Platform Features section)
    Route::get('homepage-features', [\App\Http\Controllers\Admin\HomepageFeatureController::class, 'index'])->name('homepage-features.index');
    Route::get('homepage-features/create', [\App\Http\Controllers\Admin\HomepageFeatureController::class, 'create'])->name('homepage-features.create');
    Route::post('homepage-features', [\App\Http\Controllers\Admin\HomepageFeatureController::class, 'store'])->name('homepage-features.store');
    Route::get('homepage-features/{homepage_feature}/edit', [\App\Http\Controllers\Admin\HomepageFeatureController::class, 'edit'])->name('homepage-features.edit');
    Route::put('homepage-features/{homepage_feature}', [\App\Http\Controllers\Admin\HomepageFeatureController::class, 'update'])->name('homepage-features.update');
    Route::delete('homepage-features/{homepage_feature}', [\App\Http\Controllers\Admin\HomepageFeatureController::class, 'destroy'])->name('homepage-features.destroy');
    Route::post('homepage-features/{homepage_feature}/toggle', [\App\Http\Controllers\Admin\HomepageFeatureController::class, 'toggle'])->name('homepage-features.toggle');
    Route::post('homepage-features/reorder', [\App\Http\Controllers\Admin\HomepageFeatureController::class, 'reorder'])->name('homepage-features.reorder');

    // Homepage Navigation Items
    Route::get('homepage-nav', [\App\Http\Controllers\Admin\HomepageNavController::class, 'index'])->name('homepage-nav.index');
        Route::get('homepage-nav/create', [\App\Http\Controllers\Admin\HomepageNavController::class, 'create'])->name('homepage-nav.create');
        Route::post('homepage-nav', [\App\Http\Controllers\Admin\HomepageNavController::class, 'store'])->name('homepage-nav.store');
        Route::get('homepage-nav/{homepage_nav}/edit', [\App\Http\Controllers\Admin\HomepageNavController::class, 'edit'])->name('homepage-nav.edit');
        Route::put('homepage-nav/{homepage_nav}', [\App\Http\Controllers\Admin\HomepageNavController::class, 'update'])->name('homepage-nav.update');
        Route::delete('homepage-nav/{homepage_nav}', [\App\Http\Controllers\Admin\HomepageNavController::class, 'destroy'])->name('homepage-nav.destroy');
        Route::post('homepage-nav/{homepage_nav}/toggle', [\App\Http\Controllers\Admin\HomepageNavController::class, 'toggle'])->name('homepage-nav.toggle');
        Route::post('homepage-nav/reorder', [\App\Http\Controllers\Admin\HomepageNavController::class, 'reorder'])->name('homepage-nav.reorder');

    // Media Library
    Route::get('media-library', [\App\Http\Controllers\Admin\MediaLibraryController::class, 'index'])->name('media-library.index');
        Route::post('media-library', [\App\Http\Controllers\Admin\MediaLibraryController::class, 'store'])->name('media-library.store');
        Route::put('media-library/{media}', [\App\Http\Controllers\Admin\MediaLibraryController::class, 'update'])->name('media-library.update');
        Route::delete('media-library/{media}', [\App\Http\Controllers\Admin\MediaLibraryController::class, 'destroy'])->name('media-library.destroy');
        Route::get('media-library/select', [\App\Http\Controllers\Admin\MediaLibraryController::class, 'select'])->name('media-library.select');

    // Homepage Slides (Image Slider CMS)
    Route::get('homepage-slides', [\App\Http\Controllers\Admin\HomepageSlideController::class, 'index'])->name('homepage-slides.index');
    Route::get('homepage-slides/create', [\App\Http\Controllers\Admin\HomepageSlideController::class, 'create'])->name('homepage-slides.create');
    Route::post('homepage-slides', [\App\Http\Controllers\Admin\HomepageSlideController::class, 'store'])->name('homepage-slides.store');
    Route::get('homepage-slides/{homepage_slide}/edit', [\App\Http\Controllers\Admin\HomepageSlideController::class, 'edit'])->name('homepage-slides.edit');
    Route::put('homepage-slides/{homepage_slide}', [\App\Http\Controllers\Admin\HomepageSlideController::class, 'update'])->name('homepage-slides.update');
    Route::delete('homepage-slides/{homepage_slide}', [\App\Http\Controllers\Admin\HomepageSlideController::class, 'destroy'])->name('homepage-slides.destroy');
    Route::post('homepage-slides/{homepage_slide}/toggle', [\App\Http\Controllers\Admin\HomepageSlideController::class, 'toggle'])->name('homepage-slides.toggle');
    Route::post('homepage-slides/reorder', [\App\Http\Controllers\Admin\HomepageSlideController::class, 'reorder'])->name('homepage-slides.reorder');

    // Homepage Preview
    Route::get('homepage-preview', [\App\Http\Controllers\Admin\HomepageController::class, 'preview'])->name('homepage-preview');

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Users Management
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::get('users/trash', [UserController::class, 'trash'])->name('users.trash');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    
    // Products Management
    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    
    // Orders Management
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
    Route::post('orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
    Route::post('orders/{order}/deliver', [OrderController::class, 'deliver'])->name('orders.deliver');
    
    // Store Cancellation Request Management
    Route::get('cancellation-requests', [OrderController::class, 'cancellationRequests'])->name('cancellation-requests.index');
    Route::post('cancellation-requests/{request}/approve', [OrderController::class, 'approveCancellationRequest'])->name('cancellation-requests.approve');
    Route::post('cancellation-requests/{request}/reject', [OrderController::class, 'rejectCancellationRequest'])->name('cancellation-requests.reject');
    
    // Bill Generation
    Route::get('orders/{order}/generate-bill', [OrderController::class, 'generateBill'])->name('orders.generate-bill');
    Route::get('orders/{order}/view-bill', [OrderController::class, 'viewBill'])->name('orders.view-bill');
    Route::get('orders/{order}/download-bill', [OrderController::class, 'downloadBill'])->name('orders.download-bill');
    
    // Rewards Management
    Route::resource('rewards', RewardController::class);
    Route::patch('rewards/{reward}/toggle-status', [RewardController::class, 'toggleStatus'])->name('rewards.toggle-status');
    
    // Reports
    Route::get('reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/doctors', [ReportController::class, 'doctors'])->name('reports.doctors');
    Route::get('reports/stores', [ReportController::class, 'stores'])->name('reports.stores');
    
    // Report Exports
    Route::get('reports/sales/export/pdf', [ReportController::class, 'exportSalesPdf'])->name('reports.sales.export.pdf');
    Route::get('reports/sales/export/excel', [ReportController::class, 'exportSalesExcel'])->name('reports.sales.export.excel');
    Route::get('reports/doctors/export/pdf', [ReportController::class, 'exportDoctorsPdf'])->name('reports.doctors.export.pdf');
    Route::get('reports/doctors/export/excel', [ReportController::class, 'exportDoctorsExcel'])->name('reports.doctors.export.excel');
    Route::get('reports/stores/export/pdf', [ReportController::class, 'exportStoresPdf'])->name('reports.stores.export.pdf');
    Route::get('reports/stores/export/excel', [ReportController::class, 'exportStoresExcel'])->name('reports.stores.export.excel');
    
    // Grand Lucky Draw Management
    Route::get('grand-draw', [\App\Http\Controllers\Admin\GrandDrawController::class, 'index'])->name('grand-draw.index');
    Route::post('grand-draw/run', [\App\Http\Controllers\Admin\GrandDrawController::class, 'runDraw'])->name('grand-draw.run');
    Route::get('grand-draw/history', [\App\Http\Controllers\Admin\GrandDrawController::class, 'history'])->name('grand-draw.history');
    
    // Store Stock Monitoring
    Route::get('store-stock', [\App\Http\Controllers\Admin\StoreStockController::class, 'index'])->name('store-stock.index');
    Route::get('store-stock/{store}', [\App\Http\Controllers\Admin\StoreStockController::class, 'show'])->name('store-stock.show');
    
    // Referral Audit
    Route::get('referrals/audit', [ReferralAuditController::class, 'index'])->name('referrals.audit');
    
    // Sales by Doctor/Store Report
    Route::get('reports/sales-by-entity', [SalesReportController::class, 'index'])->name('reports.sales-by-entity');
    
    // Settings
    Route::get('settings/spin', [SettingsController::class, 'spinSettings'])->name('settings.spin');
    Route::post('settings/spin', [SettingsController::class, 'updateSpinSettings'])->name('settings.spin.update');
    
    // Activity Logs
    Route::get('activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    
    // Spin Control (Admin assigns specific rewards to doctors)
    Route::get('spin-control', [\App\Http\Controllers\Admin\SpinControlController::class, 'index'])->name('spin-control.index');
    Route::post('spin-control', [\App\Http\Controllers\Admin\SpinControlController::class, 'store'])->name('spin-control.store');
    Route::delete('spin-control/{override}', [\App\Http\Controllers\Admin\SpinControlController::class, 'destroy'])->name('spin-control.destroy');
    
    // Spin Campaigns (Global fixed reward for all doctors)
    Route::get('spin-campaigns', [\App\Http\Controllers\Admin\SpinCampaignController::class, 'index'])->name('spin-campaigns.index');
    Route::post('spin-campaigns', [\App\Http\Controllers\Admin\SpinCampaignController::class, 'store'])->name('spin-campaigns.store');
    Route::put('spin-campaigns/{campaign}', [\App\Http\Controllers\Admin\SpinCampaignController::class, 'update'])->name('spin-campaigns.update');
    Route::delete('spin-campaigns/{campaign}', [\App\Http\Controllers\Admin\SpinCampaignController::class, 'destroy'])->name('spin-campaigns.destroy');
    
    // Grand Spin Rewards Management
    Route::resource('grand-spin-rewards', \App\Http\Controllers\Admin\GrandSpinRewardController::class);
    Route::patch('grand-spin-rewards/{reward}/toggle-status', [\App\Http\Controllers\Admin\GrandSpinRewardController::class, 'toggleStatus'])->name('grand-spin-rewards.toggle-status');
    
    // Offers Management
    Route::resource('offers', \App\Http\Controllers\Admin\OfferController::class);
    Route::patch('offers/{offer}/toggle', [\App\Http\Controllers\Admin\OfferController::class, 'toggle'])->name('offers.toggle');
    
    // MR Doctor Approval Workflow
    Route::prefix('doctors')->name('doctors.')->group(function () {
        Route::get('approval', [DoctorApprovalController::class, 'index'])->name('approval.index');
        Route::get('approval/{doctor}', [DoctorApprovalController::class, 'show'])->name('approval.show');
        Route::post('approval/{doctor}/approve', [DoctorApprovalController::class, 'approve'])->name('approval.approve');
        Route::post('approval/{doctor}/reject', [DoctorApprovalController::class, 'reject'])->name('approval.reject');
        Route::post('approval/{doctor}/deactivate', [DoctorApprovalController::class, 'deactivate'])->name('approval.deactivate');
        Route::post('approval/{doctor}/reactivate', [DoctorApprovalController::class, 'reactivate'])->name('approval.reactivate');
    });
    
    // MR Store Approval Workflow
    Route::prefix('stores')->name('stores.')->group(function () {
        Route::get('approval', [StoreApprovalController::class, 'index'])->name('approval.index');
        Route::get('approval/{store}', [StoreApprovalController::class, 'show'])->name('approval.show');
        Route::post('approval/{store}/approve', [StoreApprovalController::class, 'approve'])->name('approval.approve');
        Route::post('approval/{store}/reject', [StoreApprovalController::class, 'reject'])->name('approval.reject');
        Route::post('approval/{store}/deactivate', [StoreApprovalController::class, 'deactivate'])->name('approval.deactivate');
        Route::post('approval/{store}/reactivate', [StoreApprovalController::class, 'reactivate'])->name('approval.reactivate');
        
        // Admin Create Store (uses same form/controller as MR)
        Route::get('create', [MRStoreController::class, 'create'])->name('create');
        Route::post('store', [MRStoreController::class, 'store'])->name('store');
    });
    
    // Territory Management
    Route::prefix('territory')->name('territory.')->group(function () {
        // States
        Route::get('states', [TerritoryController::class, 'states'])->name('states');
        Route::post('states', [TerritoryController::class, 'storeState'])->name('states.store');
        Route::put('states/{state}', [TerritoryController::class, 'updateState'])->name('states.update');
        Route::delete('states/{state}', [TerritoryController::class, 'destroyState'])->name('states.destroy');
        
        // Districts
        Route::get('districts', [TerritoryController::class, 'districts'])->name('districts');
        Route::post('districts', [TerritoryController::class, 'storeDistrict'])->name('districts.store');
        Route::put('districts/{district}', [TerritoryController::class, 'updateDistrict'])->name('districts.update');
        Route::delete('districts/{district}', [TerritoryController::class, 'destroyDistrict'])->name('districts.destroy');
        
        // Cities
        Route::get('cities', [TerritoryController::class, 'cities'])->name('cities');
        Route::post('cities', [TerritoryController::class, 'storeCity'])->name('cities.store');
        Route::put('cities/{city}', [TerritoryController::class, 'updateCity'])->name('cities.update');
        Route::delete('cities/{city}', [TerritoryController::class, 'destroyCity'])->name('cities.destroy');
        Route::get('states/{state}/districts', [TerritoryController::class, 'getDistrictsByState'])->name('states.districts');
        
        // Areas
        Route::get('areas', [TerritoryController::class, 'areas'])->name('areas');
        Route::post('areas', [TerritoryController::class, 'storeArea'])->name('areas.store');
        Route::put('areas/{area}', [TerritoryController::class, 'updateArea'])->name('areas.update');
        Route::delete('areas/{area}', [TerritoryController::class, 'destroyArea'])->name('areas.destroy');
        Route::get('districts/{district}/cities', [TerritoryController::class, 'getCitiesByDistrict'])->name('districts.cities');
    });
});

// Leaderboard Routes (Public/Authenticated)
Route::middleware(['auth'])->prefix('leaderboard')->name('leaderboard.')->group(function () {
    Route::get('/monthly', [\App\Http\Controllers\LeaderboardController::class, 'monthly'])->name('monthly');
    Route::get('/all-time', [\App\Http\Controllers\LeaderboardController::class, 'allTime'])->name('all-time');
});

// Doctor Routes
Route::middleware(['auth', 'role:Doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders/export', [DoctorDashboardController::class, 'exportOrders'])->name('orders.export');
    
    // Referrals
    Route::get('/referrals', [DoctorDashboardController::class, 'referrals'])->name('referrals');
    
    // Targets
    Route::get('targets', [DoctorTargetController::class, 'index'])->name('targets.index');
    
    // Spin & Rewards (with rate limiting and module check)
    Route::middleware('module:spin')->group(function () {
        Route::get('spin', [SpinController::class, 'index'])->name('spin.index');
        Route::post('spin', [SpinController::class, 'spin'])->name('spin.spin')->middleware('throttle:spin');
        Route::get('spin/history', [SpinController::class, 'history'])->name('spin.history');
        Route::post('spin/{spin}/claim', [SpinController::class, 'claim'])->name('spin.claim');
    });
    
    // Reports
    Route::get('reports/performance', [DoctorReportController::class, 'performance'])->name('reports.performance');
    Route::get('reports/referral-sales', [DoctorReportController::class, 'referralSales'])->name('reports.referral-sales');
});

// Store Routes
Route::middleware(['auth', 'role:Store'])->prefix('store')->name('store.')->group(function () {
    Route::get('/dashboard', [StoreDashboardController::class, 'index'])->name('dashboard');
    
    // Stock Management
    Route::get('stock', [\App\Http\Controllers\Store\StockController::class, 'index'])->name('stock.index');
    Route::post('stock/{stock}/record-sale', [\App\Http\Controllers\Store\StockController::class, 'recordSale'])->name('stock.record-sale');
    Route::get('bulk-sale', [\App\Http\Controllers\Store\StockController::class, 'bulkSaleForm'])->name('bulk-sale.form');
    Route::post('bulk-sale', [\App\Http\Controllers\Store\StockController::class, 'storeBulkSale'])->name('bulk-sale.store');
    Route::get('stock-register', [\App\Http\Controllers\Store\StockRegisterController::class, 'index'])->name('stock-register');
    
    // Store Orders (My Orders)
    Route::get('orders', [\App\Http\Controllers\Store\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\Store\OrderController::class, 'show'])->name('orders.show');
    
    // Store Order Cancellation
    Route::post('orders/{order}/cancel', [\App\Http\Controllers\Store\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('orders/{order}/request-cancel', [\App\Http\Controllers\Store\OrderController::class, 'requestCancelForm'])->name('orders.request-cancel');
    Route::post('orders/{order}/submit-cancel-request', [\App\Http\Controllers\Store\OrderController::class, 'submitCancelRequest'])->name('orders.submit-cancel-request');
    
    // Referrals
    Route::get('referrals', [StoreDashboardController::class, 'referrals'])->name('referrals');
    
    // Reports
    Route::get('reports/sales', [StoreReportController::class, 'sales'])->name('reports.sales');
    
    // Store Offers (module check)
    Route::get('offers', [\App\Http\Controllers\Store\OfferController::class, 'index'])
        ->name('offers.index')
        ->middleware('module:offers');
});

// Product Catalog (accessible by Doctor and End User only)
Route::middleware(['auth', 'role:Doctor|End User'])->get('/products', function () {
    $products = \App\Models\Product::where('status', 'active')->paginate(12);
    return view('products.catalog', compact('products'));
})->name('products.catalog');

// Cart Routes (accessible by Doctor and End User only)
Route::middleware(['auth', 'role:Doctor|End User'])->group(function () {
    Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{item}', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/cart/update/{item}', [\App\Http\Controllers\CartController::class, 'updateQuantity'])->name('cart.update');
    Route::delete('/cart/clear', [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
});

// Wishlist Routes (accessible by Doctor and End User only)
Route::middleware(['auth', 'role:Doctor|End User'])->group(function () {
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add/{product}', [\App\Http\Controllers\WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{product}', [\App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/move-to-cart/{product}', [\App\Http\Controllers\WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');
});

// Order Routes (accessible by all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('orders', [UserOrderController::class, 'index'])->name('orders.index');
    
    // Redirect Doctor and End User to product catalog instead of manual order form
    Route::get('orders/create', function () {
        if (auth()->user()->hasAnyRole(['Doctor', 'End User'])) {
            return redirect()->route('products.catalog');
        }
        return app(\App\Http\Controllers\OrderController::class)->create(request());
    })->name('orders.create');
    
    Route::post('orders', [UserOrderController::class, 'store'])->name('orders.store')->middleware('throttle:orders');
    Route::get('orders/{order}', [UserOrderController::class, 'show'])->name('orders.show');
    
    // Cancel Order - End User Only (only pending orders)
    Route::post('orders/{order}/cancel', [UserOrderController::class, 'cancel'])
        ->name('orders.cancel')
        ->middleware(['role:End User']);
    
    // Prescription download/view routes (protected with rate limiting)
    Route::middleware('throttle:prescription')->group(function () {
        Route::get('orders/{order}/prescription/download', [PrescriptionController::class, 'download'])->name('orders.prescription.download');
        Route::get('orders/{order}/prescription/view', [PrescriptionController::class, 'view'])->name('orders.prescription.view');
    });
});

// Profile Routes (accessible by all authenticated users)
Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [\App\Http\Controllers\ProfileController::class, 'update'])->name('update');
    Route::get('/password', [\App\Http\Controllers\ProfileController::class, 'password'])->name('password');
    Route::put('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');
});

// Role Request Routes (End Users only)
Route::middleware(['auth', 'role:End User'])->prefix('role-requests')->name('role-requests.')->group(function () {
  Route::get('/create', [RoleRequestController::class, 'create'])->name('create');
  Route::post('/store', [RoleRequestController::class, 'store'])->name('store');
});

/*
|--------------------------------------------------------------------------
| Super Admin CMS Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Super Admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

    // System Settings
    Route::get('/settings', [\App\Http\Controllers\SuperAdmin\SystemSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\SuperAdmin\SystemSettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/toggle', [\App\Http\Controllers\SuperAdmin\SystemSettingController::class, 'toggle'])->name('settings.toggle');

    // Modules
    Route::get('/modules', [\App\Http\Controllers\SuperAdmin\ModuleController::class, 'index'])->name('modules.index');
    Route::post('/modules/{module}/toggle', [\App\Http\Controllers\SuperAdmin\ModuleController::class, 'toggle'])->name('modules.toggle');

    // CMS Pages
    Route::resource('cms-pages', \App\Http\Controllers\SuperAdmin\CmsPageController::class);
    Route::post('/cms-pages/{cms_page}/toggle', [\App\Http\Controllers\SuperAdmin\CmsPageController::class, 'toggle'])->name('cms-pages.toggle');

    // UI Settings
    Route::get('/ui-settings', [\App\Http\Controllers\SuperAdmin\UiSettingController::class, 'index'])->name('ui-settings.index');
    Route::put('/ui-settings', [\App\Http\Controllers\SuperAdmin\UiSettingController::class, 'update'])->name('ui-settings.update');

    // Dashboard Widgets
    Route::get('/widgets', [\App\Http\Controllers\SuperAdmin\DashboardWidgetController::class, 'index'])->name('widgets.index');
    Route::post('/widgets', [\App\Http\Controllers\SuperAdmin\DashboardWidgetController::class, 'update'])->name('widgets.update');
    Route::post('/widgets/{widget}/toggle', [\App\Http\Controllers\SuperAdmin\DashboardWidgetController::class, 'toggle'])->name('widgets.toggle');

    // Notification Templates
    Route::resource('notifications', \App\Http\Controllers\SuperAdmin\NotificationTemplateController::class);
    Route::post('/notifications/{notification_template}/toggle', [\App\Http\Controllers\SuperAdmin\NotificationTemplateController::class, 'toggle'])->name('notifications.toggle');

    // Feature Flags
    Route::get('/feature-flags', [\App\Http\Controllers\SuperAdmin\FeatureFlagController::class, 'index'])->name('feature-flags.index');
    Route::post('/feature-flags', [\App\Http\Controllers\SuperAdmin\FeatureFlagController::class, 'update'])->name('feature-flags.update');
    Route::post('/feature-flags/{feature_flag}/toggle', [\App\Http\Controllers\SuperAdmin\FeatureFlagController::class, 'toggle'])->name('feature-flags.toggle');

    // Admin Management
    Route::get('/admins', [\App\Http\Controllers\SuperAdmin\AdminManagementController::class, 'index'])->name('admins.index');
    Route::get('/admins/create', [\App\Http\Controllers\SuperAdmin\AdminManagementController::class, 'create'])->name('admins.create');
    Route::post('/admins', [\App\Http\Controllers\SuperAdmin\AdminManagementController::class, 'store'])->name('admins.store');
    Route::get('/admins/{user}/edit', [\App\Http\Controllers\SuperAdmin\AdminManagementController::class, 'edit'])->name('admins.edit');
    Route::put('/admins/{user}', [\App\Http\Controllers\SuperAdmin\AdminManagementController::class, 'update'])->name('admins.update');
    Route::delete('/admins/{user}', [\App\Http\Controllers\SuperAdmin\AdminManagementController::class, 'destroy'])->name('admins.destroy');
    Route::post('/admins/{user}/toggle-status', [\App\Http\Controllers\SuperAdmin\AdminManagementController::class, 'toggleStatus'])->name('admins.toggle-status');
    Route::post('/admins/{user}/reset-password', [\App\Http\Controllers\SuperAdmin\AdminManagementController::class, 'resetPassword'])->name('admins.reset-password');
    
    // Role Requests (Admin/ Super Admin only)
    Route::prefix('role-requests')->name('role-requests.')->group(function () {
    Route::get('/', [RoleRequestController::class, 'adminIndex'])->name('index');
    Route::post('/{roleRequest}/approve', [RoleRequestController::class, 'approve'])->name('approve');
    Route::post('/{roleRequest}/reject', [RoleRequestController::class, 'reject'])->name('reject');
    });

    // Homepage CMS
    Route::get('/homepage-cms', [\App\Http\Controllers\SuperAdmin\HomepageCmsController::class, 'index'])->name('homepage-cms.index');
    Route::get('/homepage-cms/site-settings', [\App\Http\Controllers\SuperAdmin\HomepageCmsController::class, 'siteSettings'])->name('homepage-cms.site-settings');
    Route::put('/homepage-cms/site-settings', [\App\Http\Controllers\SuperAdmin\HomepageCmsController::class, 'updateSiteSettings'])->name('homepage-cms.site-settings.update');
    Route::get('/homepage-cms/sections/{section}/edit', [\App\Http\Controllers\SuperAdmin\HomepageCmsController::class, 'editSection'])->name('homepage-cms.sections.edit');
    Route::put('/homepage-cms/sections/{section}', [\App\Http\Controllers\SuperAdmin\HomepageCmsController::class, 'updateSection'])->name('homepage-cms.sections.update');
    Route::post('/homepage-cms/sections/{section}/toggle', [\App\Http\Controllers\SuperAdmin\HomepageCmsController::class, 'toggleSection'])->name('homepage-cms.sections.toggle');
    Route::post('/homepage-cms/sections/reorder', [\App\Http\Controllers\SuperAdmin\HomepageCmsController::class, 'reorderSections'])->name('homepage-cms.sections.reorder');
    Route::get('/homepage-cms/featured-products', [\App\Http\Controllers\SuperAdmin\HomepageCmsController::class, 'featuredProducts'])->name('homepage-cms.featured-products');
    Route::post('/homepage-cms/products/{product}/toggle', [\App\Http\Controllers\SuperAdmin\HomepageCmsController::class, 'toggleFeaturedProduct'])->name('homepage-cms.products.toggle');
});
