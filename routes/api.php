<?php

use App\Http\Controllers\AbilityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DispenserController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\NozzleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentSettingController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TankController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\VehicleController;
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

    // Tank
    Route::resource('tanks', TankController::class, [
        'except' => ['create', 'edit']
    ]);

    // Dispenser
    Route::delete('dispensers/delete_multiple', [DispenserController::class, 'destroy_multiple']);
    Route::resource('dispensers', DispenserController::class, [
        'except' => ['create', 'edit']
    ]);

    // Nozzle
    Route::delete('nozzles/delete_multiple', [NozzleController::class, 'destroy_multiple']);
    Route::resource('nozzles', NozzleController::class, [
        'except' => ['create', 'edit']
    ]);

    // Vehicle
    Route::get('vehicles/{vehicle}/chambers', [VehicleController::class, 'getVehicleChambers']);
    Route::resource('vehicles', VehicleController::class, [
        'except' => ['create', 'edit']
    ]);

    // Company
    Route::delete('companies/delete_multiple', [CompanyController::class, 'destroy_multiple']);
    Route::resource('companies', CompanyController::class, [
        'except' => ['create', 'edit']
    ]);

    // Bank
    Route::delete('banks/delete_multiple', [BankController::class, 'destroy_multiple']);
    Route::resource('banks', BankController::class, [
        'except' => ['create', 'edit']
    ]);

    // Transaction
    Route::delete('transactions/delete_multiple', [TransactionController::class, 'destroy_multiple']);
    Route::resource('transactions', TransactionController::class, [
        'except' => ['create', 'edit']
    ]);

    // Utility
    Route::delete('utilities/delete_multiple', [UtilityController::class, 'destroy_multiple']);
    Route::resource('utilities', UtilityController::class, [
        'except' => ['create', 'edit']
    ]);

    Route::delete('purchases/delete_multiple', [PurchaseController::class, 'destroy_multiple']);
    Route::resource('purchases', PurchaseController::class, [
        'except' => ['create', 'edit']
    ]);

    // Rate
    Route::get('rates/current', [RateController::class, 'get_current_rates']);
    Route::post('rates/update', [RateController::class, 'update']);
    Route::get('rates', [RateController::class, 'index']);

    // Payment
    Route::post('payments/get_payments', [PaymentController::class, 'get_payments']);
    Route::resource('payments', PaymentController::class)->except([
        'create', 'edit'
    ]);

    // Setting (edit)
    Route::put('settings/{setting}', [SettingController::class, 'editSetting']);

    // Payment Setting
    Route::get('payment_settings', [PaymentSettingController::class, 'get']);
    Route::get('payment_settings/currencies', [PaymentSettingController::class, 'currencies']);
    Route::put('payment_settings/{paymentSetting}', [PaymentSettingController::class, 'update']);

    // Export 
    Route::post('export', [ExportController::class, 'export']);
});

// Public routes
Route::get('settings', [SettingController::class, 'getAppSetting']);
