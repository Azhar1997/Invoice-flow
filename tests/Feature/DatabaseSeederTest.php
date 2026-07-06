<?php

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('database seeder creates a coherent demo workspace', function () {
    $this->seed(DatabaseSeeder::class);

    $user = User::where('email', 'demo@invoiceflow.test')->first();

    expect($user)->not->toBeNull();
    expect($user->clients()->count())->toBe(3);
    expect($user->invoices()->count())->toBe(4);
    expect($user->payments()->count())->toBe(2);

    $statuses = $user->invoices()
        ->get()
        ->map(fn (Invoice $invoice) => $invoice->status->value)
        ->all();

    expect($statuses)->toContain(InvoiceStatus::Draft->value);
    expect($statuses)->toContain(InvoiceStatus::Sent->value);
    expect($statuses)->toContain(InvoiceStatus::Overdue->value);
    expect($statuses)->toContain(InvoiceStatus::Paid->value);
});

test('database seeder keeps invoice items payments and totals in sync', function () {
    $this->seed(DatabaseSeeder::class);

    $invoices = Invoice::with(['items', 'payments', 'client', 'user'])->get();

    expect($invoices)->toHaveCount(4);

    $invoices->each(function (Invoice $invoice): void {
        $itemSubtotal = $invoice->items->sum('line_total_amount');
        $paymentTotal = $invoice->payments->sum('amount');

        expect($invoice->client->user_id)->toBe($invoice->user_id);
        expect($itemSubtotal)->toBe($invoice->subtotal_amount);
        expect($paymentTotal)->toBe($invoice->amount_paid);
        expect($invoice->balance_due_amount)->toBe(max($invoice->total_amount - $paymentTotal, 0));
    });
});
