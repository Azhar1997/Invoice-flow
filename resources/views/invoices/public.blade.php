<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoice->number }} | InvoiceFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <main class="mx-auto max-w-5xl px-6 py-12">
        <section class="rounded-3xl border border-slate-800 bg-slate-900/80 p-8 shadow-2xl shadow-slate-950/30">
            <div class="flex flex-col gap-6 border-b border-slate-800 pb-8 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-sky-400">InvoiceFlow</p>
                    <h1 class="mt-4 text-3xl font-semibold text-white">{{ $invoice->number }}</h1>
                    <p class="mt-2 text-sm text-slate-300">Due {{ $invoice->due_date->format('F j, Y') }}</p>
                </div>
                <div class="rounded-2xl bg-slate-800 px-5 py-4 text-right">
                    <p class="text-sm uppercase tracking-wide text-slate-400">{{ $invoice->status->value }}</p>
                    <p class="mt-2 text-2xl font-semibold text-white">{{ \App\Support\Money::formatMinor($invoice->balance_due_amount, $invoice->currency) }}</p>
                    <p class="text-sm text-slate-400">Balance due</p>
                </div>
            </div>

            <div class="mt-8 grid gap-8 md:grid-cols-[1.1fr_0.9fr]">
                <div>
                    <h2 class="text-lg font-semibold text-white">Bill To</h2>
                    <p class="mt-3 text-slate-300">{{ $invoice->client->name }}</p>
                    @if ($invoice->client->company_name)
                        <p class="text-slate-400">{{ $invoice->client->company_name }}</p>
                    @endif
                    @if ($invoice->client->email)
                        <p class="text-slate-400">{{ $invoice->client->email }}</p>
                    @endif
                </div>
                <div class="grid gap-3 rounded-2xl border border-slate-800 p-5 text-sm text-slate-300">
                    <div class="flex items-center justify-between">
                        <span>Issued</span>
                        <span>{{ $invoice->issue_date->format('M j, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Due</span>
                        <span>{{ $invoice->due_date->format('M j, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Paid</span>
                        <span>{{ \App\Support\Money::formatMinor($invoice->amount_paid, $invoice->currency) }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 overflow-hidden rounded-2xl border border-slate-800">
                <table class="min-w-full divide-y divide-slate-800 text-sm">
                    <thead class="bg-slate-900">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-slate-400">Description</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-400">Qty</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-400">Unit</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-400">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach ($invoice->items as $item)
                            <tr>
                                <td class="px-4 py-3">{{ $item->description }}</td>
                                <td class="px-4 py-3">{{ number_format((float) $item->quantity, 2) }}</td>
                                <td class="px-4 py-3">{{ \App\Support\Money::formatMinor($item->unit_price_amount, $invoice->currency) }}</td>
                                <td class="px-4 py-3 font-medium text-white">{{ \App\Support\Money::formatMinor($item->line_total_amount, $invoice->currency) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex flex-col gap-8 md:flex-row md:items-start md:justify-between">
                <div class="max-w-2xl text-sm text-slate-300">
                    <p><span class="font-medium text-white">Notes:</span> {{ $invoice->notes ?: 'No notes added.' }}</p>
                    <p class="mt-3"><span class="font-medium text-white">Terms:</span> {{ $invoice->terms ?: 'No payment terms added.' }}</p>
                </div>
                <div class="w-full max-w-sm rounded-2xl border border-slate-800 p-5">
                    <div class="flex items-center justify-between text-sm text-slate-400">
                        <span>Subtotal</span>
                        <span>{{ \App\Support\Money::formatMinor($invoice->subtotal_amount, $invoice->currency) }}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-sm text-slate-400">
                        <span>Tax</span>
                        <span>{{ \App\Support\Money::formatMinor($invoice->tax_amount, $invoice->currency) }}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-sm text-slate-400">
                        <span>Discount</span>
                        <span>-{{ \App\Support\Money::formatMinor($invoice->discount_amount, $invoice->currency) }}</span>
                    </div>
                    <div class="mt-4 border-t border-slate-800 pt-4">
                        <div class="flex items-center justify-between text-lg font-semibold text-white">
                            <span>Total</span>
                            <span>{{ \App\Support\Money::formatMinor($invoice->total_amount, $invoice->currency) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
