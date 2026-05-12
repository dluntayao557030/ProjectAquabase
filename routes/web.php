<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\StaffsController;
use App\Http\Controllers\AdminTransactionsController;
use App\Http\Controllers\AdminReportsController;
use App\Http\Controllers\StaffTransactionsController;
use App\Http\Controllers\StaffReportsController;

// Root
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth (no middleware)
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes – using CustomAuth directly
Route::prefix('admin')->name('admin.')->middleware(\App\Http\Middleware\CustomAuth::class . ':admin')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/kpi/inventory', [DashboardController::class, 'kpiInventory'])->name('dashboard.kpi.inventory');
    Route::get('/dashboard/kpi/stock-in',  [DashboardController::class, 'kpiStockIn'])->name('dashboard.kpi.stockIn');
    Route::get('/dashboard/kpi/stock-out', [DashboardController::class, 'kpiStockOut'])->name('dashboard.kpi.stockOut');

    Route::resource('inventory', InventoryController::class)->only(['index', 'store', 'update', 'destroy'])
        ->names(['index' => 'inventory.index', 'store' => 'inventory.store', 'update' => 'inventory.update', 'destroy' => 'inventory.destroy']);

    Route::resource('suppliers', SuppliersController::class)->only(['index', 'store', 'update', 'destroy'])
        ->names(['index' => 'suppliers.index', 'store' => 'suppliers.store', 'update' => 'suppliers.update', 'destroy' => 'suppliers.destroy']);

    Route::resource('staffs', StaffsController::class)->only(['index', 'store', 'update', 'destroy'])
        ->names(['index' => 'staffs.index', 'store' => 'staffs.store', 'update' => 'staffs.update', 'destroy' => 'staffs.destroy']);

    Route::get('/transactions', [AdminTransactionsController::class, 'index'])->name('transactions.index');
    Route::post('/transactions/stock-in', [AdminTransactionsController::class, 'stockIn'])->name('transactions.stockIn');
    Route::post('/transactions/stock-out', [AdminTransactionsController::class, 'stockOut'])->name('transactions.stockOut');
    Route::get('/transactions/history', [AdminTransactionsController::class, 'historyData'])->name('transactions.historyData');

    Route::get('/reports', [AdminReportsController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [AdminReportsController::class, 'generate'])->name('reports.generate');
});

// Staff routes
Route::prefix('staff')->name('staff.')->middleware(\App\Http\Middleware\CustomAuth::class . ':staff')->group(function () {

    Route::get('/transactions', [StaffTransactionsController::class, 'index'])->name('transactions.index');
    Route::post('/transactions/stock-in', [StaffTransactionsController::class, 'stockIn'])->name('transactions.stockIn');
    Route::post('/transactions/stock-out', [StaffTransactionsController::class, 'stockOut'])->name('transactions.stockOut');
    Route::get('/transactions/history', [StaffTransactionsController::class, 'historyData'])->name('transactions.historyData');

    Route::get('/reports', [StaffReportsController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [StaffReportsController::class, 'generate'])->name('reports.generate');
});