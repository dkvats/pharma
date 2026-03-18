<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MR\MRDashboardController;
use App\Http\Controllers\MR\MRDoctorController;
use App\Http\Controllers\MR\MRVisitController;
use App\Http\Controllers\MR\MROrderController;
use App\Http\Controllers\MR\MRSampleController;
use App\Http\Controllers\MR\MRReportController;
use App\Http\Controllers\MR\PincodeController;
use App\Http\Controllers\MR\MRStoreController;

/*
|--------------------------------------------------------------------------
| MR (Medical Representative) Routes
|--------------------------------------------------------------------------
|
| All routes for the MR module are prefixed with /mr
| and require authentication with MR role.
|
*/

Route::middleware(['auth', 'role:MR'])->prefix('mr')->name('mr.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [MRDashboardController::class, 'index'])->name('dashboard');
    
    // Store Management (MR Registered Stores)
    Route::resource('stores', MRStoreController::class);
    Route::get('stores/get-districts/{stateId}', [MRStoreController::class, 'getDistricts'])->name('stores.get-districts');
    Route::get('stores/get-cities/{districtId}', [MRStoreController::class, 'getCities'])->name('stores.get-cities');
    Route::get('stores/get-areas/{cityId}', [MRStoreController::class, 'getAreas'])->name('stores.get-areas');
    
    // Doctor Management
    Route::resource('doctors', MRDoctorController::class);
    Route::get('doctors/{doctor}/visits', [MRDoctorController::class, 'visits'])->name('doctors.visits');
    Route::get('doctors/{doctor}/orders', [MRDoctorController::class, 'orders'])->name('doctors.orders');
    Route::get('doctors/get-districts/{stateId}', [MRDoctorController::class, 'getDistricts'])->name('doctors.get-districts');
    Route::get('doctors/get-cities/{districtId}', [MRDoctorController::class, 'getCities'])->name('doctors.get-cities');
    Route::get('doctors/get-areas/{cityId}', [MRDoctorController::class, 'getAreas'])->name('doctors.get-areas');
    
    // Visit Management (DCR)
    Route::resource('visits', MRVisitController::class);
    Route::get('visits/by-date/{date}', [MRVisitController::class, 'byDate'])->name('visits.by-date');
    
    // Order Management
    Route::resource('orders', MROrderController::class);
    Route::get('orders/{order}/print', [MROrderController::class, 'print'])->name('orders.print');
    
    // Sample Management
    Route::resource('samples', MRSampleController::class);
    Route::get('samples/by-doctor/{doctor}', [MRSampleController::class, 'byDoctor'])->name('samples.by-doctor');
    
    // Reports
    Route::get('reports/daily', [MRReportController::class, 'daily'])->name('reports.daily');
    Route::get('reports/weekly', [MRReportController::class, 'weekly'])->name('reports.weekly');
    Route::get('reports/monthly', [MRReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('reports/doctors', [MRReportController::class, 'doctors'])->name('reports.doctors');
    Route::get('reports/performance', [MRReportController::class, 'performance'])->name('reports.performance');
    
    // PIN Code Lookup API
    Route::get('pincode/{pin}', [PincodeController::class, 'lookup'])->name('pincode.lookup');
    Route::get('pincode/search-by-state', [PincodeController::class, 'searchByState'])->name('pincode.search-by-state');
});
