<?php

use App\Http\Controllers\UserController;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::post('users/export', [UserController::class, 'export']);

Route::get('{any}', function () {
    return view('layouts.app');
})->where('any', '.*');
