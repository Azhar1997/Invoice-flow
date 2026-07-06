<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceCalculator;
use App\Services\InvoiceNumberGenerator;
use App\Services\InvoiceStatusManager;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
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

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Invoice::class);

        $invoices = auth()->user()
            ->invoices()
            ->with(['client', 'items', 'payments'])
            ->latest()
            ->paginate(10);

        $invoices->getCollection()->transform(fn (Invoice $invoice) => $this->statusManager->sync($invoice));

        return InvoiceResource::collection($invoices);
    }

    public function store(StoreInvoiceRequest $request): InvoiceResource
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
            $invoice->load(['client', 'items', 'payments']);

            return $this->statusManager->sync($invoice);
        });

        return InvoiceResource::make($invoice);
    }

    public function show(Invoice $invoice): InvoiceResource
    {
        $this->authorize('view', $invoice);

        $invoice->load(['client', 'items', 'payments']);
        $this->statusManager->sync($invoice);

        return InvoiceResource::make($invoice);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): InvoiceResource
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
            $invoice->load(['client', 'items', 'payments']);

            $this->statusManager->sync($invoice);
        });

        return InvoiceResource::make($invoice);
    }

    public function destroy(Invoice $invoice): Response
    {
        $this->authorize('delete', $invoice);

        $invoice->delete();

        return response()->noContent();
    }
}
