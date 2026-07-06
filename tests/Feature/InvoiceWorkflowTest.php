<?php

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;

test('users can create invoices and totals are calculated', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();

    $this->actingAs($user);

    $this->post(route('invoices.store'), [
        'client_id' => $client->id,
        'currency' => 'EUR',
        'issue_date' => '2026-06-01',
        'due_date' => '2026-06-15',
        'tax_rate' => 21,
        'discount_rate' => 10,
        'items' => [
            ['description' => 'Design sprint', 'quantity' => 2, 'unit_price' => 100.00],
            ['description' => 'Discovery session', 'quantity' => 1, 'unit_price' => 50.00],
            ['description' => '', 'quantity' => '', 'unit_price' => ''],
        ],
        'notes' => 'Thanks for the work',
        'terms' => 'Pay within 14 days',
    ])->assertRedirect();

    $invoice = Invoice::with('items')->first();

    expect($invoice->number)->toBe('INV-0001');
    expect($invoice->subtotal_amount)->toBe(25000);
    expect($invoice->discount_amount)->toBe(2500);
    expect($invoice->tax_amount)->toBe(4725);
    expect($invoice->total_amount)->toBe(27225);
    expect($invoice->balance_due_amount)->toBe(27225);
    expect($invoice->items)->toHaveCount(2);
    expect($invoice->status)->toBe(InvoiceStatus::Draft);
});

test('sending and paying an invoice updates its status and balance', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()->for($user)->for($client)->create([
        'subtotal_amount' => 10000,
        'total_amount' => 10000,
        'balance_due_amount' => 10000,
    ]);

    $invoice->items()->create([
        'position' => 1,
        'description' => 'Build',
        'quantity' => 1,
        'unit_price_amount' => 10000,
        'line_total_amount' => 10000,
    ]);

    $this->actingAs($user);

    $this->post(route('invoices.send', $invoice))
        ->assertRedirect(route('invoices.show', $invoice));

    expect($invoice->fresh()->status)->toBe(InvoiceStatus::Sent);

    $this->post(route('invoices.payments.store', $invoice), [
        'amount' => 100.00,
        'paid_at' => '2026-06-20',
        'method' => 'bank transfer',
        'reference' => 'PAY-100',
    ])->assertRedirect(route('invoices.show', $invoice));

    $invoice->refresh();

    expect($invoice->amount_paid)->toBe(10000);
    expect($invoice->balance_due_amount)->toBe(0);
    expect($invoice->status)->toBe(InvoiceStatus::Paid);
});

test('public invoices can be viewed by public id', function () {
    $invoice = Invoice::factory()->create();
    $invoice->items()->create([
        'position' => 1,
        'description' => 'Strategy session',
        'quantity' => 1,
        'unit_price_amount' => 12000,
        'line_total_amount' => 12000,
    ]);

    $this->get(route('public.invoices.show', $invoice))
        ->assertOk()
        ->assertSee($invoice->number);
});
