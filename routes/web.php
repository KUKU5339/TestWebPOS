<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CsrfController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockAlertController;
use Illuminate\Support\Facades\Route;

// Login & Register routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// CSRF token refresh endpoint (outside auth for offline sync)
Route::get('/api/csrf-token', [CsrfController::class, 'refresh']);

// Protected routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/dashboard', '/');

    // Products CRUD
    Route::resource('products', ProductController::class);

    // Sales routes
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{id}/receipt', [SaleController::class, 'generateReceipt'])->name('sales.receipt');
    Route::get('/sales/receipt/batch', [SaleController::class, 'batchReceipt'])->name('sales.receipt.batch');
    Route::get('/sales/receipt/batch/pdf', [SaleController::class, 'downloadBatchReceiptPdf'])->name('sales.receipt.batch.pdf');
    Route::get('/sales/{id}/receipt/pdf', [SaleController::class, 'downloadReceiptPdf'])->name('sales.receipt.pdf');
    Route::get('/sales/{id}/receipt/json', [SaleController::class, 'getReceiptData'])->name('sales.receipt.json');
    Route::delete('/sales/{id}', [SaleController::class, 'destroy'])->name('sales.destroy');

    // Quicksale
    Route::get('/quick-sale', [SaleController::class, 'quickSale'])->name('sales.quick');

    // Expenses & Profit Calculator
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Stock Alerts
    Route::get('/stock-alerts', [StockAlertController::class, 'index'])->name('stock-alerts.index');
    Route::post('/stock-alerts/threshold', [StockAlertController::class, 'updateThreshold'])->name('stock-alerts.threshold');
    Route::post('/stock-alerts/toggle', [StockAlertController::class, 'toggleAlerts'])->name('stock-alerts.toggle');
    Route::get('/stock-alerts/shopping-list', [StockAlertController::class, 'generateShoppingList'])->name('stock-alerts.shopping-list');

    // Sales Reports
    Route::get('/reports/daily-sales', [ReportController::class, 'dailySales'])->name('reports.daily-sales');
    Route::get('/reports/download', [ReportController::class, 'downloadReport'])->name('reports.download');

    // Lazy-load API endpoints (JSON, GET only)
    Route::get('/api/low-stock', [StockAlertController::class, 'lowStockJson']);
    Route::get('/api/dashboard/stats', [DashboardController::class, 'statsJson']);
    Route::get('/api/dashboard/recent-sales', [DashboardController::class, 'recentSalesJson']);
    Route::get('/api/dashboard/low-stock', [DashboardController::class, 'lowStockJson']);
    Route::get('/api/dashboard/top-products', [DashboardController::class, 'topProductsJson']);
    Route::get('/api/dashboard/today', [DashboardController::class, 'todayJson']);
    Route::get('/api/products/in-stock', [SaleController::class, 'productsInStockJson']);
    Route::get('/api/products', [ProductController::class, 'indexJson']);
    Route::get('/api/sales', [SaleController::class, 'indexJson']);

    // Offline sync endpoints
    Route::post('/api/sync-sale', [SaleController::class, 'syncOfflineSale']);
    Route::post('/api/sync-product', [ProductController::class, 'syncOfflineProduct']);
    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
});
