<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

Route::prefix('customers')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('upload', [CustomerController::class, 'upload'])->name('customers.upload');
    Route::get('getCustomers', [CustomerController::class, 'getCustomers'])->name('customers.getCustomers');
    Route::get('getCalculation/{id}', [CustomerController::class, 'getCalculation'])->name('customers.getCalculation');
});
