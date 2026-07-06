<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                InvoiceFlow Dashboard
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Your current clients, invoices, and payments in one place.
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:px-8">
            <section class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Revenue This Month</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $revenueThisMonth }}</p>
                </div>
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Outstanding Invoices</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $outstandingInvoices }}</p>
                </div>
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Overdue Invoices</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $overdueInvoices }}</p>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Invoices</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jump back into your latest billing work.</p>
                        </div>
                        <a href="{{ route('invoices.create') }}" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">
                            New Invoice
                        </a>
                    </div>

                    <div class="mt-6 space-y-3">
                        @forelse ($recentInvoices as $invoice)
                            <a href="{{ route('invoices.show', $invoice) }}" class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-4 hover:border-sky-300 dark:border-gray-700 dark:hover:border-sky-500">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $invoice->number }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $invoice->client->name }} · due {{ $invoice->due_date->format('M j, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ \App\Support\Money::formatMinor($invoice->total_amount, $invoice->currency) }}</p>
                                    <p class="text-sm capitalize text-gray-500 dark:text-gray-400">{{ $invoice->status->value }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-lg border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                No invoices yet. Create your first invoice to start tracking revenue.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Payments</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manual payment records tied to invoices.</p>
                        </div>
                        <a href="{{ route('payments.index') }}" class="text-sm font-medium text-sky-600 hover:text-sky-500">
                            View all
                        </a>
                    </div>

                    <div class="mt-6 space-y-3">
                        @forelse ($recentPayments as $payment)
                            <div class="rounded-lg border border-gray-200 px-4 py-4 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $payment->invoice->number }}</p>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ \App\Support\Money::formatMinor($payment->amount, $payment->invoice->currency) }}</p>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $payment->invoice->client->name }} · {{ $payment->paid_at->format('M j, Y') }}
                                </p>
                            </div>
                        @empty
                            <div class="rounded-lg border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                No payments recorded yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
