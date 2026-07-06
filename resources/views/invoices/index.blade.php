<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Invoices</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track totals, status, due dates, and payments.</p>
            </div>
            <a href="{{ route('invoices.create') }}" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">
                New Invoice
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl bg-white shadow-sm dark:bg-gray-800">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($invoices as $invoice)
                        <div class="flex flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between">
                            <div>
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-lg font-semibold text-gray-900 hover:text-sky-600 dark:text-gray-100 dark:hover:text-sky-400">
                                    {{ $invoice->number }}
                                </a>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $invoice->client->name }} · due {{ $invoice->due_date->format('M j, Y') }}
                                </p>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ \App\Support\Money::formatMinor($invoice->total_amount, $invoice->currency) }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Balance {{ \App\Support\Money::formatMinor($invoice->balance_due_amount, $invoice->currency) }}</p>
                                </div>
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                    {{ $invoice->status->value }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-16 text-center text-sm text-gray-500 dark:text-gray-400">
                            No invoices yet. Start by creating one for an existing client.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
