<?php

use App\Http\Controllers\UserController;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::post('users/export', [UserController::class, 'export']);

Route::get('{any}', function () {

    $pvcPipeNames = [
        'PVC Pipe 1/2"',
        'PVC Pipe 3/4"',
        'PVC Pipe 1"',
        'PVC Schedule 40 Pipe',
        'PVC Schedule 80 Pipe',
        'PVC DWV Pipe',
        'PVC CPVC Pipe',
        'PVC Conduit Pipe',
        'PVC Irrigation Pipe',
        // Add more PVC pipe-related names as needed
    ];

    shuffle($pvcPipeNames);

    Product::chunk(1000, function ($products) use ($pvcPipeNames) {
        foreach ($products as $product) {
            $product->name = array_shift($pvcPipeNames) ?? 'Default Product Name';
            DB::table('products')->where('id', $product->id)->update(['name' => $product->name]);
        }
    });

    return view('layouts.app');
})->where('any', '.*');
