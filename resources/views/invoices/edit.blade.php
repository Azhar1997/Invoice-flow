<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit {{ $invoice->number }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800">
                <form method="POST" action="{{ route('invoices.update', $invoice) }}">
                    @method('PUT')
                    @include('invoices._form', [
                        'submitLabel' => 'Save Invoice',
                        'cancelRoute' => route('invoices.show', $invoice),
                    ])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
