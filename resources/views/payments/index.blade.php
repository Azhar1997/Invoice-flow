<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Payments</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">A running log of every recorded payment.</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl bg-white shadow-sm dark:bg-gray-800">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($payments as $payment)
                        <div class="flex flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between">
                            <div>
                                <a href="{{ route('invoices.show', $payment->invoice) }}" class="text-lg font-semibold text-gray-900 hover:text-sky-600 dark:text-gray-100 dark:hover:text-sky-400">
                                    {{ $payment->invoice->number }}
                                </a>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $payment->invoice->client->name }} · {{ $payment->paid_at->format('M j, Y') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ \App\Support\Money::formatMinor($payment->amount, $payment->invoice->currency) }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->method ?: 'Manual payment' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-16 text-center text-sm text-gray-500 dark:text-gray-400">
                            No payments recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
