<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\ExpenseController;
use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Inventory\StockAdjustmentController;
use App\Http\Controllers\Masters\CategoryController;
use App\Http\Controllers\Masters\CustomerController;
use App\Http\Controllers\Masters\ProductController;
use App\Http\Controllers\Masters\PromotionController;
use App\Http\Controllers\Masters\UnitController;
use App\Http\Controllers\Masters\VendorController;
use App\Http\Controllers\Production\FormulaController;
use App\Http\Controllers\Production\ProductionOrderController;
use App\Http\Controllers\Purchasing\GoodsReceiptController;
use App\Http\Controllers\Purchasing\PurchaseOrderController;
use App\Http\Controllers\Purchasing\VendorPaymentController;
use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\Sales\PosController;
use App\Http\Controllers\Sales\SalesInvoiceController;
use App\Http\Controllers\Sales\SalesReturnController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Settings\UserController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated ERP Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS & Sales Billing
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::post('/calculate', [PosController::class, 'calculate'])->name('calculate');
        Route::post('/store', [PosController::class, 'store'])->name('store');
    });

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [SalesInvoiceController::class, 'index'])->name('index');
        Route::get('/{invoice}', [SalesInvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/print', [SalesInvoiceController::class, 'print'])->name('print');
        Route::get('/{invoice}/pdf', [SalesInvoiceController::class, 'downloadPdf'])->name('pdf');
    });

    Route::prefix('returns')->name('returns.')->group(function () {
        Route::get('/', [SalesReturnController::class, 'index'])->name('index');
        Route::get('/create', [SalesReturnController::class, 'create'])->name('create');
        Route::post('/store', [SalesReturnController::class, 'store'])->name('store');
    });

    // Manufacturing & Chemical Production
    Route::prefix('production')->name('production.')->group(function () {
        Route::prefix('formulas')->name('formulas.')->group(function () {
            Route::get('/', [FormulaController::class, 'index'])->name('index');
            Route::get('/create', [FormulaController::class, 'create'])->name('create');
            Route::post('/', [FormulaController::class, 'store'])->name('store');
            Route::get('/{formula}', [FormulaController::class, 'show'])->name('show');
        });

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [ProductionOrderController::class, 'index'])->name('index');
            Route::get('/create', [ProductionOrderController::class, 'create'])->name('create');
            Route::post('/', [ProductionOrderController::class, 'store'])->name('store');
            Route::get('/{order}', [ProductionOrderController::class, 'show'])->name('show');
            Route::post('/{order}/qc', [ProductionOrderController::class, 'recordQc'])->name('qc');
            Route::post('/{order}/finalize', [ProductionOrderController::class, 'finalize'])->name('finalize');
        });
    });

    // Multi-tier Inventory & Ledger
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/ledger', [InventoryController::class, 'ledger'])->name('ledger');
        Route::get('/opening-stock', [InventoryController::class, 'createOpeningStock'])->name('opening_stock');
        Route::post('/opening-stock', [InventoryController::class, 'storeOpeningStock'])->name('opening_stock.store');

        Route::prefix('adjustments')->name('adjustments.')->group(function () {
            Route::get('/', [StockAdjustmentController::class, 'index'])->name('index');
            Route::get('/create', [StockAdjustmentController::class, 'create'])->name('create');
            Route::post('/', [StockAdjustmentController::class, 'store'])->name('store');
        });
    });

    // Purchasing & Vendor Management
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
            Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
            Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
            Route::get('/{order}', [PurchaseOrderController::class, 'show'])->name('show');
            Route::post('/{order}/approve', [PurchaseOrderController::class, 'approve'])->name('approve');
        });

        Route::prefix('grn')->name('grn.')->group(function () {
            Route::get('/', [GoodsReceiptController::class, 'index'])->name('index');
            Route::get('/create', [GoodsReceiptController::class, 'create'])->name('create');
            Route::post('/', [GoodsReceiptController::class, 'store'])->name('store');
            Route::get('/{receipt}', [GoodsReceiptController::class, 'show'])->name('show');
        });

        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [VendorPaymentController::class, 'index'])->name('index');
            Route::get('/create', [VendorPaymentController::class, 'create'])->name('create');
            Route::post('/', [VendorPaymentController::class, 'store'])->name('store');
        });
    });

    // Masters Management
    Route::prefix('masters')->name('masters.')->group(function () {
        Route::resource('products', ProductController::class);
        Route::resource('customers', CustomerController::class);
        Route::resource('vendors', VendorController::class)->except(['show', 'destroy']);

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('units')->name('units.')->group(function () {
            Route::get('/', [UnitController::class, 'index'])->name('index');
            Route::post('/', [UnitController::class, 'store'])->name('store');
            Route::post('/conversion', [UnitController::class, 'storeConversion'])->name('conversion.store');
        });
    });

    // Promotions
    Route::prefix('promotions')->name('promotions.')->group(function () {
        Route::get('/', [PromotionController::class, 'index'])->name('index');
        Route::get('/create', [PromotionController::class, 'create'])->name('create');
        Route::post('/', [PromotionController::class, 'store'])->name('store');
        Route::post('/{promotion}/toggle', [PromotionController::class, 'toggle'])->name('toggle');
    });

    // Finance & Expenses
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/create', [ExpenseController::class, 'create'])->name('create');
        Route::post('/', [ExpenseController::class, 'store'])->name('store');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/production', [ReportController::class, 'production'])->name('production');
    });

    // System Settings & Users
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/update', [SettingsController::class, 'update'])->name('update');
    });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::post('/{user}/toggle', [UserController::class, 'toggle'])->name('toggle');
    });
});
