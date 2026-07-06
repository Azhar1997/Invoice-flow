<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Clients</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your customer list and billing details.</p>
            </div>
            <a href="{{ route('clients.create') }}" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">
                New Client
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl bg-white shadow-sm dark:bg-gray-800">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($clients as $client)
                        <div class="flex flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between">
                            <div>
                                <a href="{{ route('clients.show', $client) }}" class="text-lg font-semibold text-gray-900 hover:text-sky-600 dark:text-gray-100 dark:hover:text-sky-400">
                                    {{ $client->name }}
                                </a>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $client->company_name ?: 'Independent client' }}
                                    @if ($client->email)
                                        · {{ $client->email }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-6 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $client->invoices_count }} invoices</span>
                                <a href="{{ route('clients.edit', $client) }}" class="font-medium text-sky-600 hover:text-sky-500">Edit</a>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-16 text-center text-sm text-gray-500 dark:text-gray-400">
                            No clients yet. Add your first client to start invoicing.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6">
                {{ $clients->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
