<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $invoice->number }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $invoice->client->name }} · {{ $invoice->status->value }} · due {{ $invoice->due_date->format('M j, Y') }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if ($invoice->sent_at === null)
                    <form method="POST" action="{{ route('invoices.send', $invoice) }}">
                        @csrf
                        <button type="submit" class="rounded-md bg-amber-500 px-4 py-2 text-sm font-medium text-white hover:bg-amber-400">
                            Mark Sent
                        </button>
                    </form>
                @endif
                <a href="{{ route('public.invoices.show', $invoice) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800" target="_blank">
                    Public View
                </a>
                <a href="{{ route('invoices.edit', $invoice) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                    Edit
                </a>
                <form method="POST" action="{{ route('invoices.destroy', $invoice) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
            <div class="space-y-6">
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Subtotal</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Money::formatMinor($invoice->subtotal_amount, $invoice->currency) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tax</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Money::formatMinor($invoice->tax_amount, $invoice->currency) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Discount</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \App\Support\Money::formatMinor($invoice->discount_amount, $invoice->currency) }}</p>
                        </div>
                    </div>

                    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Description</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Qty</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Unit</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($invoice->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $item->description }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ number_format((float) $item->quantity, 2) }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ \App\Support\Money::formatMinor($item->unit_price_amount, $invoice->currency) }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ \App\Support\Money::formatMinor($item->line_total_amount, $invoice->currency) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <p><span class="font-medium text-gray-900 dark:text-gray-100">Notes:</span> {{ $invoice->notes ?: 'No notes added.' }}</p>
                        <p><span class="font-medium text-gray-900 dark:text-gray-100">Terms:</span> {{ $invoice->terms ?: 'No payment terms added.' }}</p>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Payment History</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $invoice->payments->count() }} recorded</span>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse ($invoice->payments as $payment)
                            <div class="rounded-lg border border-gray-200 px-4 py-4 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ \App\Support\Money::formatMinor($payment->amount, $invoice->currency) }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->paid_at->format('M j, Y') }}</p>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $payment->method ?: 'Manual payment' }}
                                    @if ($payment->reference)
                                        · Ref {{ $payment->reference }}
                                    @endif
                                </p>
                            </div>
                        @empty
                            <div class="rounded-lg border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                No payments recorded yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Summary</h3>
                    <dl class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300">
                        <div class="flex items-center justify-between">
                            <dt>Total</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ \App\Support\Money::formatMinor($invoice->total_amount, $invoice->currency) }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt>Paid</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ \App\Support\Money::formatMinor($invoice->amount_paid, $invoice->currency) }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt>Balance Due</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ \App\Support\Money::formatMinor($invoice->balance_due_amount, $invoice->currency) }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt>Public Link</dt>
                            <dd class="text-right text-sky-600 dark:text-sky-400">Shared via UUID URL</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Record Payment</h3>
                    <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                            <input id="amount" name="amount" type="number" min="0.01" step="0.01" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                        </div>
                        <div>
                            <label for="paid_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Paid At</label>
                            <input id="paid_at" name="paid_at" type="date" value="{{ now()->toDateString() }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                        </div>
                        <div>
                            <label for="method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Method</label>
                            <input id="method" name="method" type="text" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" placeholder="Bank transfer, card, cash">
                        </div>
                        <div>
                            <label for="reference" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reference</label>
                            <input id="reference" name="reference" type="text" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="payment_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                            <textarea id="payment_notes" name="notes" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"></textarea>
                        </div>
                        <button type="submit" class="w-full rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">
                            Save Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
