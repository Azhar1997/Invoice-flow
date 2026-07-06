<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Create Invoice</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if ($clients->isEmpty())
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
                    Create a client first before issuing an invoice.
                    <a href="{{ route('clients.create') }}" class="font-medium underline">Add client</a>
                </div>
            @else
                <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <form method="POST" action="{{ route('invoices.store') }}">
                        @include('invoices._form', [
                            'submitLabel' => 'Create Invoice',
                            'cancelRoute' => route('invoices.index'),
                        ])
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
