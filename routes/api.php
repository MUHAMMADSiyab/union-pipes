<?php

use App\Http\Controllers\AbilityController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BulkPaymentController;
use App\Http\Controllers\ChequeClearanceController;
use App\Http\Controllers\ClosingReportController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PaymentSettingController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpenseSourceController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GatePassController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MonthlySheetController;
use App\Http\Controllers\PartnerWithdrawalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseItemController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnedSoldItemController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\StockItemController;
use App\Http\Controllers\StockSheetController;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\AuthGates;
use App\Http\Middleware\NullToEmptyString;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::post('attempt_login', [AuthController::class, 'attemptLogin']);
Route::post('logout', [AuthController::class, 'logout']);

// Api routes
Route::group(['middleware' => ['auth:api', AuthGates::class, NullToEmptyString::class]], function () {
    Route::get('abilities', [AbilityController::class, 'index']);

    // Current user
    Route::get('me', [AuthController::class, 'getAuthuser']);

    // Permission
    Route::delete('permissions/delete_multiple', [PermissionController::class, 'destroy_multiple']);
    Route::resource('permissions', PermissionController::class, [
        'except' => ['create', 'edit']
    ]);

    // Role
    Route::delete('roles/delete_multiple', [RoleController::class, 'destroy_multiple']);
    Route::resource('roles', RoleController::class, [
        'except' => ['create', 'edit']
    ]);

    // User
    Route::put('users/edit_user_account', [UserController::class, 'edit_user_account']);
    Route::delete('users/delete_multiple', [UserController::class, 'destroy_multiple']);
    Route::resource('users', UserController::class, [
        'except' => ['create', 'edit']
    ]);

    // Product
    Route::delete('products/delete_multiple', [ProductController::class, 'destroy_multiple']);
    Route::resource('products', ProductController::class, [
        'except' => ['create', 'edit']
    ]);

    // Company
    Route::post('/companies/search', [CompanyController::class, 'search']);
    Route::post('/companies/{company_id}/ledger_entries', [CompanyController::class, 'get_ledger_entries']);
    Route::delete('companies/delete_multiple', [CompanyController::class, 'destroy_multiple']);
    Route::resource('companies', CompanyController::class, [
        'except' => ['create', 'edit']
    ]);

    // Bank
    Route::post('/banks/{bank_id}/ledger_entries', [BankController::class, 'get_ledger_entries']);
    Route::delete('banks/delete_multiple', [BankController::class, 'destroy_multiple']);
    Route::resource('banks', BankController::class, [
        'except' => ['create', 'edit']
    ]);

    // Expense Source
    Route::delete('expense_sources/delete_multiple', [ExpenseSourceController::class, 'destroy_multiple']);
    Route::resource('expense_sources', ExpenseSourceController::class, [
        'except' => ['create', 'edit']
    ]);

    // Expense
    Route::get('search_expenses', [ExpenseController::class, 'search_expenses']);
    Route::delete('expenses/delete_multiple', [ExpenseController::class, 'destroy_multiple']);
    Route::resource('expenses', ExpenseController::class, [
        'except' => ['create', 'edit']
    ]);

    // Employee
    Route::delete('employees/delete_multiple', [
        EmployeeController::class,
        'destroy_multiple'
    ]);
    Route::resource('employees', EmployeeController::class)->except([
        'create',
        'edit'
    ]);

    // Salary
    Route::get('salaries/{employee}/get_totals', [SalaryController::class, 'getTotals']);
    Route::get('salaries/{employee}/get_records', [SalaryController::class, 'salaryRecords']);
    Route::resource('salaries', SalaryController::class)->except([
        'create',
        'edit'
    ]);

    // Customer
    Route::post('/customers/search', [CustomerController::class, 'search']);
    Route::post('/customers/{customer_id}/ledger_entries', [CustomerController::class, 'get_ledger_entries']);
    Route::get('all_customers', [CustomerController::class, 'all']);
    Route::resource('customers', CustomerController::class, [
        'except' => ['create', 'edit']
    ]);

    // Partner
    Route::post('/partners/search', [PartnerController::class, 'search']);
    Route::get('all_partners', [PartnerController::class, 'all']);
    Route::resource('partners', PartnerController::class, [
        'except' => ['create', 'edit']
    ]);

    // Partner Withdrawal
    Route::get('search_partner_withdrawals', [PartnerWithdrawalController::class, 'search_partner_withdrawals']);
    Route::delete('partner_withdrawals/delete_multiple', [PartnerWithdrawalController::class, 'destroy_multiple']);
    Route::resource('partner_withdrawals', PartnerWithdrawalController::class, [
        'except' => ['create', 'edit']
    ]);

    // Purchase Item
    Route::delete('purchase_items/delete_multiple', [PurchaseItemController::class, 'destroy_multiple']);
    Route::resource('purchase_items', PurchaseItemController::class, [
        'except' => ['create', 'edit']
    ]);

    // Purchase 
    Route::get('search_purchases', [PurchaseController::class, 'search_purchases']);
    Route::delete('purchases/delete_multiple', [PurchaseController::class, 'destroy_multiple']);
    Route::resource('purchases', PurchaseController::class, [
        'except' => ['create', 'edit']
    ]);

    // Sell 
    Route::get('search_sells', [SellController::class, 'search_sells']);
    Route::delete('sells/delete_multiple', [SellController::class, 'destroy_multiple']);
    Route::resource('sells', SellController::class, [
        'except' => ['create', 'edit']
    ]);

    // Returned Sold Item
    Route::post('returned_sold_items', [ReturnedSoldItemController::class, 'store']);

    // Transaction
    Route::get('search_transactions', [TransactionController::class, 'search_transactions']);
    Route::delete('transactions/delete_multiple', [TransactionController::class, 'destroy_multiple']);
    Route::resource('transactions', TransactionController::class, [
        'except' => ['create', 'edit']
    ]);

    // Gate Pass
    Route::get('no_sell_gate_passes', [GatePassController::class, 'get_no_sell_gate_passes']);
    Route::get('sell_gate_passes/{sell}', [GatePassController::class, 'get_sell_gate_pass']);
    Route::delete('gate_passes/delete_multiple', [GatePassController::class, 'destroy_multiple']);
    Route::resource('gate_passes', GatePassController::class, [
        'except' => ['create', 'edit']
    ]);

    // Stock Item
    Route::delete('stock_items/delete_multiple', [StockItemController::class, 'destroy_multiple']);
    Route::resource('stock_items', StockItemController::class, [
        'except' => ['create', 'edit']
    ]);

    // Stock
    Route::delete('stocks/delete_multiple', [StockController::class, 'destroy_multiple']);
    Route::get('stocks/{stock_item_id}/get_stock_item_stocks', [StockController::class, 'get_stock_item_stocks']);
    Route::resource('stocks', StockController::class, [
        'except' => ['create', 'edit', 'index']
    ]);

    // Machine
    Route::delete('machines/delete_multiple', [MachineController::class, 'destroy_multiple']);
    Route::resource('machines', MachineController::class, [
        'except' => ['create', 'edit']
    ]);

    // Machine
    Route::get('search_productions', [ProductionController::class, 'search_productions']);
    Route::delete('productions/delete_multiple', [ProductionController::class, 'destroy_multiple']);
    Route::resource('productions', ProductionController::class, [
        'except' => ['create', 'edit']
    ]);

    // Setting (edit)
    Route::put('settings/{setting}', [SettingController::class, 'editSetting']);

    // Payment
    Route::post('payments/get_payments', [PaymentController::class, 'get_payments']);
    Route::resource('payments', PaymentController::class)->except([
        'create',
        'edit'
    ]);

    // Payment Setting
    Route::get('payment_settings', [PaymentSettingController::class, 'get']);
    Route::get('payment_settings/currencies', [PaymentSettingController::class, 'currencies']);
    Route::put('payment_settings/{paymentSetting}', [PaymentSettingController::class, 'update']);

    // Export 
    Route::post('export', [ExportController::class, 'export']);

    // Dashboard
    Route::post('dashboard', DashboardController::class);

    Route::apiResource('bulk_payments', BulkPaymentController::class);

    Route::delete('raw_materials/delete_multiple', [RawMaterialController::class, 'destroy_multiple']);
    Route::apiResource('raw_materials', RawMaterialController::class);

    Route::delete('stock_sheets/delete_multiple', [StockSheetController::class, 'destroy_multiple']);
    Route::apiResource('stock_sheets', StockSheetController::class);

    Route::delete('monthly_sheets/delete_multiple', [MonthlySheetController::class, 'destroy_multiple']);
    Route::apiResource('monthly_sheets', MonthlySheetController::class);

    Route::group(['prefix' => 'reports'], function () {
        Route::post('purchase', [ReportController::class, 'get_purchase_report']);
        Route::post('purchased_items', [ReportController::class, 'get_purchased_items_report']);

        Route::post('sell', [ReportController::class, 'get_sell_report']);
        Route::post('sold_items', [ReportController::class, 'get_sold_items_report']);

        Route::get('receivables', [ReportController::class, 'get_receivables_report']);
        Route::get('payables', [ReportController::class, 'get_payables_report']);

        Route::post('expense', [ReportController::class, 'get_expense_report']);

        Route::post('machine', [ReportController::class, 'get_machines_report']);

        Route::post('salary', [ReportController::class, 'get_salaries_report']);

        Route::post('closing', [ClosingReportController::class, 'index']);
    });

    Route::put('uncleared_cheques/{payment}', [
        ChequeClearanceController::class,
        'markAsCleared'
    ]);

    Route::put('uncleared_cheques', [
        ChequeClearanceController::class,
        'markAllAsCleared'
    ]);

    Route::get('/youtube/playlist', [PlaylistController::class, 'getPlaylistVideos']);
});

// Public routes
Route::get('settings', [SettingController::class, 'getAppSetting']);
