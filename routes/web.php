<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LocationShiftController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\GroupManagementController;
use App\Http\Controllers\SystemLogController;
use App\Http\Controllers\SatpamController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\LogHistoryController;
use App\Http\Controllers\PGAController;
use App\Http\Controllers\GroupDetailsController;

// Halaman Login (Hanya bisa diakses jika belum login/guest)
Route::middleware(['guest', 'prevent-back-history'])->group(function () {
    Route::get('/', [LoginController::class, 'index'])->name('login');
    Route::get('/login', [LoginController::class, 'index']); // Handle GET /login request
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
});

// Halaman yang butuh Login (Auth)
Route::middleware(['auth', 'prevent-back-history'])->group(function () {
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ==========================================
    // SHARED ROUTES (Satpam & PGA)
    // ==========================================
    Route::middleware(['role:Satpam,PGA'])->group(function () {
        Route::get('/journal/view/{id}', [JournalController::class, 'viewJournalDetail'])->name('journal.view');
        Route::get('/log-history', [LogHistoryController::class, 'viewJournal'])->name('log-history');
        Route::get('/journal/download/{id}', [JournalController::class, 'downloadPDF'])->name('journal.download');
    });

    // ==========================================
    // ADMIN ROUTES
    // ==========================================
    Route::middleware(['role:Admin'])->group(function () {
        // Admin - Dashboard
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // Admin - System Logs
        Route::get('/admin/system-logs', [SystemLogController::class, 'index'])->name('admin.system-logs');

        // Admin - User Management
        Route::get('/admin/user-management', [UserManagementController::class, 'index'])->name('admin.user-management');
        Route::post('/admin/user', [UserManagementController::class, 'addUser'])->name('admin.user.store');
        Route::put('/admin/user/{user}', [UserManagementController::class, 'editUser'])->name('admin.user.update');
        Route::delete('/admin/user/{user}', [UserManagementController::class, 'deleteUser'])->name('admin.user.destroy');

        // Admin - Group Management
        Route::get('/admin/group-management', [GroupManagementController::class, 'index'])->name('admin.group-management');
        Route::post('/admin/group', [GroupManagementController::class, 'addGroup'])->name('admin.group.store');
        Route::put('/admin/group/{group}', [GroupManagementController::class, 'editGroup'])->name('admin.group.update');
        Route::delete('/admin/group/{group}', [GroupManagementController::class, 'deleteGroup'])->name('admin.group.destroy');

        // Admin - Location & Shift Management
        Route::get('/admin/location-shift', [LocationShiftController::class, 'index'])->name('admin.location-shift');

        // Admin - Locations
        Route::post('/admin/location', [LocationShiftController::class, 'addLocation'])->name('admin.location.store');
        Route::put('/admin/location/{location}', [LocationShiftController::class, 'editLocation'])->name('admin.location.update');
        Route::patch('/admin/location/{location}/toggle', [LocationShiftController::class, 'updateLocationStatus'])->name('admin.location.toggle');
        Route::delete('/admin/location/{location}', [LocationShiftController::class, 'deleteLocation'])->name('admin.location.destroy');

        // Admin - Shifts
        Route::post('/admin/shift', [LocationShiftController::class, 'addShift'])->name('admin.shift.store');
        Route::put('/admin/shift/{shift}', [LocationShiftController::class, 'editShift'])->name('admin.shift.update');
        Route::patch('/admin/shift/{shift}/toggle', [LocationShiftController::class, 'updateShiftStatus'])->name('admin.shift.toggle');
        Route::delete('/admin/shift/{shift}', [LocationShiftController::class, 'deleteShift'])->name('admin.shift.destroy');
    });

    // ==========================================
    // SATPAM ROUTES
    // ==========================================
    Route::middleware(['role:Satpam'])->group(function () {
        // Satpam - Dashboard
        Route::get('/satpam/dashboard', [SatpamController::class, 'dashboard'])->name('satpam.dashboard');

        // Satpam - Journal Submission
        Route::get('/satpam/journal-submission', [JournalController::class, 'create'])->name('satpam.journal-submission');
        Route::post('/satpam/journal-submission', [JournalController::class, 'submitJournal'])->name('satpam.journal.submit');

        // Log History
        // Moved to Shared Routes

        // Journal Actions
        // Replaced route here to shared auth group
        Route::get('/satpam/journal/edit/{journal}', [JournalController::class, 'edit'])->name('satpam.journal.edit');
        Route::put('/satpam/journal/edit/{journal}', [JournalController::class, 'update'])->name('satpam.journal.update');
        Route::post('/satpam/journal/handover/{id}', [JournalController::class, 'handoverApproval'])->name('satpam.journal.handover');
        // Download moved to Shared Routes
    });

    // ==========================================
    // PGA ROUTES
    // ==========================================
    Route::middleware(['role:PGA'])->group(function () {
        // PGA - Dashboard
        Route::get('/pga/dashboard', [PGAController::class, 'dashboard'])->name('pga.dashboard');
        
        // PGA - Journal Approval
        Route::post('/pga/journal/{id}/approve', [JournalController::class, 'finalApproval'])->name('pga.journal.approve');
        

        // PGA - Group Details
        Route::get('/pga/groups-details', [GroupDetailsController::class, 'viewGroup'])->name('pga.groups-details');

        // PGA - Journal Deletion
        Route::delete('/pga/journal/{id}', [JournalController::class, 'deleteJournal'])->name('pga.journal.delete');
    });
});