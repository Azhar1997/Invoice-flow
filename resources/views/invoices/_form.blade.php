@csrf

<div class="grid gap-6 md:grid-cols-2">
    <div>
        <label for="client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client</label>
        <select id="client_id" name="client_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
            <option value="">Select a client</option>
            @foreach ($clients as $clientOption)
                <option value="{{ $clientOption->id }}" @selected((string) old('client_id', $invoice->client_id ?? '') === (string) $clientOption->id)>
                    {{ $clientOption->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Currency</label>
        <input id="currency" name="currency" type="text" maxlength="3" value="{{ old('currency', $invoice->currency ?? 'EUR') }}" class="mt-1 block w-full rounded-lg border-gray-300 uppercase dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
    </div>
    <div>
        <label for="issue_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Issue Date</label>
        <input id="issue_date" name="issue_date" type="date" value="{{ old('issue_date', isset($invoice) ? $invoice->issue_date->toDateString() : now()->toDateString()) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
    </div>
    <div>
        <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
        <input id="due_date" name="due_date" type="date" value="{{ old('due_date', isset($invoice) ? $invoice->due_date->toDateString() : now()->addDays(14)->toDateString()) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
    </div>
    <div>
        <label for="tax_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tax Rate (%)</label>
        <input id="tax_rate" name="tax_rate" type="number" step="0.01" min="0" max="100" value="{{ old('tax_rate', $invoice->tax_rate ?? 0) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
    </div>
    <div>
        <label for="discount_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Discount Rate (%)</label>
        <input id="discount_rate" name="discount_rate" type="number" step="0.01" min="0" max="100" value="{{ old('discount_rate', $invoice->discount_rate ?? 0) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
    </div>
</div>

<div class="mt-8">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Line Items</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Add as many rows as you need later; this MVP starts with a few editable rows.</p>
    </div>

    <div class="mt-4 space-y-4">
        @foreach ($itemRows as $index => $itemRow)
            <div class="grid gap-4 rounded-lg border border-gray-200 p-4 md:grid-cols-[1.6fr_0.6fr_0.8fr] dark:border-gray-700">
                <div>
                    <label for="items_{{ $index }}_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <input id="items_{{ $index }}_description" name="items[{{ $index }}][description]" type="text" value="{{ old("items.$index.description", $itemRow['description'] ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                </div>
                <div>
                    <label for="items_{{ $index }}_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                    <input id="items_{{ $index }}_quantity" name="items[{{ $index }}][quantity]" type="number" step="0.01" min="0.01" value="{{ old("items.$index.quantity", $itemRow['quantity'] ?? '1') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                </div>
                <div>
                    <label for="items_{{ $index }}_unit_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price</label>
                    <input id="items_{{ $index }}_unit_price" name="items[{{ $index }}][unit_price]" type="number" step="0.01" min="0" value="{{ old("items.$index.unit_price", $itemRow['unit_price'] ?? '0.00') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" required>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="mt-8">
    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('notes', $invoice->notes ?? '') }}</textarea>
</div>

<div class="mt-6">
    <label for="terms" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Terms</label>
    <textarea id="terms" name="terms" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('terms', $invoice->terms ?? '') }}</textarea>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ $cancelRoute }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
        Cancel
    </a>
    <button type="submit" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">
        {{ $submitLabel }}
    </button>
</div>
