<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::name('api.')
    ->middleware(['auth', 'throttle:api'])
    ->group(function (): void {
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('invoices', InvoiceController::class);
});
