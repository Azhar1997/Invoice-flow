<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $client->name }}</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $client->company_name ?: 'Independent client' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('clients.edit', $client) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                    Edit
                </a>
                <form method="POST" action="{{ route('clients.destroy', $client) }}">
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
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Contact Details</h3>
                <dl class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300">
                    <div>
                        <dt class="font-medium text-gray-900 dark:text-gray-100">Email</dt>
                        <dd>{{ $client->email ?: 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-900 dark:text-gray-100">Phone</dt>
                        <dd>{{ $client->phone ?: 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-900 dark:text-gray-100">Billing Address</dt>
                        <dd>
                            {{ $client->billing_address_line_1 ?: 'No address saved' }}
                            @if ($client->billing_address_line_2)
                                <br>{{ $client->billing_address_line_2 }}
                            @endif
                            @if ($client->billing_city || $client->billing_state || $client->billing_postal_code)
                                <br>{{ collect([$client->billing_city, $client->billing_state, $client->billing_postal_code])->filter()->join(', ') }}
                            @endif
                            @if ($client->billing_country)
                                <br>{{ $client->billing_country }}
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-900 dark:text-gray-100">Notes</dt>
                        <dd>{{ $client->notes ?: 'No notes yet.' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Invoices</h3>
                    <a href="{{ route('invoices.create') }}" class="text-sm font-medium text-sky-600 hover:text-sky-500">Create invoice</a>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($recentInvoices as $invoice)
                        <a href="{{ route('invoices.show', $invoice) }}" class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-4 hover:border-sky-300 dark:border-gray-700 dark:hover:border-sky-500">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $invoice->number }}</p>
                                <p class="text-sm capitalize text-gray-500 dark:text-gray-400">{{ $invoice->status->value }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ \App\Support\Money::formatMinor($invoice->total_amount, $invoice->currency) }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Due {{ $invoice->due_date->format('M j, Y') }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                            This client does not have any invoices yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
