<?php

use App\Http\Controllers\Web\ClientController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\InvoiceController;
use App\Http\Controllers\Web\InvoiceDispatchController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\PublicInvoiceController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/public/invoices/{invoice:public_id}', PublicInvoiceController::class)
    ->name('public.invoices.show');

Route::get('dashboard', DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware('auth')->group(function (): void {
    Route::resource('clients', ClientController::class);
    Route::resource('invoices', InvoiceController::class);

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/invoices/{invoice}/send', InvoiceDispatchController::class)->name('invoices.send');
    Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('invoices.payments.store');
});

require __DIR__.'/auth.php';
