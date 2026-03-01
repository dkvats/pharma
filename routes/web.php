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
use Illuminate\Support\Facades\RateLimiter;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

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
    
    // User Offers Page
    Route::get('/offers', [\App\Http\Controllers\OfferController::class, 'index'])->name('offers.index');
});

// Admin Routes
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
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
    
    // Spin & Rewards (with rate limiting)
    Route::get('spin', [SpinController::class, 'index'])->name('spin.index');
    Route::post('spin', [SpinController::class, 'spin'])->name('spin.spin')->middleware('throttle:spin');
    Route::get('spin/history', [SpinController::class, 'history'])->name('spin.history');
    Route::post('spin/{spin}/claim', [SpinController::class, 'claim'])->name('spin.claim');
    
    // Reports
    Route::get('reports/performance', [DoctorReportController::class, 'performance'])->name('reports.performance');
});

// Store Routes
Route::middleware(['auth', 'role:Store'])->prefix('store')->name('store.')->group(function () {
    Route::get('/dashboard', [StoreDashboardController::class, 'index'])->name('dashboard');
    
    // Stock Management
    Route::get('stock', [\App\Http\Controllers\Store\StockController::class, 'index'])->name('stock.index');
    Route::post('stock/{stock}/record-sale', [\App\Http\Controllers\Store\StockController::class, 'recordSale'])->name('stock.record-sale');
    
    // Store Orders (My Orders)
    Route::get('orders', [\App\Http\Controllers\Store\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\Store\OrderController::class, 'show'])->name('orders.show');
    
    // Referrals
    Route::get('referrals', [StoreDashboardController::class, 'referrals'])->name('referrals');
    
    // Reports
    Route::get('reports/sales', [StoreReportController::class, 'sales'])->name('reports.sales');
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
