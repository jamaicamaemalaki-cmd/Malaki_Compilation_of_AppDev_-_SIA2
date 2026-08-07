<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\FacilityController;
use App\Models\BloodInventory;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/availability', function () {
    return view('availability', [
        'inventories' => BloodInventory::where('units_available', '>', 0)
            ->orderBy('facility_name')
            ->orderBy('blood_type')
            ->get(),
    ]);
})->name('availability');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'chooseRole'])->name('register.choose');
Route::get('/register/{role}', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register/{role}', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/donor/request', [DonorController::class, 'create'])->name('donor.request');
Route::post('/donor/request', [DonorController::class, 'store'])->name('donor.request.store');

Route::get('/facility/request-blood', [FacilityController::class, 'requestBlood'])->name('facility.request-blood');
Route::post('/facility/request-blood', [FacilityController::class, 'storeBloodRequest'])->name('facility.request-blood.store');
Route::get('/facility/requests', [FacilityController::class, 'facilityRequests'])->name('facility.requests');
Route::get('/facility/donor-requests', [FacilityController::class, 'donorRequests'])->name('facility.donor-requests');
Route::patch('/facility/donor-requests/{donationRequest}', [FacilityController::class, 'scheduleDonor'])->name('facility.donor-requests.update');
Route::patch('/facility/addressed-requests/{bloodRequest}', [FacilityController::class, 'updateAddressedRequest'])->name('facility.addressed-requests.update');
Route::get('/facility/inventory', [FacilityController::class, 'inventory'])->name('facility.inventory');
Route::post('/facility/inventory', [FacilityController::class, 'storeInventory'])->name('facility.inventory.store');

Route::get('/admin/create-default', [AdminController::class, 'seedAdmin'])->name('admin.seed');
Route::get('/admin/requests', [AdminController::class, 'requests'])->name('admin.requests');
Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
