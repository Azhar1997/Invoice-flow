<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Services\InvoiceCalculator;
use App\Services\InvoiceNumberGenerator;
use App\Services\InvoiceStatusManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceCalculator $calculator,
        private readonly InvoiceNumberGenerator $numberGenerator,
        private readonly InvoiceStatusManager $statusManager,
    ) {
    }

    public function index(): View
    {
        $this->authorize('viewAny', Invoice::class);

        $invoices = auth()->user()
            ->invoices()
            ->with(['client', 'payments'])
            ->latest()
            ->paginate(10);

        $invoices->getCollection()->transform(fn (Invoice $invoice) => $this->statusManager->sync($invoice));

        return view('invoices.index', [
            'invoices' => $invoices,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Invoice::class);

        return view('invoices.create', [
            'clients' => auth()->user()->clients()->orderBy('name')->get(),
            'itemRows' => old('items', [
                ['description' => '', 'quantity' => '1', 'unit_price' => '0.00'],
                ['description' => '', 'quantity' => '1', 'unit_price' => '0.00'],
                ['description' => '', 'quantity' => '1', 'unit_price' => '0.00'],
            ]),
        ]);
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $this->authorize('create', Invoice::class);

        $validated = $request->validated();
        $calculated = $this->calculator->calculate(
            $validated['items'],
            (float) ($validated['tax_rate'] ?? 0),
            (float) ($validated['discount_rate'] ?? 0),
        );

        $invoice = DB::transaction(function () use ($validated, $calculated): Invoice {
            $invoice = auth()->user()->invoices()->create([
                'client_id' => $validated['client_id'],
                'public_id' => (string) Str::uuid(),
                'number' => $this->numberGenerator->nextForUser(auth()->user()),
                'currency' => strtoupper($validated['currency']),
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'discount_rate' => $validated['discount_rate'] ?? 0,
                'subtotal_amount' => $calculated['subtotal_amount'],
                'tax_amount' => $calculated['tax_amount'],
                'discount_amount' => $calculated['discount_amount'],
                'total_amount' => $calculated['total_amount'],
                'balance_due_amount' => $calculated['balance_due_amount'],
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            $invoice->items()->createMany($calculated['items']);
            $invoice->load('items');

            return $this->statusManager->sync($invoice);
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('status', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $invoice->load(['client', 'items', 'payments']);
        $this->statusManager->sync($invoice);

        return view('invoices.show', [
            'invoice' => $invoice,
        ]);
    }

    public function edit(Invoice $invoice): View
    {
        $this->authorize('update', $invoice);

        $invoice->load('items');

        return view('invoices.edit', [
            'invoice' => $invoice,
            'clients' => auth()->user()->clients()->orderBy('name')->get(),
            'itemRows' => old('items', $invoice->items->map(fn ($item): array => [
                'description' => $item->description,
                'quantity' => (string) $item->quantity,
                'unit_price' => number_format($item->unit_price_amount / 100, 2, '.', ''),
            ])->all()),
        ]);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        $validated = $request->validated();
        $calculated = $this->calculator->calculate(
            $validated['items'],
            (float) ($validated['tax_rate'] ?? 0),
            (float) ($validated['discount_rate'] ?? 0),
            $invoice->amount_paid,
        );

        DB::transaction(function () use ($invoice, $validated, $calculated): void {
            $invoice->update([
                'client_id' => $validated['client_id'],
                'currency' => strtoupper($validated['currency']),
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'discount_rate' => $validated['discount_rate'] ?? 0,
                'subtotal_amount' => $calculated['subtotal_amount'],
                'tax_amount' => $calculated['tax_amount'],
                'discount_amount' => $calculated['discount_amount'],
                'total_amount' => $calculated['total_amount'],
                'balance_due_amount' => $calculated['balance_due_amount'],
                'notes' => $validated['notes'] ?? null,
                'terms' => $validated['terms'] ?? null,
            ]);

            $invoice->items()->delete();
            $invoice->items()->createMany($calculated['items']);
            $invoice->load('items');

            $this->statusManager->sync($invoice);
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('status', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorize('delete', $invoice);

        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('status', 'Invoice deleted successfully.');
    }
}
