<?php

use App\Http\Controllers\LedgerExportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('users/export', [UserController::class, 'export']);
Route::get('/companies/{company_id}/ledger/export/pdf', [LedgerExportController::class, 'exportCompanyPDF'])->name('company.ledger.export.pdf');
Route::get('/customers/{customer_id}/ledger/export/pdf', [LedgerExportController::class, 'exportCustomerPDF'])->name('customer.ledger.export.pdf');
Route::get('/banks/{bank_id}/ledger/export/pdf', [LedgerExportController::class, 'exportBankPDF'])
    ->name('bank.ledger.export.pdf');

Route::get('{any}', function () {
    return view('layouts.app');
})->where('any', '.*');
