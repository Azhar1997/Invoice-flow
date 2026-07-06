@csrf

<div class="grid gap-6 md:grid-cols-2">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $client->name ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
    </div>
    <div>
        <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company</label>
        <input id="company_name" name="company_name" type="text" value="{{ old('company_name', $client->company_name ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
    </div>
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $client->email ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
    </div>
    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', $client->phone ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
    </div>
    <div>
        <label for="billing_address_line_1" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address Line 1</label>
        <input id="billing_address_line_1" name="billing_address_line_1" type="text" value="{{ old('billing_address_line_1', $client->billing_address_line_1 ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
    </div>
    <div>
        <label for="billing_address_line_2" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address Line 2</label>
        <input id="billing_address_line_2" name="billing_address_line_2" type="text" value="{{ old('billing_address_line_2', $client->billing_address_line_2 ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
    </div>
    <div>
        <label for="billing_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
        <input id="billing_city" name="billing_city" type="text" value="{{ old('billing_city', $client->billing_city ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
    </div>
    <div>
        <label for="billing_state" class="block text-sm font-medium text-gray-700 dark:text-gray-300">State / Region</label>
        <input id="billing_state" name="billing_state" type="text" value="{{ old('billing_state', $client->billing_state ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
    </div>
    <div>
        <label for="billing_postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Postal Code</label>
        <input id="billing_postal_code" name="billing_postal_code" type="text" value="{{ old('billing_postal_code', $client->billing_postal_code ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
    </div>
    <div>
        <label for="billing_country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
        <input id="billing_country" name="billing_country" type="text" value="{{ old('billing_country', $client->billing_country ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
    </div>
</div>

<div class="mt-6">
    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
    <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('notes', $client->notes ?? '') }}</textarea>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ $cancelRoute }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
        Cancel
    </a>
    <button type="submit" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">
        {{ $submitLabel }}
    </button>
</div>
